<?php

include(__DIR__."/Tile.php");
include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

class Map{
	public $w; //width
	public $h; //height
	public $tiles; //array of w by h tile objects
	private $cList; //list of cells/tiles
	private $mapCopy;
	private $roomOrgins;
	private $keyPlace;
	private $telePlace;
	
	public function generateMap(){
		for($i = 0; $i < 150; $i++){
			$obstructed = false;
			$roomw;
			$roomh;//random room width and height
			if(rand(0,5) == 1){
				$roomw = rand(8,15);
				$roomh = rand(8,15);//random room width and height
				
			} else {
				$roomw = rand(2,6);
				$roomh = rand(2,6);//random room width and height
			}
			$roomx = rand(1,($this->w-$roomw)-1);
			$roomy = rand(1,($this->h-$roomh)-1); //random room position
			for($x = $roomx ; $x < $roomx+$roomw; $x++){
				for($y = $roomy ; $y < $roomy+$roomh; $y++){
					foreach($this->findSurroundingTiles($this->getTile($x,$y)) as $ttile){
						if($ttile->passable){
							$obstructed = true;
							break;
						}						
					}
					if($this->getTile($x,$y)->passable == true){
							$obstructed = true;
							break;
					}
					if($obstructed == true){
						break;
					}
				}
				if($obstructed == true){
					break;
				}
			}  //check if the tiles are obstructed by other empty tiles
			
			if ($obstructed == false){
				$roomOrgins[] = $this->getTile($roomx,$roomy);
				for($x = $roomx ; $x < $roomx+$roomw; $x++){
					for($y = $roomy ; $y < $roomy+$roomh; $y++){
						//echo $x;
						//echo $y;
						$this->getTile($x,$y)->passable = true;
						
					}
				}
			} //fill in the rooms if not obstructed by floor tiles
			
		} //put down as many rooms as possible
		
		$c = $this->findStartTile($this->tiles);
		$this->cList[] = $c; //add the first tile
		$this->carve($c,0); //let the games begin
		
		$mapCopy = array();
		for($x = 0; $x < $this->w ; $x++){
			for($y = 0; $y < $this->h ; $y++){
				$mapCopy[$x][$y] = clone $this->tiles[$x][$y];
			}
		}
		for($x = 0; $x < $this->w ; $x++){
			for($y = 0; $y < $this->h ; $y++){
				$curTile = $this->tiles[$x][$y];
				if($curTile->passable == true){
					$noCardTiles = 0;
					foreach($this->findCardinalTiles($curTile) as $ttile){
						if($ttile->passable == true){
							$noCardTiles++;
						}
					}
					if($noCardTiles == 1){
						if(rand(0,9) != 1){
							$mapCopy[$curTile->x][$curTile->y]->passable = false;
						}
					}
				}
			}
		}
		//
		for($x = 0; $x < $this->w ; $x++){
			for($y = 0; $y < $this->h ; $y++){
				$this->tiles[$x][$y] = clone $mapCopy[$x][$y];
			}
		}
		//var_dump($tiles);
		//echo "<br/>";
		//var_dump($mapCopy);
		//echo count($roomOrgins);
		foreach($roomOrgins as $room){
			//var_dump($room);
			//rand(1,3);
			$this->digDoors($room);
		}
		$this->placeObjectives();
		
	}
	
	private function carve($c,$indexNo){ //c is the current Tile being processed
		$cListStartCount = count($this->cList)-1;;
		unset($this->cList[$indexNo]);
		$this->cList = array_values($this->cList);
		$cNeighbors = $this->findCardinalTiles($c);
		foreach($cNeighbors as $cell){
			if($cell->passable == true){
				continue;
			}
			$cNX = $cell->x;
			$cNY = $cell->y;
			if(!$this->isLegalTile($cell)){
				continue;
			}
			$this->tiles[$cNX][$cNY]->passable = true;
			$cell->passable = true;
			$this->cList[] = $cell;
		}
		if(empty($this->cList)){
			return;
		}
		$cListEndCount = count($this->cList)-1;;
		$indexNo = rand($cListStartCount,$cListEndCount);
		$this->carve($this->cList[$cListEndCount],$indexNo);
		return;
	}
	
