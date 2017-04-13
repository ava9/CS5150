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

require_once "config.php";

/************************************************************/
/******************* DEBUG FUNCTIONS ************************/
/************************************************************/



function DEBUG_ECHO($val) {
  $DEBUG_ON = false;
  if ($DEBUG_ON) {
    echo $val;
    flush();
  }
}

/************************************************************/
/******************* DATABASE SQL QUERIES *******************/
/************************************************************/

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($conn->connect_errno) {
    printf("Connect failed: %s\n", $conn->connect_error);
    exit();
}

#Ithaca PorchFest, PorchfestID = 1

$sqlBandsTimeSlots = "SELECT ats.BandID, ats.TimeslotID FROM bandavailabletimes ats, bandstoporchfests bp WHERE ats.BandID = bp.BandID AND bp.PorchfestID = 1";
$resultBandsTimeSlots = $conn->query($sqlBandsTimeSlots);
if (!$resultBandsTimeSlots) { #for available time slots
    printf("Get Bands-Timeslots failed\n");
    printf("Connect failed: %s\n", $conn->error);
    exit();
}

$sqlBandsPorchfests = "SELECT bp.BandID FROM bandstoporchfests bp WHERE bp.PorchfestID = 1";
$resultBandsPorchfests = $conn->query($sqlBandsPorchfests);
if (!$resultBandsPorchfests) { #for bands in this porchfest
    printf("Get Bands-Porchfests failed\n");
    exit();
}

$sqlTimeSlotsPorchfests = "SELECT ts.TimeslotID FROM porchfesttimeslots ts WHERE ts.PorchfestID = 1";
$resultTimeslotsPorchfests = $conn->query($sqlTimeSlotsPorchfests);
if (!$resultTimeslotsPorchfests) { #to see all timeslots available for a porchfest
    printf("Get Porchfest-Timeslots failed\n");
    exit();
}

$sqlBands = "SELECT b.BandID, b.Name, bp.PorchLocation, b.Conflicts, bp.Latitude, bp.Longitude FROM bands b, bandstoporchfests bp WHERE b.BandID = bp.BandID AND bp.PorchfestID = 1";
$resultBands = $conn->query($sqlBands);
if (!$resultBands) { #for band id, porch location and conflicts
    printf("Get Bands-Porchfests failed\n");
    exit();
}


/************************************************************/
/******************* GLOBAL VARIABLES ***********************/
/************************************************************/

$NUM_SCHEDS_TO_GENERATE = 10;
$MIN_DISTANCE = 25; // minimum distance in meters allowed between playing bands
$kNeighbors = 10; //how many nearest neighbors used to calculate distance variance

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

// Returns random lat lng coordinates in the ithca area
function getRandomCoordinates($address){
  $lat = 42 + (rand(0,10000)/10000000);
  $lng = -76 - (rand(0,10000)/10000000);
  return array($lat, $lng);
}


/************************************************************/
/******************* BAND AND SCHEDULE CLASSES **************/
/************************************************************/

class Band {
	
  function __construct($ID, $name, $lat, $lng, $availableTimeSlots, $conflicts, $slot, $distances) {
    //Band(int ID, float lat, float lng, String name, int[] conflicts, bool[] availableTimeSlots)
    $this->id = $ID; //int
    $this->name = $name; //String      # say, "The Amazing Crooners"
    $this->lat = $lat; //float        # say, 42.450962
    $this->lng = $lng; //float        # say, -76.501122
    $this->availableTimeSlots = $availableTimeSlots; //<slotID, boolean> map   # say, [10: true, 21: true, 30: false, 13: true ]
    $this->conflicts = $conflicts;  // int[]   # say, [ 11111 ] band IDs that we conflict with
    $this->slot = $slot; //int         # initially -1 until assigned
    $this->distances = $distances; #HashMap<int bandID, int d> 
  }
  
  /* Takes a bandID and calculates the distance between this band object and the band corresponding to bandID */
  function getDistance($bandID)
  {
    global $bandsHashMap; //HashMap<int id, Band band>

    if (!array_key_exists($bandID, $this->distances)) {
      $b = $bandsHashMap[$bandID]; //Band object
      $this->distances[$bandID] = tdistance($this->lat, $this->lng, $b->lat, $b->lng);
    }
    return $this->distances[$bandID];
  }

  /* Takes a schedule object and calculates the k (specified in the global variables section
  		nearest bands to this band object. Returns an array of size k of bandIDs */
  function calculateKNearest($sched, $kNeighbors) {

    $bands = $sched->getBandsAtSlot($this->slot); //Band[] 
    $this->sortByDistance($bands);
    $result = [];
    for ($i = 0; $i < $kNeighbors; $i++) { # get the nearest k IDs
      array_push($result, $bands[$i]->id);
    }
    return $result;
  }
  
