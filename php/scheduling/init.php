<?php
# TODO:
  
# future ideas:
# order by conflicts, or by location clusters. Add this in later
# explicitly check if the variances between timeslots are roughly equivalent
  # In the section where we randomly swap between the worst time slot and any other,
  # it might make sense to swap with the best one instead
  
# ALGORITHM

/*
randomly create base schedule
score schedule, this is just the max variance over all timeslots

for bands in each time slot
1) get k nearest neighbors
2) calculate average distance from those neighbors and store
3) get variance of that entire timeslot
4) keep track of the largest variance
*/

/************************************************************/
/******************* INITIALIZATION FUNCTIONS ***************/
/************************************************************/

function initGlobals() {
  global $resultBandsTimeSlots;
  global $resultBandsPorchfests;
  global $resultTimeslotsPorchfests;
  global $resultBands;
  global $bandsTimeSlots;
  global $bandsPorchfests;
  global $timeslotsPorchfests;
  global $bandsHashMap;
  global $bandsWithXTimeSlots;
  global $totalNumTimeSlots;
  global $resultBandConflicts;
  global $bandConflicts;
  global $PorchfestID;

  /************************************************************/
  /******************* DATABASE SQL QUERIES *******************/
  /************************************************************/

  // Create connection
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if ($conn->connect_errno) {
    printf("Connect failed: %s\n", $conn->connect_error);
    exit();
  }

  $sqlBandsTimeSlots = "SELECT ats.BandID, ats.TimeslotID FROM bandavailabletimes ats, bandstoporchfests bp WHERE ats.BandID = bp.BandID AND bp.PorchfestID = " . $PorchfestID;
  $resultBandsTimeSlots = $conn->query($sqlBandsTimeSlots);
  if (!$resultBandsTimeSlots) { #for available time slots
    printf("Get Bands-Timeslots failed, with query " . $sqlBandsTimeSlots . "\n");
    printf("Connect failed: %s\n", $conn->error);
    exit();
  }

  $sqlBandsPorchfests = "SELECT bp.BandID FROM bandstoporchfests bp WHERE bp.PorchfestID = " . $PorchfestID;
  $resultBandsPorchfests = $conn->query($sqlBandsPorchfests);
  if (!$resultBandsPorchfests) { #for bands in this porchfest
    printf("Get Bands-Porchfests failed\n");
    exit();
  }

  $sqlTimeSlotsPorchfests = "SELECT ts.TimeslotID FROM porchfesttimeslots ts WHERE ts.PorchfestID = " . $PorchfestID;
  $resultTimeslotsPorchfests = $conn->query($sqlTimeSlotsPorchfests);
  if (!$resultTimeslotsPorchfests) { #to see all timeslots available for a porchfest
    printf("Get Porchfest-Timeslots failed\n");
    exit();
  }

  $sqlBands = "SELECT b.BandID, b.Name, bp.PorchLocation, bp.Latitude, bp.Longitude, bp.TimeslotID FROM bands b, bandstoporchfests bp WHERE b.BandID = bp.BandID AND bp.PorchfestID = " . $PorchfestID;
  $resultBands = $conn->query($sqlBands);
  if (!$resultBands) { #for band id, porch location
    printf("Get Bands-Porchfests failed\n");
    exit();
  }

  $sqlBandConflicts = "SELECT bc.BandID1, bc.BandID2 FROM bandconflicts bc, bandstoporchfests bp WHERE bc.BandID1 = bp.BandID AND bp.PorchfestID = "  . $PorchfestID;
  $resultBandConflicts = $conn->query($sqlBandConflicts);
  if (!$resultBandConflicts) { #for band conflicts
    printf("Get Bands-Conflicts failed\n");
    exit();
  }
  
  /************************************************************/
  /*********************** INITIALIZATION *********************/
  /************************************************************/

  $bandsTimeSlots = populateBandsTimeSlots(); //HashMap<BandID, TimeslotID>
  $bandsPorchfests = []; //array of all bands that can play at a particular porchfest
  while ($row = $resultBandsPorchfests->fetch_array(MYSQLI_NUM)) {
    array_push($bandsPorchfests, $row[0]);
  }
  $timeslotsPorchfests = []; //array of all timeslots available for a particular porchfest
  while ($row = $resultTimeslotsPorchfests->fetch_array(MYSQLI_NUM)) {
    array_push($timeslotsPorchfests, $row[0]);
  }
  $bandsHashMap; //HashMap<int id, Band band> 
  $bandsWithXTimeSlots = []; //HashMap<int numberOfTimeSlots, int[] bandIds> max number of time slots a band can play in
  $totalNumTimeSlots = sizeof($timeslotsPorchfests); //total number of timeslots for a porchfest
  $bandConflicts = populateBandConflicts();
}

/* 
 * Returns an array representing data from our database such that
 * an index into the array is the column name, and the data at that 
 * index is the row data.
 *
 * @param    $queryResult    A mysqli query result
 */
function getQueryArr($queryResult) {
  $result = [];
  while ($row = $queryResult->fetch_array(MYSQLI_NUM)) {
    array_push($result, $row);
  }
  return $result;
}

