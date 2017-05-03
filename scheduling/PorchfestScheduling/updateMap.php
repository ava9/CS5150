<?php
require_once "main.php";
require_once "helperFuncs.php";
require_once "init.php";

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

$bandsHashMap = createBandObjects();



?>