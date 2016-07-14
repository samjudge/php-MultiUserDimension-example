<?php

class Map {
	$w; //width
	$h; //height;
	$tiles; //array of w by h tile objects
	
	public function generateMap(){
		
	}
	
	public function __Construct($w, $h){
		$this->$w = $w;
		$this->$h = $h;
		while($x = 0 ; $x < $w; $x++){
			while($y = 0 ; $y < $h; $y++){
				$this->$tiles[x][y] = new Tile(x,y,false);
			}
		}
	}
	
}

?>