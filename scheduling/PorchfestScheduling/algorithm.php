<?php
# TODO:
  # TimeSlotID
  # Conflicts by ID not name
  # Visualization that the schedule is feasible (no conflicts) - graph
    # 7 views
    # 6 for each timeslot 1 general
  # Latitude Longitude!!!
  
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

/* converts query result to an array */
function getQueryArr($queryResult) {
  $result = [];
  while ($row = $queryResult->fetch_array(MYSQLI_NUM)) {
    array_push($result, $row);
  }
  return $result;
}

# pull data from DB
# for each band we'll need: available time slots, address, conflicts
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

function createBandObjects(){
  DEBUG_ECHO("creating band objects\n");
  global $resultBands;
  global $bandsHashMap; //HashMap<int id, Band band> 
  global $bandsTimeSlots; //HashMap<BandID, TimeslotID>
  global $totalNumTimeSlots; //total number of timeslots for a porchfest
  global $timeslotsPorchfests; //array of all timeslots available for a particular porchfest
  
  $tmp = getQueryArr($resultBands);
  for ($i = 0; $i < sizeof($tmp); $i++){
    flush();
    $bandId = $tmp[$i][0];
    $bandName = $tmp[$i][1];
    $bandLocation = $tmp[$i][2];
    $bandConflictsString = $tmp[$i][3];
    $bandLatLng = array("lat" => $tmp[$i][4], "lng" => $tmp[$i][5]);
        
    // convert the string of conflicts into an array of conflicts
    $bandConflicts = explode(',', $bandConflictsString);
    if (sizeof($bandConflicts[0]) == 0) {
      $bandConflicts = [];
    }
    $availableTimeSlots = [];
    
    for ($j = 0; $j < $totalNumTimeSlots; $j++) {
      $slotID = $timeslotsPorchfests[$j];
      $availableTimeSlots[$slotID] = intVal(false);
    }
    foreach ($bandsTimeSlots[$bandId] as $canDoSlotID) {
      $availableTimeSlots[$canDoSlotID] = intVal(true);
    }
    $bandsHashMap[$bandId] = new Band($bandId, $bandName, $bandLatLng["lat"], $bandLatLng["lng"], $availableTimeSlots, $bandConflicts, -1, []);
  }
  DEBUG_ECHO("created all band objects\n");
  return $bandsHashMap;
}

# populate the numberOfTimeSlots hashmap
function populateBandsWithXTimeSlots() {
  global $bandsWithXTimeSlots; //HashMap<int numberOfTimeSlots, int[] bandIds> max number of time slots a band can play in
  global $bandsPorchfests; //array of all bands that can play at a particular porchfest
  global $bandsTimeSlots; //HashMap<BandID, TimeslotID>
  global $totalNumTimeSlots; //total number of timeslots for a porchfest
  global $bandsHashMap; //HashMap<int id, Band band> 
  
  DEBUG_ECHO("populating bandsWithXTimeSlots\n");
  for ($i = 1; $i <= $totalNumTimeSlots; $i++) {
    $bandsWithXTimeSlots[$i] = [];
  }
  
  foreach ($bandsPorchfests as $bandID) {
    // assuming bandsTimeSlots is a hashmap of our DB, we count the number of available time slots a band has
    $availTimeSlots = sizeof($bandsTimeSlots[$bandID]);
    if ($bandsWithXTimeSlots[$availTimeSlots] === null) {
      $bandsWithXTimeSlots[$availTimeSlots] = [];
    }
    array_push($bandsWithXTimeSlots[$availTimeSlots], $bandID);
  }
  ksort($bandsWithXTimeSlots);
  DEBUG_ECHO("populated\n");
}

/************************************************************/
/*********************** ALGORITHM **************************/
/************************************************************/

