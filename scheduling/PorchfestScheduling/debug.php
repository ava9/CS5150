<?php

/************************************************************/
/******************* DEBUG FUNCTIONS ************************/
/************************************************************/

/* 
 * Takes in a string to print. If $DEBUG_ON is True the string will print to
 * console. Otherwise printing is disabled.
 * 
 * @param $val     A string that you want to print
 */
function DEBUG_ECHO($val) {
  $DEBUG_ON = true;
  if ($DEBUG_ON) {
    echo $val;
    flush();
  }
}

/* 
 * Returns random lat lng coordinates in the Ithaca area
 * 
 * @return  An array of latitude, longitude
 */
function getRandomCoordinates(){
  $lat = 42 + (rand(0,10000)/10000000);
  $lng = -76 - (rand(0,10000)/10000000);
  return array($lat, $lng);
}

?>
