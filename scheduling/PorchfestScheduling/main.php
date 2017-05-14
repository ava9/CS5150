<?php

require_once __DIR__."/../../config.php";
require_once "debug.php";
require_once "Band.php";
require_once "Schedule.php";
require_once "Score.php";
require_once "helperFuncs.php";
require_once "init.php";

# ALGORITHM OVERVIEW #

/*
 * Randomly create base schedule
 * Score schedule - the method of scoring is based on the 
 * implementation of the implmentation of the Score class
 * 
 * for bands in each time slot:
 *   1) get k nearest neighbors
 *   2) calculate average distance from those neighbors and store
 *   3) get variance of that entire timeslot
 *   4) keep track of the largest variance
 */


# ALGORITHM OVERVIEW #

/*
 * Randomly create base schedule
 * Score schedule - the method of scoring is based on the 
 * implementation of the implmentation of the Score class
 * 
 * for bands in each time slot:
 *   1) get k nearest neighbors
 *   2) calculate average distance from those neighbors and store
 *   3) get variance of that entire timeslot
 *   4) keep track of the largest variance
 */


/************************************************************/
/******************* GLOBAL VARIABLES ***********************/
/************************************************************/

$NUM_SCHEDS_TO_GENERATE = 1;
$MIN_DISTANCE = 25; // minimum distance in meters allowed between playing bands
$kNeighbors = 10; //how many nearest neighbors used to calculate distance variance

#Ithaca PorchFest
// $PorchfestID = $_POST['porchfestid'];
$PorchfestID = 1;

$resultBandsTimeSlots;
$resultBandsPorchfests;
$resultTimeslotsPorchfests;
$resultBands;
$resultBandConflicts;

/* 
 * Hashmap (int bandID => int[] timeslotID) 
 * All time slots that a band can play at 
 */
$bandsTimeSlots;

/* All bandIDs that can play at this PorchFest */
$bandsPorchfests;

/* array of all timeslot IDs */
$timeslotsPorchfests;

/* Hashmap<BandID, Band Object> */
$bandsHashMap;
$bandsWithXTimeSlots;
$totalNumTimeSlots;
$bandConflicts;

/* create schedule then repeat */
function run(){
  global $NUM_SCHEDS_TO_GENERATE;
  global $MIN_DISTANCE;
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

  $result = null;


  initGlobals();


  /************************************************************/
  /*********************** ALGORITHM LOOP *********************/
  /************************************************************/

  for ($i = 0; $i < $NUM_SCHEDS_TO_GENERATE; $i++){
    echo "running schedule " . $i . "\n";
      /* 
       * generateBaseSchedule() returns an array of size 2 
       * 0 index is a boolean saying whether we've successfully created a schedule,
       * 1 index is the new schedule  
       */
    $intermediateSchedule = generateBaseSchedule();

    // if (!$success) {
    //   echo "Failed to generate base schedule in this iteration. Moving on\n";
    //   if ($i+1 == $NUM_SCHEDS_TO_GENERATE) {
    //     echo "Error: failed to generate a single base schedule, 
    //           either too many band conflicts or not enough spread 
    //           apart porch locations\n";
    //   }
    //   continue;
    // }

    $noImprovements = 0;
    while (true){
      /* 
       * improve($intermediateSchedule) returns an array of size 2 
       * 0 index is a boolean saying whether we've improved, 
       * 1 index is the new schedule if we improved or the 
       * same schedule if we did not improve 
       */
      $imp = $intermediateSchedule->improve();
      $intermediateSchedule = $imp[1];
      $noImprovements = ($imp[0] ? 0 : $noImprovements + 1);
      if ($noImprovements == 100){
        DEBUG_ECHO("no more improvements\n");
        break;
      }
    }
    if ($result === null || $intermediateSchedule->score < $result->score){
      $result = $intermediateSchedule;
      echo "finished improving with score " . $result->score->toInt() . "\n";
    }

    flagBandsAtDist($MIN_DISTANCE * 2, $result);

  }
  
  #******* POST PROCESS *******
  # write back to the database with the bands' assigned timeslot
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  $finalSched = $result->schedule;
  foreach ($finalSched as $ts => $bands) {
    foreach ($bands as $b) {
      $id = $b->id;
      $sql = "UPDATE bandstoporchfests SET TimeslotID = " . $ts . " WHERE BandID = " . $id . " AND PorchfestID = " . $PorchfestID;
      if ($conn->query($sql) === false) {
        DEBUG_ECHO("Error: " . $sql . "<br>" . $conn->error . "\n");
      }

      // Update flagged column for each band in the DB
      $sql = "UPDATE bandstoporchfests SET Flagged = " . $b->flag . " WHERE BandID = " . $id . " AND PorchfestID = " . $PorchfestID;
      if ($conn->query($sql) === false) {
        DEBUG_ECHO("Error: " . $sql . "<br>" . $conn->error . "\n");
      }

    }
  }
  $setScheduled = "UPDATE porchfests SET Scheduled = 1 WHERE PorchfestID = " . $PorchfestID;
  if ($conn->query($setScheduled) === false) {
    DEBUG_ECHO("Error: " . $setScheduled . "<br>" . $conn->error . "\n");
  }
  echo "Success!";
}

# (∩^-^)⊃━☆ﾟ.*･｡ﾟ magic time #
run();
?>