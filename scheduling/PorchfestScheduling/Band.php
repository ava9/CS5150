<?php

class Band {
	
  function __construct($ID, $name, $lat, $lng, $availableTimeSlots, $conflicts, $slot, $distances) {
    //Band(int ID, float lat, float lng, String name, int[] conflicts, bool[] availableTimeSlots)
    $this->id = $ID; //int
    $this->name = $name; //String      # say, "The Amazing Crooners"
    $this->lat = $lat; //float        # say, 42.450962
    $this->lng = $lng; //float        # say, -76.501122
    $this->availableTimeSlots = $availableTimeSlots; //<slotID, boolean> map   # say, [10: true, 21: true, 30: false, 13: true ]
    $this->conflicts = $conflicts;  // int[]   # say, [ 11111 ] band IDs that we conflict with
    $this->slot = $slot; //int         # initially -1 until assigned
    $this->distances = $distances; #HashMap<int bandID, int d> 
  }
  
  /* Takes a bandID and calculates the distance between this band object and the band corresponding to bandID */
  function getDistance($bandID)
  {
    global $bandsHashMap; //HashMap<int id, Band band>

    if (!array_key_exists($bandID, $this->distances)) {
      $b = $bandsHashMap[$bandID]; //Band object
      $this->distances[$bandID] = tdistance($this->lat, $this->lng, $b->lat, $b->lng);
    }
    return $this->distances[$bandID];
  }

  /* Takes a schedule object and calculates the k (specified in the global variables section
  		nearest bands to this band object. Returns an array of size k of bandIDs */
  function calculateKNearest($sched, $kNeighbors) {

    $bands = $sched->getBandsAtSlot($this->slot); //Band[] 
    $this->sortByDistance($bands);
    $result = [];
    for ($i = 0; $i < $kNeighbors; $i++) { # get the nearest k IDs
      array_push($result, $bands[$i]->id);
    }
    return $result;
  }
  
  /* Sorts the keys of the bands at the same time slot as this band object by distance to this band object */
  function sortByDistance($bands) {
    $distances = [];
    for ($i = 0; $i < sizeof($bands); $i++) {
      $distances[$this->getDistance($bands[$i]->id)] = $bands[$i];
    }
    ksort($distances);
    $bands = array_values($distances);
  }
  
  /* Getter function that returns a list of all bands that conflict with this band object */
  function getConflicts(){
    return $this->conflicts;
  }

  /* Overwrites the php clone function to create a new Band object with the same values as this band object */
  function __clone() {
    return new Band($this->id, $this->name, $this->lat, $this->lng, $this->availableTimeSlots, $this->conflicts, $this->slot, $this->distances);
  }

}
  


?>