	private function findStartTile($tileList){
		$obstructed = false;
		while(true) {
			$rX = rand(1,$this->w-1);
			$rY = rand(1,$this->h-1); //-1 so it is not on the edge of the map
			$start = $this->getTile($rX,$rY);
			if($start->passable == true){
				continue;
			}
			elseif($rX % 2 != 1 || $rY % 2 != 1){
				continue;
			}else {
				if($this->isAloneTile($start)){
					////echo "<br/>Start Node Found";
					$this->tiles[$start->x][$start->y]->passable = true;
					return $start;
				} else {
					continue;
				}
			}
		}
	}
	
	private function checkTileInBounds($tile){
		$x = $tile->x;
		$y = $tile->y;
		if($x < $this->w - 1 && $y < $this->h - 1 && $x > 0 && $y > 0){
			return true;
		} else {
			return false;;
		}
	} //returns true if a tile is on the map and not on the map's edge
	
	private function isLegalTile($tile){
		if(!$this->checkTileInBounds($tile)){
			return false;
		}
		$noCardTiles = 0;
		foreach($this->findCardinalTiles($tile) as $ttile){
			if($ttile->passable == true){
				$noCardTiles++;
			}
		}
		$cccTiles = $this->findSurroundingTiles($tile);
		$oX = $tile->x;
		$oY = $tile->y;
		foreach($cccTiles as $ttile){
			$x = $ttile->x;
			$y = $ttile->y;
			$co = count($cccTiles);
			if($ttile->passable == true){
				if($ttile->x == $tile->x-1 && $ttile->y == $tile->y-1){
					if (($this->tiles[$x+1][$y]->passable != true && $this->tiles[$x][$y+1]->passable != true)){
						return false;
					}
				}
				if($ttile->x == $tile->x-1 && $ttile->y == $tile->y+1){
					if (($this->tiles[$x+1][$y]->passable != true && $this->tiles[$x][$y-1]->passable != true)){
						return false;
					}
				}
				if($ttile->x == $tile->x+1 && $ttile->y == $tile->y-1){
					if (($this->tiles[$x-1][$y]->passable != true && $this->tiles[$x][$y+1]->passable != true)){
						return false;
					}
				}
				if($ttile->x == $tile->x+1 && $ttile->y == $tile->y+1){
					if (($this->tiles[$x-1][$y]->passable != true && $this->tiles[$x][$y-1]->passable != true)){
						return false;
					}
				}
			}
		}
		if($noCardTiles > 1){ //there can only be at most 2 surrounding tiles on all 8 sides, and no more than 1 the cardinal tiles
			return false;
		}
		return true;
	}
	
	private function isAloneTile($tile){
		if(!$this->checkTileInBounds($tile)){
			return false;
		}
		$noSurroundingTiles = 0;
		$ccTiles = $this->findSurroundingTiles($tile);
		foreach($ccTiles as $ttile){
			if($ttile->passable == true){
				$noSurroundingTiles++;
			}
		}
		if($noSurroundingTiles > 0){
			return false;
		}
		return true;
	}//bad programming, DRY. but I don't care. Checks to make sure tile is alone with no others.
	
	
	
	private function findSurroundingTiles($originTile){
		$nCList = array();
		for($xOffset = -1; $xOffset <= 1; $xOffset++){ //values -1,0,1
			for($yOffset = -1; $yOffset <= 1; $yOffset++){
				if($xOffset == 0 && $yOffset == 0){//ignore the calling tile
					continue;
				}
				$curTile = $this->getTile($originTile->x+$xOffset,$originTile->y+$yOffset);
				$nCList[] = $curTile;
			}
		}
		return $nCList;
	}
	
