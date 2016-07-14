<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION['user'];
//var_dump($user);
$connection = connectToServer($user->username, $user->password);
$xml = new SimpleXMLElement('<xml/>');
$items;
$itemsResult = $connection->query("SELECT * FROM items");
while($iRow = $itemsResult->fetch_assoc()){
		unset($iDataArray);
		$iDataArray[] = $iRow["TileID"];
		$iDataArray[] = $iRow["ItemNo"];
		$items[] = $iDataArray;
}//get all items so that they can be associated with the tiles below. This would  be more efficent with joins probably but meh
$userMaps = $connection->query('SELECT MapId FROM maps WHERE maps.GameId='.$user->gameId);
while($mRow = $userMaps->fetch_assoc()){
	$mapNode = $xml->addChild('Map');
	$mapNode->addAttribute('Id',$mRow["MapId"]);
	$result = $connection->query('SELECT tiles.TileID, tiles.XPos, tiles.YPos, tiles.Passable, tiles.MapId, maps.GameId FROM tiles INNER JOIN `maps` on maps.MapId = tiles.MapId WHERE maps.MapId='.$mRow["MapId"].' AND maps.GameId='.$user->gameId);
	while($tRow = $result->fetch_assoc()){
			$tileNode = $mapNode->addChild('Tile');
			$tileNode->addAttribute('XPos',$tRow["XPos"]);
			$tileNode->addAttribute('YPos',$tRow["YPos"]);
			$tileNode->addChild('Passable',$tRow['Passable']);
			$testCount = 0;
			foreach($items as $key=>$val){
				if($val[0] == $tRow["TileID"]){
					$tileNode->addChild('ItemSpecial',$items[$key][1]);
				}
			}
	}
}
//load in tiles from database based on user's current map and game

Header('Content-type: text/xml');
print($xml->asXML());
?>