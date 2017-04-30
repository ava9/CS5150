<?php

/* This is a class for a schedule object. The schedule object contains three
 * fields: 
 * $schedule is a hashmap of timeslots to an array of Band objects scheduled
 *    to play in that timeslot 
 * $timeSlotScores is a hashmap of timeslots to vectors of scores
 *    Currently contains minimum distance between any two bands and variance
 * $score is a Score object for the overall score of the schedule
 *    This is the score of the worst time slot, which is checked in the
 *    compareTo function. It is weighted evenly between the minimum distance
 *    between any two bands in the timeslot and the variance of distances 
 *    between the bands in the timeslot.
 */

class Schedule {
   /*
   * Constructs a schedule object where the $schedule hashamp has an empty 
   * array for each of the timeslot keys, the $timeSlotScores hashmap has new
   * initialized Score objects for each timeslot, and the overall $score is a 
   * new Score object. 
    * 
   * @param timeSlotsArr    array of all timeslots available for a particular 
   *    porchfest
   */
  function __construct($timeSlotsArr) {
    $this->schedule = [];
    foreach ($timeSlotsArr as $timeSlotID) {
      $this->schedule[$timeSlotID] = [];
      $this->timeSlotScores[$timeSlotID] = new Score();
    }
    $this->score = new Score();
  }
  
  /* Creates a copy of the schedule with score reinitialized to -1 
   * Returns a copy of the schedule
   * 
   * @param timeSlotsArr    array of all timeslots available for a particular
   *    porchfest
   */
  function deepCopy($timeSlotsArr) {
    $copy = new Schedule($timeSlotsArr);
    $copy->schedule = [];
    $copy->timeSlotScores = [];

    foreach ($this->schedule as $timeslot => $bandArr) {
      $tmp = [];
      foreach ($bandArr as $band) {
         array_push($tmp, clone $band);
      }
      $copy->schedule[$timeslot] = $tmp;
    }

    foreach ($this->timeSlotScores as $timeslot => $score) {
      $copy->timeSlotScores[$timeslot] = $score->deepCopy();
    }

    $copy->score = new Score();
    return $copy;
  }

   /* Gets the variances for each timeslot from the $timeSlotScores vector of 
   *    scores and creates a new array with key timeslot and value variance.
   *  Returns this array. 
   */
  function getTimeSlotVariances() {
    $timeSlotVariances = [];
    foreach ($this->timeSlotScores as $timeSlotID=>$score) {
      $timeSlotVariances[$timeSlotID] = $score->variance;
    }
    return $timeSlotVariances;
  }
  
  /* Iterates through the timeSlotVariances and finds the timeslot with the 
   * best/lowest variance. Try to swap with a band in there. If not possible
   * due to conflicts, try the next best timeslot until either the band has 
   * been successfully swapped or all time slots have been examined. 
   * 
   * @param $band   the band in the worst time slot that needs to be swapped
   *    with a band from another time slot
   */
  function randomSwap($band) {
    //array of all timeslots available for a particular porchfest
    global $timeslotsPorchfests; 
    
    $worstTimeSlot = $band->slot;
    
    //get timeslot with min variance
    $minVariance = PHP_INT_MAX;

    $timeSlotVariances = $this->getTimeSlotVariances();
    ksort($timeSlotVariances);
    $flag = false;
    for ($i = 0; $i < sizeof($timeSlotVariances); $i++){
      if (!$flag){
        $timeslotID = $timeslotsPorchfests[$i];
        if (noConflicts($this->schedule[$timeslotID], $band) &&
            bandOverMinDist($this->schedule[$timeslotID], $band)) {

          shuffle($this->schedule[$timeslotID]);
          foreach ($this->schedule[$timeslotID] as $possibleBandToSwap) {
            if (noConflicts($this->schedule[$worstTimeSlot], $possibleBandToSwap) && 
                bandOverMinDist($this->schedule[$worstTimeSlot], $possibleBandToSwap)
              ){

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
  }

  /*
   * Calculates the overall score based on the individual scores 
   * of each of its timeslots. This is done via the compareTo function which
   * weights minimum distance between two bands and maximum variance of the 
   * distances between bands in each timeslot.
   * 
   * Sets this schedule object's score field and makes a deep copy of the score
   * when it is better than the current score.
   */
  function score() {
    //array of all timeslots available for a particular porchfest
    global $timeslotsPorchfests; 
    foreach ($timeslotsPorchfests as $slot){
      computeVariance($slot, $this);
      computeMinDist($slot, $this);

      $s = $this->timeSlotScores[$slot];
      # overall score is represented by the worst timeslot
      if ($this->score->compareTo($s) === 1) {
        $this->score = $s->deepCopy();
      }
    }
  }
      
  /* Pairwise swaps to improve the schedule. Recomputes the
   * variance and min distance between any two bands in the two time slots that
   * are affected by the swap.
   * 
   * Returns an array where the first element is a boolean value true or false,
   * and the second value is the new improved schedule or the original schedule,
   * respectively.
   */ 
  function improve() {
    //array of all timeslots available for a particular porchfest  
    global $timeslotsPorchfests;

    $timeSlotVariances = $this->getTimeSlotVariances();
    arsort($timeSlotVariances);
    # 0 is the index of highest variance corresponds to highestVarianceTimeSlot
    $highestVarianceTimeSlot = array_keys($timeSlotVariances)[0]; 
    
    # Choose a band from the highest variance timeslot to swap out.
    # We choose by figuring out the two bands that have the minimum distance
    # between each other. Then we pick one of them randomly.
    $bands = $this->getBandsAtSlot($highestVarianceTimeSlot);
    $minDistance = PHP_INT_MAX;
    $bx; 
    $by;
    for ($i = 0; $i < sizeof($bands); $i++) {
      $bi = $bands[$i];
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

    $newSched = $this->deepCopy($timeslotsPorchfests);
    $newSched->randomSwap($br); # update score in randomSwap()
    $newSched->score();
    if ($this == $newSched) {
      DEBUG_ECHO("didn't swap\n");
    }
    if ($newSched->score->compareTo($this->score) === 1) {
      DEBUG_ECHO("improved!\n");
      DEBUG_ECHO("old score " . $this->score->toInt() . "\n");
      DEBUG_ECHO("new score " . $newSched->score->toInt() . "\n");
      return array(true, $newSched);
    }
    return array(false, $this);
  }
  
  /* Takes in band and time slot. 
   * Adds band to timeslot in this schedule 
   */
  function add($slot, $band){
    array_push($this->schedule[$slot], $band);
  }
  
  /* Takes in band and time slot. 
   * Deletes band from timeslot in this schedule 
   */
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
  
  /* Returns a list of all bands scheduled at this specified time slot in this 
   * schedule.
   * 
   * @param $timeSlot   the particular time slot for which we want to see the 
   *    currently scheduled bands.
   */
  function getBandsAtSlot($timeSlot){
    return $this->schedule[$timeSlot];
  }
  
}

?>