	private function findCardinalTiles($originTile){
		$nCList = array();
		$nCList[] = $this->getTile($originTile->x,$originTile->y-1);
		$nCList[] = $this->getTile($originTile->x-1,$originTile->y);
		$nCList[] = $this->getTile($originTile->x+1,$originTile->y);
		$nCList[] = $this->getTile($originTile->x,$originTile->y+1);
		//echo "<br/>";
		//echo "<br/>";
		//varr_dump($nCList);
		return $nCList;
	}
	
	public function getTile($x,$y){
		return $this->tiles[$x][$y];;
	}
	
	public function __construct($w, $h){
		$this->w = $w;
		$this->h = $h;
		for($x = 0 ; $x < $w; $x++){
			for($y = 0 ; $y < $h; $y++){
				$this->tiles[$x][$y] = new Tile($x,$y,false);
			}
		} //fill the map with wall tiles.
	}
	
	public function digDoors($tile){
		$attempts = 0;;
		if($tile->passable){
			$offsetX = 0;
			$offsetY = 0;
			while($this->tiles[$tile->x + $offsetX][$tile->y]->passable){
				 $offsetX++;
			}
			while($this->tiles[$tile->x][$tile->y + $offsetY]->passable){
				 $offsetY++;
			} //count how big the region is
			$offsetX--;
			$offsetY--;
			$numberOfDoors = rand(1,floor(($offsetX+$offsetY)/8)+1);
			$hasDoor = false;
			for($x = 0; $x <= $numberOfDoors; $x++){
				//echo "------------<br>";
				$rX = rand($tile->x,$tile->x+$offsetX);
				$rY = rand($tile->y,$tile->y+$offsetY); // get a randomm tile for within the region
				//echo "$rX : $rY : offset is : $offsetX : $offsetY<br>";
				//echo "^'s Room UpperLeft = $tile->x : $tile->y<br>";
				$tileToDig = $this->tiles[$rX][$rY];
				$direction = rand(0,3);
				while($tileToDig->passable == true){
					if ($attempts > 30){
						//echo"Too many attempts, forcing path...<br/>";
						if($hasDoor == true){
							return;
						}
						$this->forcePath($tileToDig);;
						return;
					}
					//echo "r : $rX : $rY<br/>";
					//echo "t : $tileToDig->x : $tileToDig->y<br/>";
					//echo"digging...<br/>";
					$potentialDirections = $this->findCardinalTiles($tileToDig);
					$adjecentDoorFlag = false;
					$tileToDig = $potentialDirections[$direction];


					if($tileToDig->testCode==2){
						$rX = rand($tile->x,$tile->x+$offsetX);
						$rY = rand($tile->y,$tile->y+$offsetY); // get a randomm tile for within the region
						$tileToDig = $this->tiles[$rX][$rY];
						$direction = rand(0,3);
						$attempts++;
						continue;
					}
					if(!$this->checkTileInBounds($tileToDig)){
						//echo"going off of map, restarting. A:$attempts<br/>";
						$rX = rand($tile->x,$tile->x+$offsetX);
						$rY = rand($tile->y,$tile->y+$offsetY); // get a randomm tile for within the region
						$tileToDig = $this->tiles[$rX][$rY];
						$direction = rand(0,3);
						$attempts++;
						continue;
					} //if the digging goes oob start again
					if($tileToDig->passable == false){
						$potentialDirections = $this->findCardinalTiles($tileToDig);
						$nextAdjTile = $potentialDirections[$direction];
						if($nextAdjTile->passable == false){
							//echo"Tile doesn't connect. Restarting. A:$attempts<br/>";
							$rX = rand($tile->x,$tile->x+$offsetX);
							$rY = rand($tile->y,$tile->y+$offsetY); // get a randomm tile for within the region
							$tileToDig = $this->tiles[$rX][$rY];
							$direction = rand(0,3);
							$attempts++;
							continue;
						} //if there is not a floortile on the otherside too, it can't be made into a door! so start again

					}
					$doorCheck = $this->findCardinalTiles($tileToDig);
					foreach($doorCheck as $ttile){
						if($ttile->testCode == 1){
							$adjecentDoorFlag = true;
							//echo "door here<br/>";
						}//no adjectent doors
					}
					if($adjecentDoorFlag == true){
						$rX = rand($tile->x,$tile->x+$offsetX);
						$rY = rand($tile->y,$tile->y+$offsetY); // get a randomm tile for within the region
						$tileToDig = $this->tiles[$rX][$rY];
						$direction = rand(0,3);
						$adjecentDoorFlag = false;
						$attempts++;
						continue;
					}
					//echo "normal digging...<br/>";
				}
				//var_dump($tileToDig);
				//echo"DUG!<br/>";
				$hasDoor = true;
				$tileToDig->testCode = 1;;
				$tileToDig->passable = true;
				$tiles[$tileToDig->x][$tileToDig->y] = $tileToDig;
			}
		} //if the tile is a passable tile
	}
	
