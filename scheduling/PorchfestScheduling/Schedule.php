<?php

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



?>