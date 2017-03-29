<?php
# future ideas:
# order by conflicts, or by location clusters. Add this in later
# explicitly check if the variances between timeslots are roughly equivalent
  # In the section where we randomly swap between the worst time slot and any other,
  # it might make sense to swap with the best one instead

#******* PREPROCESS *******

require_once "../php/config.php";

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

$sqlBands = "SELECT b.BandID, b.Name, bp.PorchLocation, b.Conflicts FROM bands b, bandstoporchfests bp WHERE b.BandID = bp.BandID AND bp.PorchfestID = 1";
$resultBands = $conn->query($sqlBands);
if (!$resultBands) { #for band id, porch location and conflicts
    printf("Get Bands-Porchfests failed\n");
    exit();
}

// GLOBAL VARIABLES
$kNeighbors = 4;

# pull data from DB
# for each band we'll need: available time slots, address, conflicts
function populateBandsTimeSlots(){
  global $resultBandsTimeSlots;
  global $bandsTimeSlots;
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
  echo "creating band objects\n";
  flush();
  global $resultBands;
  global $bandsHashMap;
  global $bandsTimeSlots;
  global $totalNumTimeSlots;
  global $timeslotsPorchfests;
  
  $tmp = getQueryArr($resultBands);
  for ($i = 0; $i < sizeof($tmp); $i++){
    flush();
    $bandId = $tmp[$i][0];
    $bandName = $tmp[$i][1];
    $bandLocation = $tmp[$i][2];
    $bandConflictsString = $tmp[$i][3];
        
    // convert the string of conflicts into an array of conflicts
    $bandConflicts = explode(',', $bandConflictsString);
    if (sizeof($bandConflicts[0]) == 0) {
      $bandConflicts = [];
    }
    $coordinateLocation = getCoordinates($bandLocation);
    $availableTimeSlots = [];
    
    for ($j = 0; $j < $totalNumTimeSlots; $j++) {
      $slotID = $timeslotsPorchfests[$j];
      $availableTimeSlots[$slotID] = intVal(false);
    }
    foreach ($bandsTimeSlots[$bandId] as $canDoSlotID) {
      $availableTimeSlots[$canDoSlotID] = intVal(true);
    }
    $bandsHashMap[$bandId] = new Band($bandId, $bandName, $coordinateLocation[0], $coordinateLocation[1], $availableTimeSlots, $bandConflicts, -1, []);
  }
  echo "created all band objects\n";
  flush();
  return $bandsHashMap;
}

// GLOBAL VARIABLES
$bandsTimeSlots = populateBandsTimeSlots();
$bandsPorchfests = [];
while ($row = $resultBandsPorchfests->fetch_array(MYSQLI_NUM)) {
  array_push($bandsPorchfests, $row[0]);
}
$timeslotsPorchfests = [];
while ($row = $resultTimeslotsPorchfests->fetch_array(MYSQLI_NUM)) {
  array_push($timeslotsPorchfests, $row[0]);
}
$bandsHashMap; //HashMap<int id, Band band> 
$bandsWithXTimeSlots = []; //HashMap<int numberOfTimeSlots, int[] bandIds> #max number of time slots a band can play in
$totalNumTimeSlots = sizeof($timeslotsPorchfests); //total number of timeslots for a porchfest

// input a string: address (i.e. "114 Summit Ave. Ithaca, NY 14850"
// output is a latitude, longitude coordinate pair (i.e. 42.442064,-76.483469)
function getCoordinates($address){
  // replace white space with "+" sign (match google search pattern)
  $address = str_replace(" ", "+", $address); 
  $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
  $response = file_get_contents($url);
  $json = json_decode($response,TRUE); //array object from the web response
  $lat = 42 + (rand(0,10000)/10000000);
  $lng = -76 - (rand(0,10000)/10000000);
  return array($lat, $lng);
  
  //array of lat, long
  return array((int)$json['results'][0]['geometry']['location']['lat'], (int)$json['results'][0]['geometry']['location']['lng']);
}


class Band {
//  
//  $ID; //int
//  $name; //String      # say, "The Amazing Crooners"
//  $lat; //float        # say, 42.450962
//  $lng; //float        # say, -76.501122
//  $availableTimeSlots; //boolean[]    # say, [ true, true, false, true ]
//  $conflicts;  // int[]   # say, [ 11111 ] band IDs that we conflict with
//  $slot; //int         # initially -1 until assigned
//  $distances; #HashMap<int bandID, int d> 
  
  function __construct($ID, $name, $lat, $lng, $availableTimeSlots, $conflicts, $slot, $distances) {
    //Band(int ID, float lat, float lng, String name, int[] conflicts, bool[] availableTimeSlots)
    $this->id = $ID; //int
    $this->name = $name; //String      # say, "The Amazing Crooners"
    $this->lat = $lat; //float        # say, 42.450962
    $this->lng = $lng; //float        # say, -76.501122
    $this->availableTimeSlots = $availableTimeSlots; //boolean[]   # say, [ true, true, false, true ]
    $this->conflicts = $conflicts;  // int[]   # say, [ 11111 ] band IDs that we conflict with
    $this->slot = $slot; //int         # initially -1 until assigned
    $this->distances = $distances; #HashMap<int bandID, int d> 
  }
  
