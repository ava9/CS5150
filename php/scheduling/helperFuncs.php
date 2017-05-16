<?php

/************************************************************/
/******************* HELPER FUNCTIONS ***********************/
/************************************************************/
        
/*
 * Calculates standard deviation of an array.
 * http://php.net/manual/en/function.stats-standard-deviation.php
 * 
 * @param $a        The array of data to find the standard deviation for. Note
 *                  that all values of the array will be cast to float.
 * @param $sample   Indicates if a represents a sample of the population; defaults to FALSE
 * 
 * @return          The standard deviation of the $a as a float
 */
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

/*
 * Computes the variance at each timeslot of a schedule and
 * updates the schedule's score vector, timeSlotScores.
 * 
 * @param $timeSlotID   A slot ID to compute the min distance of
 * @param $sched        A Schedule object
 *
 * @return              Variance between bands at timeSlotID
 */
function computeVariance($timeSlotID, $sched){
  global $kNeighbors; //how many nearest neighbors used to calculate distance variance
  // $kNeighbors = sizeof($sched->getBandsAtSlot($timeSlotID));
  
  $knnData = [];
  foreach ($sched->getBandsAtSlot($timeSlotID) as $band) {
    $knearest = $band->calculateKNearest($sched, $kNeighbors);
    $avg = 0;
    foreach ($knearest as $nearestBand){
      $avg = $avg + $band->distances[$nearestBand];
    }
    array_push($knnData, ($avg/$kNeighbors)) ;
    # gets average distance from knn for each band at each timetimeSlotID 
  }
  # calculate variance
  $deviation = stats_standard_deviation($knnData);
  # update score of timetimeSlotID 
  $sched->timeSlotScores[$timeSlotID]->variance = $deviation * $deviation;
  return $deviation * $deviation;
}

/*
 * Computes the minimum distance at each timeslot of a schedule and
 * updates the schedule's score vector, timeSlotScores.
 * 
 * @param $timeSlotID   A slot ID to compute the min distance of
 * @param $sched        A Schedule object
 *
 * @return              Minimum distance between bands at timeSlotID
 */
function computeMinDist($timeSlotID, $sched) {
  global $bandsHashMap;

  # iterate through all bands at each timeslot
  $minDist = PHP_INT_MAX;

  foreach ($sched->getBandsAtSlot($timeSlotID) as $bandObj) {
    # calculateKNearest returns an array of size k and we just want
    # the 0th one
    $closestBandID = $bandObj->calculateKNearest($sched, 1)[0];
    $distance = $bandObj->getDistance($closestBandID);

    // assert($distance != 0, "Timeslot should never have two bands with the same porch location!");

    if ($distance < $minDist) {
      $minDist = $distance;

      # update the schedule score at this timeslot
      $currScore = $sched->timeSlotScores[$timeSlotID];
      $currScore->setMinDist($minDist);
      $currScore->closestBandID1 = $bandObj->id;
      $currScore->closestBandID2 = $closestBandID;
    }

  }

  return $minDist;

}



/*
 * Computes the distance of each band at each timeslot of a schedule and
 * flags the band if any of its neighbors are within $distance.
 * 
 * @param $distance        Distance (in meters) to check 
 * @param $schedule        A Schedule object
 *
 */
function flagBandsAtDist($distance, $schedule) {
  global $timeslotsPorchfests;
  foreach ($timeslotsPorchfests as $timeslotID) {
    $bandsArr = $schedule->getBandsAtSlot($timeslotID);
    foreach ($bandsArr as $bandObj) {
      if (!bandOverMinDist($bandsArr, $bandObj, 0)) {
        $bandObj->flag = 1;
      } else if (!bandOverMinDist($bandsArr, $bandObj, $distance)) {
        $bandObj->flag = 2;
      }
    }
  }
}
  
/*
 * Moves band to a different timeslot and returns true on success, false
 * otherwise.
 * 
 * @param $id           The id of the band we want to move
 * @param $schedule     The current schedule object
 * 
 * @return              True if swap was successful, False otherwise
 */
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

/*
 * Returns true if band is farther than $minDist away from every band in
 * $bandsArr
 *
 * @param $bandsArr     Array of Band objects
 * @param $band         Band object
 * @param $minDist      Int representing minimum distance to check (in meters)
 * 
 * @return              A boolean
 */
function bandOverMinDist($bandsArr, $band, $minDist) {

  if (sizeof($bandsArr) == 0) {
    return true;
  }

  foreach ($bandsArr as $b){
    if ($band->id == $b->id) {
      continue;
    }

    if ($band->getDistance($b->id) <= $minDist) {
      return false;
    }
  }

  return true;

}

/* 
 * Returns true if $band does not conflict with any band in $bandArr, false
 * otherwise
 * 
 * @param $bandsArr     An array of band objects
 * @param $band         The band object of the band to compare with the array
 * 
 * @return              A boolean
 */
function noConflicts($bandsArr, $band){
  
  if (sizeof($bandsArr) == 0) {
    return true;
  }

  $conflicts = $band->getConflicts();

  if (sizeof($conflicts > 0)) {

    if (sizeof($conflicts) == 0 || $conflicts[0] == null) {
      return true;
    }

    foreach ($bandsArr as $b){
      if (in_array($b->id, $conflicts)) {
        return false;
      }
    }

  } else {
    return true;
  }

}

/*
 * Calculates the distance between two longitude/latitude coordinates in meters
 * 
 * @param latitudeFrom      The latitude of the first location
 * @param longitudeFrom     The longitude of the first location
 * @param latitudeTo        The latitude of the second location
 * @param longitudeTo       The longitude of the second location
 * @param earthRadius       The radius of the earth in meters, which is 6371000
 * 
 * @return                  The distance between two locations in meters
 */
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
  

?>