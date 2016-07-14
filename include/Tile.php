<?php

class Tile{
	public $x; //width
	public $y; //height;
	public $passable; //array of w by h tile objects
	public $testCode;
	
	public function __construct($x, $y, $isPass){
		$this->x = $x;
		$this->y = $y;
		$this->passable = $isPass;
	}
}

?>