  function getDistance($bandID) // needs to be an ID
  {
    global $bandsHashMap;

    if (!array_key_exists($bandID, $this->distances)) {
      $b = $bandsHashMap[$bandID]; //Band
      $this->distances[$bandID] = tdistance($this->lat, $this->lng, $b->lat, $b->lng);
    }
    return $this->distances[$bandID];
  }

  function calculateKNearest($sched) {
    global $kNeighbors;
    
    $bands = $sched->getBandsAtSlot($this->slot); //Band[] 
    $this->sortByDistance($bands);
    $result = [];
    for ($i = 0; $i < $kNeighbors; $i++) { # get the nearest k IDs
      array_push($result, $bands[$i]->id);
    }
    return $result;
  }
  
  function sortByDistance($bands) {
    $distances = [];
    for ($i = 0; $i < sizeof($bands); $i++) {
      $distances[$this->getDistance($bands[$i]->id)] = $bands[$i];
    }
    ksort($distances);
    $bands = array_values($distances);
  }
  
  function getConflicts(){
    return $this->conflicts;
  }
  
  function __clone() {
    return new Band($this->id, $this->name, $this->lat, $this->lng, $this->availableTimeSlots, $this->conflicts, $this->slot, $this->distances);
  }

}
  
class Schedule {
//  $schedule; //HashMap<int timeslot, Band[] bands> 
//  
//  $timeSlotVariances; //float[] 
  
  function __construct($timeSlotsArr) {
    $this->schedule = [];
    foreach ($timeSlotsArr as $timeSlotID) {
      $this->schedule[$timeSlotID] = [];
    }
    $this->timeSlotVariances = [];
    $this->score = -1;
  }
  
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
  
  //iterate through the timeslotVariances and find the timeslot with the
  //best/lowest variance. Try to swap with a band in there
  function randomSwap($band) {
    $worstTimeSlot = $band->slot;
    
    //get timeslot with min variance
    $minVariance = PHP_INT_MAX;
    ksort($this->timeSlotVariances);
    $flag = false;
    for ($i = 1; $i <= sizeof($this->timeSlotVariances); $i++){
      if (!$flag){
        if (noConflicts($this->schedule[$i], $band)){
          shuffle($this->schedule[$i]);
          foreach ($this->schedule[$i] as $possibleBandToSwap) {
            if (noConflicts($this->schedule[$worstTimeSlot], $possibleBandToSwap)){
              $this->delete($i, $possibleBandToSwap);
              $this->delete($worstTimeSlot, $band);
              $this->add($worstTimeSlot, $possibleBandToSwap);
              $this->add($i, $band);
              $possibleBandToSwap->slot = $worstTimeSlot;
              $band->slot = $i;
              $flag = true;
              break;
            }
          }
        }
      }
    }
    score($this);
  }
  
  function add($slot, $band){
    array_push($this->schedule[$slot], $band);
  }
  
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
  
  function getBandsAtSlot($timeSlot){
    return $this->schedule[$timeSlot];
  }
  
}

# populate the numberOfTimeSlots hashmap
function populateBandsWithXTimeSlots() {
  global $bandsWithXTimeSlots;
  global $bandsPorchfests;
  global $bandsTimeSlots;
  global $totalNumTimeSlots;
  global $bandsHashMap;
  
  echo "populating bandsWithXTimeSlots\n";
  flush();
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
  echo "populated\n";
  flush();
}

#******* ALGORITHM *******

# HELPER FUNCTIONS
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

function getQueryArr($queryResult) {
  $result = [];
  while ($row = $queryResult->fetch_array(MYSQLI_NUM)) {
    array_push($result, $row);
  }
  return $result;
}
  
# moves band to a different timeslot
# returns true on success, false otherwise
function tryToMoveBand($id, $schedule) {
  $band = $bandsHashMap[$id];
  for ($slot = 1; $slot <= $totalNumTimeSlots; $slot++) {
    if ($slot == $band->slot) {
      continue;
    }
    if ($band->availableTimeSlots[$slot] && noConflicts($schedule[$slot], $band)) {
        $schedule->delete($band->slot, $band);
        if ($slot === null) {
          print("here1\n");
        }
        $schedule->add($slot, $band);
        $band->slot = $slot;
        return true;
    }
  }
  return false;
}

function noConflicts($bandsArr, $band){
  if (sizeof($bandsArr) == 0) {
    return true;
  }
  $conflicts = $band->getConflicts();
  foreach ($bandsArr as $b){
    if (in_array($b->name, $conflicts)) {
      return false;
    }
  }
  return true;
}

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