	public function forcePath($tile){

			$direction = rand(0,3);
			$rTile = $this->findCardinalTiles($tile)[$direction];;
			while($rTile->passable == true){
				//var_dump();
				$neihgbors = $this->findCardinalTiles($rTile);
				//var_dump($neihgbors);
				//echo "<br/>";;
				$rTile = $neihgbors[$direction];
				//$rTile->passable = true;
				//$rTile->testCode = 2;
			}
			//echo "!!!!!!!";
			while($rTile->passable == false){
				if($this->checkTileInBounds($rTile)){
					$this->tiles[$rTile->x][$rTile->y]->passable = true;
					$this->tiles[$rTile->x][$rTile->y]->testCode = 2;
					$neihgbors = $this->findCardinalTiles($rTile);
					//var_dump($neihgbors);
					//echo "<br/>";;
					$rTile = $neihgbors[$direction];
					//$rTile->passable = true;
					//$rTile->testCode = 2;

				} else {
					$this->forcePath($tile);
					break;
				}
			} //drill a line until empty space is struck

	}
	
	public function makeKey(){
		$flag = false;
		while($flag == false){
			$roomx = rand(1,$this->w-2);
			$roomy = rand(1,$this->h-2); 
			$ttile = $this->getTile($roomx,$roomy);
			$tCount = $this->findSurroundingTiles($ttile);
			$surroundNo = 0;
			foreach($tCount as $cTile){
				if($cTile->passable == true){
					$surroundNo++;
				}
			}
			if($ttile->passable == true && $surroundNo == 8){
				$ttile->testCode = 3;
				$flag = true;
				$this->keyPlace = $ttile;
				$this->tiles[$roomx][$roomy] = $ttile;
				//var_dump($this->tiles[$roomx][$roomy]);
			}
		}
	}
	
	public function makeTeleporter(){
		$flag = false;

		while($flag == false){
			$roomx = rand(1,$this->w-2);
			$roomy = rand(1,$this->h-2); 
			$ttile = $this->getTile($roomx,$roomy);
			$tCount = $this->findSurroundingTiles($ttile);
			$surroundNo = 0;
			foreach($tCount as $cTile){
				if($cTile->passable == true){
					$surroundNo++;
				}
			}
			if($ttile->passable == true && $surroundNo == 8){
				$ttile->testCode = 4;
				$flag = true;
				$this->telePlace = $ttile;
				$this->tiles[$roomx][$roomy] = $ttile;
				//var_dump($this->tiles[$roomx][$roomy]);
			}
		}
	}
	
	public function placeObjectives(){
		//echo "xXx";
		$this->makeKey();
		$this->makeTeleporter();
		//echo "oOo";
	}
	