/*
 * Returns a hashmap ( int bandID => int[] timeslotID ). This represents
 * timeslots that a particular band is able to play at.
 */
function populateBandsTimeSlots(){
  global $resultBandsTimeSlots;
  $result = [];
  $tmp = getQueryArr($resultBandsTimeSlots);
  for ($i = 0; $i < sizeof($tmp); $i++) {
    $bandID = $tmp[$i][0];
    $timeslotID = $tmp[$i][1];
    if (array_key_exists($bandID, $result)) {
      array_push($result[$bandID], $timeslotID);
    } 
    else {
      $result[$bandID] = array($timeslotID); 
    }
  }
  return $result;
}

/*
 * Returns a hashmap that represents the other Bands that a particular band conflicts with.
 *
 * $result    hashmap ( int bandID => int[] bandIDs )
 */
function populateBandConflicts(){
    global $resultBandConflicts;
    $result = [];
    $tmp = getQueryArr($resultBandConflicts);
    for ($i = 0; $i < sizeof($tmp); $i++) {
        $bandID1 = $tmp[$i][0];
        $bandID2 = $tmp[$i][1];
        if (array_key_exists($bandID1, $result)) {
            array_push($result[$bandID1], $bandID2);
        }
        else {
            $result[$bandID1] = array($bandID2);
        }
    }
    return $result;
}

/*
 * Returns a populated version of global variable $bandsHashMap. 
 *
 * $bandsHashMap      hashmap ( int bandID => Band bandObject ) 
 */
function createBandObjects(
    $MIN_DISTANCE,
    $resultBands,
    $bandsTimeSlots,   
    $totalNumTimeSlots, 
    $timeslotsPorchfests,
    $bandConflicts        
  ){
  global $resultBands;
  global $bandsHashMap;

  DEBUG_ECHO("creating band objects\n");
  
  $tmp = getQueryArr($resultBands);
  $resultBands = $tmp;
  for ($i = 0; $i < sizeof($tmp); $i++){
    $bandId = $tmp[$i][0];
    $bandName = $tmp[$i][1];
    $bandLocation = $tmp[$i][2];
    $bandLatLng = array("lat" => $tmp[$i][3], "lng" => $tmp[$i][4]);
    $availableTimeSlots = [];
    
    for ($j = 0; $j < $totalNumTimeSlots; $j++) {
      $slotID = $timeslotsPorchfests[$j];
      $availableTimeSlots[$slotID] = intVal(false);
    }
    if (!array_key_exists($bandId, $bandsTimeSlots) || sizeof($bandsTimeSlots[$bandId]) == 0) {
      echo "***************************** no time slots for " . $bandId . " *****************************\n";
    } else {
      foreach ($bandsTimeSlots[$bandId] as $canDoSlotID) {
        $availableTimeSlots[$canDoSlotID] = intVal(true);
      }
    }

    if (array_key_exists($bandId, $bandConflicts)) {
      $conflicts = $bandConflicts[$bandId];
    }
    else {
        $conflicts = [];
    }

    $bandsHashMap[$bandId] = new Band($bandId, $bandName, $bandLatLng["lat"], $bandLatLng["lng"], $availableTimeSlots, $conflicts, -1, [], []);
  }

  // Treat violation of MIN_DISTANCE as distanceConflicts 
  foreach ($bandsHashMap as $bandID => $bandObj) {
    foreach ($bandsHashMap as $otherBandID => $otherBandObj) {
      if ($bandID == $otherBandID) {
        continue;
      }
      if ($bandObj->getDistance($otherBandID) < $MIN_DISTANCE) {
        array_push($bandObj->distanceConflicts, $otherBandID);
      }
    }
  }

  DEBUG_ECHO("created all band objects\n");
  return $bandsHashMap;
}

/*
 * Updates the global variable $bandsWithXTimeslots which orders our bands based on
 * the number of available timeslots they have.
 *
 * $bandsWithXTimeslots    hashmap ( int numberOfTimeslots => int[] bandIDs )
 */
function populateBandsWithXTimeSlots() {
  global $bandsWithXTimeSlots; // HashMap<int numberOfTimeSlots, int[] bandIds> max number of time slots a band can play in
  global $bandsPorchfests;     // array of all bands that can play at a particular porchfest
  global $bandsTimeSlots;      // HashMap<BandID, TimeslotID>
  global $totalNumTimeSlots;   // total number of timeslots for a porchfest
  global $bandsHashMap;        // HashMap<int id, Band band> 
  
  DEBUG_ECHO("populating bandsWithXTimeSlots\n");
  for ($i = 1; $i <= $totalNumTimeSlots; $i++) {
    $bandsWithXTimeSlots[$i] = [];
  }
  
  foreach ($bandsPorchfests as $bandID) {
    // assuming bandsTimeSlots is a hashmap of our DB, we count the number of available time slots a band has
    if (array_key_exists($bandID, $bandsTimeSlots)) {
      $availTimeSlots = sizeof($bandsTimeSlots[$bandID]);
      if ($bandsWithXTimeSlots[$availTimeSlots] === null) {
        $bandsWithXTimeSlots[$availTimeSlots] = [];
      }
      array_push($bandsWithXTimeSlots[$availTimeSlots], $bandID);
    }

  }
  ksort($bandsWithXTimeSlots);
  DEBUG_ECHO("populated\n");
}

