<?php

require_once "config.php";
require_once "debug.php";
require_once "Band.php";
require_once "Schedule.php";
require_once "Score.php";
require_once "helperFuncs.php";
require_once "algorithm.php";

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
$NUM_SCHEDS_TO_GENERATE;

$resultBandsTimeSlots;
$resultBandsPorchfests;
$resultTimeslotsPorchfests;
$resultBands;
$resultBandConflicts;

// TODO: COMMENT THESE
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

  $result = null;

  /************************************************************/
  /******************* DATABASE SQL QUERIES *******************/
  /************************************************************/

  // Create connection
  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

  if ($conn->connect_errno) {
      printf("Connect failed: %s\n", $conn->connect_error);
      exit();
  }

  #Ithaca PorchFest
  $PorchfestID = 1;

  $sqlBandsTimeSlots = "SELECT ats.BandID, ats.TimeslotID FROM bandavailabletimes ats, bandstoporchfests bp WHERE ats.BandID = bp.BandID AND bp.PorchfestID = " . $PorchfestID;
  $resultBandsTimeSlots = $conn->query($sqlBandsTimeSlots);
  if (!$resultBandsTimeSlots) { #for available time slots
      printf("Get Bands-Timeslots failed\n");
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

  $sqlBands = "SELECT b.BandID, b.Name, bp.PorchLocation, bp.Latitude, bp.Longitude FROM bands b, bandstoporchfests bp WHERE b.BandID = bp.BandID AND bp.PorchfestID = " . $PorchfestID;
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
    $successAndIntermediateSched = generateBaseSchedule();
    $success = $successAndIntermediateSched[0];
    $intermediateSchedule = $successAndIntermediateSched[1];

    if (!$success) {
      echo "Failed to generate base schedule in this iteration. Moving on\n";
      if ($i+1 == $NUM_SCHEDS_TO_GENERATE) {
        echo "Error: failed to generate a single base schedule, 
              either too many band conflicts or not enough spread 
              apart porch locations\n";
      }
      continue;
    }

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
  }
  
  #******* POST PROCESS *******
  # write back to the database with the bands' assigned timeslot
  $finalSched = $result->schedule;
  foreach ($finalSched as $ts => $bands) {
    foreach ($bands as $b) {
      $id = $b->id;
      $sql = "UPDATE bandstoporchfests SET TimeslotID = " . $ts . " WHERE BandID = " . $id . " AND PorchfestID = " . $PorchfestID;
      if ($conn->query($sql) === false) {
        DEBUG_ECHO("Error: " . $sql . "<br>" . $conn->error . "\n");
      }
    }
  }
  echo "Success!";
}

# (∩^-^)⊃━☆ﾟ.*･｡ﾟ magic time #
run();
?>