	public function drawText(){
		echo '<br/>';
		echo '<br/>';
		$cW = 0;
		$lNo = 0;
		echo '+';;;
		for($x = 0; $x<25; ++$x) {	
		echo $x%10;
		}
		
		foreach($this->tiles as $tileArray){
			foreach ($tileArray as $tile){
				//var_dump($tile);
				if($cW % $this->w == 0){
					echo '<br/>'.($lNo%10);
					$lNo++;
					
				}
				if($tile->passable){
					if($tile->testCode == 1){ //door
						echo '<span style="color:#4444FF">█</span>';;
					} elseif($tile->testCode == 2){ //forced
						echo '<span style="color:#4444FF">█</span>';;
					} elseif($tile->testCode == 3){ //key
						echo '<span style="color:#FF0000">█</span>';;
					} elseif($tile->testCode == 4){ //tele
						echo '<span style="color:#00FF00">█</span>';;
					} else {
						echo '<span style="color:#4444FF">█</span>';;
					}
				} else {
					echo '<span style="color:#000000">█</span>';
				}
				$cW++;
			}
		}
		//echo "<br/>";
		//foreach($this->mapCopy as $tileArray){
		//	foreach ($tileArray as $tile){
		//		//var_dump($tile);
		//		if($cW % $this->w == 0){
		//			echo '<br/>'.($lNo%10);
		//			$lNo++;
					
		//		}
		//		if($tile->passable){
		//			echo '<span style="color:#FF0000">█</span>';;
		//		} else {
		//			echo '<span style="color:#000000">█</span>';
		//		}
		//		$cW++;
		//	}
		//}
	}
	
	public static function newMaps(){
		//session_start();
		$user = $_SESSION['user'];
		
		$ttA; //references
		$xPos;
		$yPos;
		$isPass;
		$connection = connectToServer($user->username, $user->password);
		$tileInsertPrepS = $connection->prepare("INSERT INTO `iquest`.`tiles`(MapId,XPos,YPos,Passable) VALUES(?,?,?,?)");
		$tileInsertPrepS->bind_param("iiii",$mapId,$xPos,$yPos,$isPass);
		
		//make the maps (4 of them)
		for($x = 0 ; $x <= 3; $x++){
			$connection->query("INSERT INTO maps(GameID) VALUES ($user->gameId)");
		}
		$result = $connection->query("SELECT * FROM maps WHERE GameID=$user->gameId");
		while($row = $result->fetch_assoc()){
			$mapId = $row["MapID"];
			$map = new Map(150,150);
			$map->generateMap();
			//$map->drawText();
			$connection->begin_transaction();
			for($y = 0; $y < $map->h; $y++){
				for($x = 0; $x < $map->w; $x++){
					$ttA = $map->getTile($x,$y);
					$xPos = $ttA->x;
					$yPos = $ttA->y;
					$isPass = $ttA->passable?1:0;
					$tileInsertPrepS->execute();
				}
			}
			$connection->commit();
			$xPos = $map->keyPlace->x;
			$yPos = $map->keyPlace->y;
			$nResult = $connection->query("SELECT * FROM tiles WHERE XPos = $xPos AND YPos = $yPos AND MapId = $mapId");
			while($nRow = $nResult->fetch_assoc()){
				$connection->query("INSERT INTO items(TileID, ItemNo) VALUES (".$nRow['TileID'].",3)");
			} //insert key to map
			$xPos = $map->telePlace->x;
			$yPos = $map->telePlace->y;
			$nResult = $connection->query("SELECT * FROM tiles WHERE XPos = $xPos AND YPos = $yPos AND MapId = $mapId");
			while($nRow = $nResult->fetch_assoc()){
				$connection->query("INSERT INTO items(TileID, ItemNo) VALUES (".$nRow['TileID'].",4)");
			} //insert teleporter to map
		}
		//make players

	}
}
//connect
$user = $_SESSION['user'];
$connection = connectToServer($user->username, $user->password);

//Ran by the game maker
$connection->query("INSERT INTO games(GameId) VALUES (NULL)");
$user->gameId = $connection->insert_id;
Map::newMaps();

header('Location: join.php?GameID='.$user->gameId); //join the game you just made


?>