/* 
 * Generates initial schedule with no conflicts 
 * 
 * Loops through every band and places them into timeslots 
 * until all bands are placed. Picks bands in order from least 
 * available timeslots to most available timeslots.
 * 
 * For each band, it places it into the timeslot with the fewest 
 * bands so far as long as it has no conflicts and does not violate
 * the minimum distance with any band scheduled in that slot. 
 */
function generateBaseSchedule() {
  global $bandsHashMap;             // HashMap<int id, Band band> 
  global $bandsWithXTimeSlots;      // HashMap<int numberOfTimeSlots, int[] bandIds> max number of time slots a band can play in
  global $totalNumTimeSlots;        // total number of timeslots for a porchfest
  global $timeslotsPorchfests;      // array of all timeslots available for a particular porchfest
  global $MIN_DISTANCE;
  global $resultBands;
  global $bandsTimeSlots;   
  global $bandConflicts;        
  
  DEBUG_ECHO("generating base schedule\n");
  $bandsHashMap = createBandObjects(
    $MIN_DISTANCE,
    $resultBands,
    $bandsTimeSlots,   
    $totalNumTimeSlots, 
    $timeslotsPorchfests,
    $bandConflicts        
    );
  populateBandsWithXTimeSlots();
  $schedule = new Schedule($timeslotsPorchfests);
  $unassignedBandIDs = [];          // int[] 
  
  // Phase 1: place as many bands as possible
  DEBUG_ECHO("phase 1\n");
  foreach ($bandsWithXTimeSlots as $key => $bandIDs) {
    // shuffle them to add randomness between iterations
    shuffle($bandIDs); 

    /*
     * 1) Randomly pick bands one at a time. 
     * 2) Choose from the bands with the fewest available time slots first 
     * 3) Go up from there.
     */
    foreach ($bandIDs as $id) { 
      $band = $bandsHashMap[$id];
      $i = 0;
      $assigned = false;

      // Sort the schedule to see which time slots have the fewest assigned bands
      uasort($schedule->schedule, function ($a, $b){
        if (sizeof($a) < sizeof($b)){
            return -1;
        }
        else if (sizeof($a) > sizeof($b)){
            return 1;
        }
        return 0;
      });
      
      for ($i = 0; $i < $totalNumTimeSlots; $i++){
        $slotID = array_keys($schedule->schedule)[$i];
        $isAvailable = $band->availableTimeSlots[$slotID];
        $hasNoConflicts = noConflicts($schedule->schedule[$slotID], $band);
        $locationOK = bandOverMinDist($schedule->schedule[$slotID], $band, $MIN_DISTANCE);
        if ($isAvailable && $hasNoConflicts && $locationOK){
          // band can play at this time
          $schedule->add($slotID, $band);
          $band->slot = $slotID;
          $assigned = true;
          break;
        }
        
      }

      if (!$assigned) {
        DEBUG_ECHO("no available time slots for " . $id . "\n");
        array_push($unassignedBandIDs, $id); # will deal with these later...
      }
    }
  }

  # Phase 2: deal with the bands that were unable to be assigned in phase 1
  DEBUG_ECHO("phase 2\n");
  foreach ($unassignedBandIDs as $uBandID) {
    $uBand = $bandsHashMap[$uBandID];
    $conflictingIDs = $uBand->getConflicts();
    $success = false;
    foreach ($conflictingIDs as $conflictingBandID) {
      DEBUG_ECHO("resolving conflict for " . $uBand->id . "\n");
      $band = $bandsHashMap[$conflictingBandID];
      $oldTimeSlot = $band->slot;
      $success = tryToMoveBand($conflictingBandID, $schedule);
      if ($success) {
        $schedule->add($oldTimeSlot, $uBand);
        $uBand->slot = $oldTimeSlot;
        DEBUG_ECHO("updating slot for band " . $id . "... moving it to " . $oldTimeSlot . "\n");
        break;
      }
    }
    if (!$success) {
      // die("this is actually impossible. exit with grace.");
      foreach ($uBand->availableTimeSlots as $timeslotID => $available) {
        if ($available && noConflicts($schedule->getBandsAtSlot($timeslotID), $uBand)) {
          $uBand->slot = $timeslotID;
          $schedule->add($timeslotID, $uBand);
          DEBUG_ECHO("no sensible slot to put this band in, so we place it in a nonconflicting slot hoping that our 
            algorithm will fix the min distance issue, so we place it in a nonconflicting slot hoping that our algorithm will 
            fix the min distance issue for band " . $uBand->id . "... moving it to " . $timeslotID . "\n");
          $success = true;
          break;
        }
      }
    }
  }
  DEBUG_ECHO("generated schedule!\n");
  DEBUG_ECHO("scoring the schedule...\n");
  $schedule->score();
  return $schedule;
}

?>