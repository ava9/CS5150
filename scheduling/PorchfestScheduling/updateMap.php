<?php
require_once "config.php";
require_once "debug.php";
require_once "Band.php";
require_once "Schedule.php";
require_once "Score.php";
require_once "helperFuncs.php";
require_once "init.php";

$MIN_DISTANCE = 25; // minimum distance in meters allowed between playing bands
$resultBands;
$bandsHashMap = [];         // HashMap<int id, Band band> 
$bandsTimeSlots;       // HashMap<BandID, TimeslotID>
$totalNumTimeSlots;    // total number of timeslots for a porchfest
$timeslotsPorchfests;  // array of all timeslots available for a particular porchfest
$bandConflicts;        // HashMap<int id, Array[BandID]> of all bands conflicting with a band ID
	#Ithaca PorchFest
$PorchfestID = 1;

function createSchedule() {
  global $MIN_DISTANCE;
  global $resultBands;
  global $bandsHashMap;         // HashMap<int id, Band band> 
  global $bandsTimeSlots;       // HashMap<BandID, TimeslotID>
  global $totalNumTimeSlots;    // total number of timeslots for a porchfest
  global $timeslotsPorchfests;  // array of all timeslots available for a particular porchfest
  global $bandConflicts;        // HashMap<int id, Array[BandID]> of all bands conflicting with a band ID

	$bandsHashMap = createBandObjects(
    $MIN_DISTANCE,
    $resultBands,
    $bandsTimeSlots,   
    $totalNumTimeSlots, 
    $timeslotsPorchfests,
    $bandConflicts        
	);
  $schedule = new Schedule($timeslotsPorchfests);
  for ($i = 0; $i < sizeof($resultBands); $i++){
  	$bandID = $resultBands[$i][0];
	  $timeslotID = $resultBands[$i][5];
	  $bandObj = $bandsHashMap[$bandID];
	  $bandObj->slot = $timeslotID;
	  $schedule->add($timeslotID, $bandObj);
  }
  DEBUG_ECHO("Initial Schedule Created\n");
  return $schedule;
}

function updateMap() {
	global $MIN_DISTANCE;
	global $PorchfestID;

	initGlobals();

	$schedule = createSchedule();
	flagBandsAtDist($MIN_DISTANCE*1.5, $schedule);

  $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
  foreach ($schedule->schedule as $ts => $bands) {
    foreach ($bands as $b) {
      // Update flagged column for each band in the DB
      $id = $b->id;

      $sql = "UPDATE bandstoporchfests SET Flagged = " . $b->flag . " WHERE BandID = " . $id . " AND PorchfestID = " . $PorchfestID;
      if (!$conn->query($sql)) {
        DEBUG_ECHO("Error: " . $sql . "<br>" . $conn->error . "\n");
      }
    }
  }

}

updateMap();

?>