function generateBaseSchedule() {
  global $bandsHashMap;
  global $bandsWithXTimeSlots;
  global $totalNumTimeSlots;
  global $timeslotsPorchfests;
  
  echo "generating base schedule\n";
  flush();
  $bandsHashMap = createBandObjects();
  populateBandsWithXTimeSlots();
  $schedule = new Schedule($timeslotsPorchfests);
  $unassignedBandIDs = []; // int[] 
  $currentTimeSlot = 0;
  
  # phase 1: place as many bands as possible
  echo "phase 1\n";
  flush();
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
        $slot = (($currentTimeSlot + $i) % $totalNumTimeSlots) + 1;

        $isAvailable = $band->availableTimeSlots[$slot];
        $hasNoConflicts = noConflicts($schedule->schedule[$slot], $band);
        if ($isAvailable && $hasNoConflicts){
          # band can play at this time
          if ($slot === null) {
            print("here2\n");
          }
          $schedule->add($slot, $band);
          $currentTimeSlot = $slot + 1;
          $band->slot = $slot;
          $assigned = true;
          flush();
          break;
        }
        else{
          $i++;
        }
      }
      if (!$assigned) {
        echo "no available time slots for " . $id . "\n";
        flush();
        array_push($unassignedBandIDs, $id); # will deal with these later...
      }
    }
  }

  # phase 2: deal with the bands that were unable to be assigned in phase 1
  echo "phase 2\n";
  flush();
  foreach ($unassignedBandIDs as $uBandID) {
    $uBand = $bandsHashMap[$uBandID];
    flush();
    $success = false;
    foreach ($uBand->getConflicts() as $conflictingBandID) {
      $band = $bandsHashMap[$conflictingBandID];
      $oldTimeSlot = $band->slot;
      $success = tryToMoveBand($conflictingBandID, $schedule);
      if ($success) {
        if ($oldTimeSlot === null) {
          print("here3\n");
        }
        $schedule->add($oldTimeSlot, $uBand);
        $uBand->slot = $oldTimeSlot;
        break;
      }
    }
    if (!$success) {
      die("this is actually impossible. exit with grace.");
    }
  }
  echo "generated schedule!\n";
  echo "scoring the schedule...\n";
  flush();
  score($schedule);
  return $schedule;
}

# calculate average distance of k nearest neighbors

# score schedule, this is just the max variance over all timeslots
/*
for bands in each time slot
1) get k nearest neighbors
2) calculate average distance from those neighbors and store
3) get variance of that entire timeslot
4) keep track of the largest variance
*/
    
# k is an adjustable amount of nearest neighbors to calculate
function computeVariance($slot, $sched){
  global $kNeighbors;
  
  $knnData = [];
  foreach ($sched->getBandsAtSlot($slot) as $band) {
    $knearest = $band->calculateKNearest($sched);
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
        
# k is an adjustable amount of nearest neighbors to calculate
function score($sched) {
  global $timeslotsPorchfests;
  foreach ($timeslotsPorchfests as $slot){
    $variance = computeVariance($slot, $sched);
    print ($variance . "\n");
    if ($sched->score < $variance) {
      $sched->score = $variance;
    }
  }
}
    
# pairwise swaps to improve the schedule - will need to recompute the variance of the two time slots that are affected by the swap
function improve($sched) {
  global $timeslotsPorchfests;
  arsort($sched->timeSlotVariances);
  $highestVarianceTimeSlot = array_keys($sched->timeSlotVariances)[0]; # index of highest variance corresponds to highestVarianceTimeSlot
  
  $bands = $sched->getBandsAtSlot($highestVarianceTimeSlot);
  $minDistance = PHP_INT_MAX;
  $bx; 
  $by;
  for ($i = 0; $i < sizeof($bands); $i++) {
    $bi = $bands[$i];
    if ($bi === null) {
      print_r($bands);
      print($i . "\n");
      print($highestVarianceTimeSlot + "\n");
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
    print("didn't swap\n");
  }
  print("old score " . $sched->score . "\n");
  print("new score " . $newSched->score . "\n");
  if ($newSched->score < $sched->score) {
    return array(true, $newSched);
  }
  return array(false, $sched);
}
  
# create schedule then repeat
function run(){
  global $conn;
  $result;
  $NUM_SCHEDS_TO_GENERATE = 1;
  
  for ($i = 0; $i < $NUM_SCHEDS_TO_GENERATE; $i++){
    $tmp = generateBaseSchedule();
    $noImprovements = 0;
    while (true){
      $imp = improve($tmp);
      $tmp = $imp[1];
      $noImprovements = ($imp[0] ? 0 : $noImprovements + 1);
      if ($noImprovements === 100){
        print("no more improvements\n");
        break;
      }
    }
    if ($result === null || $tmp->score < $result->score){
      $result = $tmp;
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
        echo "Error: " . $sql . "<br>" . $conn->error . "\n";
      }
    }
  }
}

run();

?>