  /* Sorts the keys of the bands at the same time slot as this band object by distance to this band object */
  function sortByDistance($bands) {
    $distances = [];
    for ($i = 0; $i < sizeof($bands); $i++) {
      $distances[$this->getDistance($bands[$i]->id)] = $bands[$i];
    }
    ksort($distances);
    $bands = array_values($distances);
  }
  
  /* Getter function that returns a list of all bands that conflict with this band object */
  function getConflicts(){
    return $this->conflicts;
  }
  
  /* Overwrites the php clone function to create a new Band object with the same values as this band object */
  function __clone() {
    return new Band($this->id, $this->name, $this->lat, $this->lng, $this->availableTimeSlots, $this->conflicts, $this->slot, $this->distances);
  }

}
  
class Schedule {
//  $schedule; //HashMap<int timeslot, Band[] bands> 
//  $timeSlotVariances; //float[] of the variance at each time slot as calculated in computeVariance()
  
  function __construct($timeSlotsArr) {
    $this->schedule = [];
    foreach ($timeSlotsArr as $timeSlotID) {
      $this->schedule[$timeSlotID] = [];
    }
    $this->timeSlotVariances = [];
    $this->score = -1;
  }
  
  /* Creates a copy of the schedule with score reinitialized to -1 */
  function deepCopy($timeSlotsArr) {
    $copy = new Schedule($timeSlotsArr);
    $copy->schedule = [];
    foreach ($this->schedule as $timeslot => $bandArr) {
      $tmp = [];
      foreach ($bandArr as $band) {
         array_push($tmp, clone $band);
      }
      $copy->schedule[$timeslot] = $tmp;
    }
    $copy->timeSlotVariances = $this->timeSlotVariances;
    $copy->score = -1;
    return $copy;
  }
  
  /* Iterates through the timeSlotVariances and find the timeslot with the
  best/lowest variance. Try to swap with a band in there */
  function randomSwap($band) {
    global $timeslotsPorchfests; //array of all timeslots available for a particular porchfest
    
    $worstTimeSlot = $band->slot;
    
    //get timeslot with min variance
    $minVariance = PHP_INT_MAX;
    ksort($this->timeSlotVariances);
    $flag = false;
    for ($i = 0; $i < sizeof($this->timeSlotVariances); $i++){
      if (!$flag){
        $timeslotID = $timeslotsPorchfests[$i];
        if (noConflicts($this->schedule[$timeslotID], $band)){
          shuffle($this->schedule[$timeslotID]);
          foreach ($this->schedule[$timeslotID] as $possibleBandToSwap) {
            if (noConflicts($this->schedule[$worstTimeSlot], $possibleBandToSwap) && 
              bandOverMinDist($this->schedule[$worstTimeSlot], $possibleBandToSwap)){

              $this->delete($timeslotID, $possibleBandToSwap);
              $this->delete($worstTimeSlot, $band);
              $this->add($worstTimeSlot, $possibleBandToSwap);
              $this->add($timeslotID, $band);
              $possibleBandToSwap->slot = $worstTimeSlot;
              $band->slot = $timeslotID;
              $flag = true;
              break;
            }
          }
        }
      }
    }
    score($this);
  }
  
  /* Takes in band and time slot. Adds band to timeslot in this schedule */
  function add($slot, $band){
    array_push($this->schedule[$slot], $band);
  }
  
  /* Takes in band and time slot. Deletes band from timeslot in this schedule */
  function delete($slot, $band){
    for ($i = 0; $i < sizeof($this->schedule[$slot]); $i++) {
      $b = $this->schedule[$slot][$i];
      if ($b->id == $band->id) {
        unset($this->schedule[$slot][$i]);
        break;
      }
    }
    $this->schedule[$slot] = array_merge($this->schedule[$slot]);
  }
  
  /* Returns a list of all bands scheduled at specified time slot in this schedule */
  function getBandsAtSlot($timeSlot){
    return $this->schedule[$timeSlot];
  }
  
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
      while ($i < $totalNumTimeSlots){ # round robin through all time slots and bands
        $slot = (($currentTimeSlot + $i) % $totalNumTimeSlots);
        $slotID = $timeslotsPorchfests[$slot];

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
        else{
          $i++;
        }
      }
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

