<?php

require_once "config.php";
require_once "debug.php";
require_once "Band.php";
require_once "Schedule.php";
require_once "helperFuncs.php";
require_once "algorithm.php";

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

$bandsTimeSlots;
$bandsPorchfests;
$timeslotsPorchfests;
$bandsHashMap;
$bandsWithXTimeSlots;
$totalNumTimeSlots;

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

  $sqlBands = "SELECT b.BandID, b.Name, bp.PorchLocation, b.Conflicts, bp.Latitude, bp.Longitude FROM bands b, bandstoporchfests bp WHERE b.BandID = bp.BandID AND bp.PorchfestID = " . $PorchfestID;
  $resultBands = $conn->query($sqlBands);
  if (!$resultBands) { #for band id, porch location and conflicts
      printf("Get Bands-Porchfests failed\n");
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
  


  for ($i = 0; $i < $NUM_SCHEDS_TO_GENERATE; $i++){
    echo "running schedule " . $i . "\n";
    $tmpAndSuccess = generateBaseSchedule($tmp);
    $tmp = $tmpAndSuccess[0];
    $success = $tmpAndSuccess[1];

    if (!$success) {
      echo "failed to generate base schedule...moving on\n";
      continue;
    }
    $noImprovements = 0;
    while (true){
      /* 
       * improve($tmp) returns an array of size 2 
       * 0 index is a boolean saying whether we've improved, 
       * 1 index is the new schedule if we improved or the 
       * same schedule if we did not improve 
       */
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
      $sql = "UPDATE bandstoporchfests SET TimeslotID = " . $ts . " WHERE BandID = " . $id . " AND PorchfestID = " . $PorchfestID;
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