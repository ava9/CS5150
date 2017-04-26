<?php

class Score {

  function __construct() {
  	$this->variance = -PHP_INT_MAX;
  	$this->minDistance = PHP_INT_MAX;
  	$this->closestBandID1 = -1;
  	$this->closestBandID2 = -1;
  }

  function setVariance($var) {
  	$this->variance = $var;
  }

  function setMinDist($dist) {
  	$this->minDistance = $dist;
  }

  function deepCopy() {
    $copy = new Score();
    $copy->variance = $this->variance;
    $copy->minDistance = $this->minDistance;
    $copy->closestBandID1 = $this->closestBandID1;
    $copy->closestBandID2 = $this->closestBandID2;
    return $copy;
  }

  function toInt() {
    return $this->minDistance;
  }

  /*
   * Returns -1 if this worse than $otherScore
   *          0 if this == $otherScore
   *          1 if this better than $otherScore
   * where "worse than" and "better than" is configurable 
   * and subjective
   * 
   * @param $otherScore    A Score object
   */
  function compareTo($otherScore) {
  	$result = 0;

  	if ($this->minDistance < $otherScore->minDistance) {
  		$result -= 1;
  	}
  	if ($this->minDistance > $otherScore->minDistance) {
  		$result += 1;
  	}

  	if ($this->variance < $otherScore->variance) {
  		$result += 1;
  	}
  	if ($this->variance > $otherScore->variance) {
  		$result -= 1;
  	}

  	if ($result < 0) {
  		return -1;
  	} else if ($result > 0) {
  		return 1;
  	} else {
  		return 0;
  	}
  }




}

?>