/* Generates initial schedule with no conflicts */
function generateBaseSchedule() {
  global $bandsHashMap; //HashMap<int id, Band band> 
  global $bandsWithXTimeSlots; //HashMap<int numberOfTimeSlots, int[] bandIds> max number of time slots a band can play in
  global $totalNumTimeSlots; //total number of timeslots for a porchfest
  global $timeslotsPorchfests; //array of all timeslots available for a particular porchfest
  
  $success = true;

  DEBUG_ECHO("generating base schedule\n");
  $bandsHashMap = createBandObjects();
  populateBandsWithXTimeSlots();
  $schedule = new Schedule($timeslotsPorchfests);
  $unassignedBandIDs = []; // int[] 
  $currentTimeSlot = 0;
  
  # phase 1: place as many bands as possible
  DEBUG_ECHO("phase 1\n");
  foreach ($bandsWithXTimeSlots as $key => $bandIDs) {
    shuffle($bandIDs); 
      #randomly pick bands one at a time. 
      #we choose from the bands with the fewest available time slots first 
      #then go up from there.
    foreach ($bandIDs as $id) { 
      $band = $bandsHashMap[$id];
      $i = 0;
      $assigned = false;

            //sort the schedule to see which time slots have the fewest assigned bands
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
        $locationOK = bandOverMinDist($schedule->schedule[$slotID], $band);
        if ($isAvailable && $hasNoConflicts && $locationOK){
          # band can play at this time
          $schedule->add($slotID, $band);
          $currentTimeSlot = $slot + 1;
          $band->slot = $slotID;
          $assigned = true;
          break;
        }
        
      }
      
      //ROUND ROBIN
//      while ($i < $totalNumTimeSlots){ # round robin through all time slots and bands
//        $slot = (($currentTimeSlot + $i) % $totalNumTimeSlots);
//        $slotID = $timeslotsPorchfests[$slot];
//
//        $isAvailable = $band->availableTimeSlots[$slotID];
//        $hasNoConflicts = noConflicts($schedule->schedule[$slotID], $band);
//        $locationOK = bandOverMinDist($schedule->schedule[$slotID], $band);
//        if ($isAvailable && $hasNoConflicts && $locationOK){
//          # band can play at this time
//          $schedule->add($slotID, $band);
//          $currentTimeSlot = $slot + 1;
//          $band->slot = $slotID;
//          $assigned = true;
//          break;
//        }
//        else{
//          $i++;
//        }
//      }

      if (!$assigned) {
        DEBUG_ECHO("no available time slots for " . $id . "\n");
        array_push($unassignedBandIDs, $id); # will deal with these later...
      }
    }
  }

  # phase 2: deal with the bands that were unable to be assigned in phase 1
  DEBUG_ECHO("phase 2\n");
  foreach ($unassignedBandIDs as $uBandID) {
    $uBand = $bandsHashMap[$uBandID];

    // getConflicts returns string names...gotta get them as bandIDs
    $conflictingIDs = namesToIDs($uBand->getConflicts());
    foreach ($conflictingIDs as $conflictingBandID) {
      $band = $bandsHashMap[$conflictingBandID];
      $oldTimeSlot = $band->slot;
      $success = tryToMoveBand($conflictingBandID, $schedule);
      if ($success) {
        $schedule->add($oldTimeSlot, $uBand);
        $uBand->slot = $oldTimeSlot;
        break;
      }
    }
    if (!$success) {
      // die("this is actually impossible. exit with grace.");
    }
  }
  DEBUG_ECHO("generated schedule!\n");
  DEBUG_ECHO("scoring the schedule...\n");
  score($schedule);
  return array($schedule, $success);
}

/* assigns a score to a schedule based on variance
k is an adjustable amount of nearest neighbors to calculate */
function score($sched) {
  global $timeslotsPorchfests; //array of all timeslots available for a particular porchfest
  foreach ($timeslotsPorchfests as $slot){
    $variance = computeVariance($slot, $sched);
    DEBUG_ECHO($variance . "\n");
    if ($sched->score < $variance) {
      $sched->score = $variance;
    }
  }
}
    
/* pairwise swaps to improve the schedule. recomputes the
		variance of the two time slots that are affected by the swap */
function improve($sched) {
  global $timeslotsPorchfests; //array of all timeslots available for a particular porchfest
  arsort($sched->timeSlotVariances);
  $highestVarianceTimeSlot = array_keys($sched->timeSlotVariances)[0]; # index of highest variance corresponds to highestVarianceTimeSlot
  
  $bands = $sched->getBandsAtSlot($highestVarianceTimeSlot);
  $minDistance = PHP_INT_MAX;
  $bx; 
  $by;
  for ($i = 0; $i < sizeof($bands); $i++) {
    $bi = $bands[$i];
    if ($bi === null) {
      DEBUG_ECHO($i . "\n");
      DEBUG_ECHO($highestVarianceTimeSlot + "\n");
    }
    for ($j = 0; $j < $i; $j++){
      $bj = $bands[$j];
      $d = $bi->getDistance($bj->id);
      if ($d < $minDistance) {
        $bx = $bi;
        $by = $bj;
        $minDistance = $d;
      }
    }
  }      
  $br = (rand(0, 2) === 0 ? $bx : $by);
  $newSched = $sched->deepCopy($timeslotsPorchfests);
  $newSched->randomSwap($br); # update score in randomSwap()
  if ($sched == $newSched) {
    DEBUG_ECHO("didn't swap\n");
  }
  DEBUG_ECHO("old score " . $sched->score . "\n");
  DEBUG_ECHO("new score " . $newSched->score . "\n");
  if ($newSched->score < $sched->score) {
    return array(true, $newSched);
  }
  return array(false, $sched);
}


?>