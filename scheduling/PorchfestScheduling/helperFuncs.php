<?php

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
    if ( $band->availableTimeSlots[$slotID] && noConflicts($schedule->schedule[$slotID], $band) ) {
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

function namesToIDs($names) {
  global $bandsHashMap;

  $result = [];
  foreach ($names as $n) {
    foreach ($bandsHashMap as $bandID => $band) {
      if ($band->name == $n) {
        array_push($result, $bandID);
      }
    }
  }
  
  return $result;
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
  

// Returns random lat lng coordinates in the Ithaca area
function getRandomCoordinates($address){
  $lat = 42 + (rand(0,10000)/10000000);
  $lng = -76 - (rand(0,10000)/10000000);
  return array($lat, $lng);
}

?>