    foreach ($uBand->getConflicts() as $conflictingBandName) {
      $band = $bandsHashMap[$conflictingBandName];
      $oldTimeSlot = $band->slot;
      $success = tryToMoveBand($conflictingBandName, $schedule);
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

/************************************************************/
/******************* HELPER FUNCTIONS ***********************/
/************************************************************/
        
/* calculates standard deviation of an array */
function stats_standard_deviation(array $a, $sample = false) {
    $n = count($a);
    if ($n === 0) {
      trigger_error("The array has zero elements", E_USER_WARNING);
      return false;
    }
    if ($sample && $n === 1) {
      trigger_error("The array has only 1 element", E_USER_WARNING);
      return false;
    }
    $mean = array_sum($a) / $n;
    $carry = 0.0;
    foreach ($a as $val) {
      $d = ((double) $val) - $mean;
      $carry += $d * $d;
    };
    if ($sample) {
       --$n;
    }
    return sqrt($carry / $n);
}

/* calculates average distance of k nearest neighbors where k is an
adjustable amount of nearest neighbors to calculate */
function computeVariance($slot, $sched){
  global $kNeighbors; //how many nearest neighbors used to calculate distance variance
  // $kNeighbors = sizeof($sched->getBandsAtSlot($slot));
  
  $knnData = [];
  foreach ($sched->getBandsAtSlot($slot) as $band) {
    $knearest = $band->calculateKNearest($sched, $kNeighbors);
    $avg = 0;
    foreach ($knearest as $nearestBand){
      $avg = $avg + $band->distances[$nearestBand];
    }
    array_push($knnData, ($avg/$kNeighbors)) ;
    # gets average distance from knn for each band at each time slot
  }
  # calculate variance
  $tmp = stats_standard_deviation($knnData);
  $sched->timeSlotVariances[$slot] = $tmp * $tmp;
  return $sched->timeSlotVariances[$slot];
}
  
/* moves band to a different timeslot and returns true on success, false otherwise */
function tryToMoveBand($id, $schedule) {
  global $bandsHashMap;
  global $totalNumTimeSlots;
  global $timeslotsPorchfests;
  global $availableTimeSlots;

  $band = $bandsHashMap[$id];
  for ($slot = 0; $slot < $totalNumTimeSlots; $slot++) {
    $slotID = $timeslotsPorchfests[$slot];
    if ($slotID == $band->slot) {
      continue;
    }
    if ( $band->availableTimeSlots[$slotID] && noConflicts($schedule[$slotID], $band) ) {
        $schedule->delete($band->slot, $band);
        $schedule->add($slotID, $band);
        $band->slot = $slotID;
        return true;
    }
  }
  return false;
}

function bandOverMinDist($bandsArr, $band) {
  global $MIN_DISTANCE;

  if (sizeof($bandsArr) == 0) {
    return true;
  }

  foreach ($bandsArr as $b){
    if ($band->getDistance($b->id) <= $MIN_DISTANCE) {
      return false;
    }
  }

  return true;

}

/* returns true if $band does not conflict with any band in $bandArr, false otherwise */
function noConflicts($bandsArr, $band){
  global $MIN_DISTANCE;

  if (sizeof($bandsArr) == 0) {
    return true;
  }

  $conflicts = $band->getConflicts();

  if ($conflicts[0] == null || sizeof($conflicts) == 0) {
    return true;
  }

  foreach ($bandsArr as $b){
    if (in_array($b->name, $conflicts)) {
      return false;
    }
  }
  return true;
}

/* calculates the distance between two longitude/latitude coordinates in meters */
function tdistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  $distance = $angle * $earthRadius;
  return $distance;
}
  
/* create schedule then repeat */
function run(){
  global $conn;
  global $NUM_SCHEDS_TO_GENERATE;
  $result = null;
  
  for ($i = 0; $i < $NUM_SCHEDS_TO_GENERATE; $i++){
    echo "running schedule " . $i . "\n";
    $tmpAndSuccess = generateBaseSchedule($tmp);
    $tmp = $tmpAndSuccess[0];
    $success = $tmpAndSuccess[1];

    if (!$success) {
      echo "failed to generate base schedule\n";
      continue;
    }
    $noImprovements = 0;
    while (true){
      $imp = improve($tmp);
      $tmp = $imp[1];
      $noImprovements = ($imp[0] ? 0 : $noImprovements + 1);
      if ($noImprovements === 100){
       DEBUG_ECHO("no more improvements\n");
        break;
      }
    }
    if ($result === null || $tmp->score < $result->score){
      $result = $tmp;
      echo "improved with score " . $result->score . "\n";
    }
  }
  
  #******* POST PROCESS *******
  # write back to the database with the band's assigned timeslot
  # $result.updateDB();
  $finalSched = $result->schedule;
  foreach ($finalSched as $ts => $bands) {
    foreach ($bands as $b) {
      $id = $b->id;
      $sql = "UPDATE bandstoporchfests SET TimeslotID = " . $ts . " WHERE BandID = " . $id . " AND PorchfestID = 1";
      if ($conn->query($sql) === false) {
        DEBUG_ECHO("Error: " . $sql . "<br>" . $conn->error . "\n");
      }
    }
  }
  echo "Success!";
}

# (∩｀-´)⊃━☆ﾟ.*･｡ﾟ magic time #
run();

?>