<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION["user"];

$gameId = $_GET["GameID"];

$user->gameId = $gameId;

$connection = connectToServer($user->username, $user->password);
$allMaps = $connection->query("SELECT * FROM maps WHERE GameID = $gameId");;
if($allMaps->num_rows == 0){//if said game does not exist redirect to main lobby
	header("location: ..\new.php");
}

$sMap = $allMaps->fetch_assoc();
//var_dump($sMap);
$sMapId = $sMap["MapID"];//the starting map
$startTile = $connection->query("SELECT tiles.MapID,tiles.TileID,tiles.XPos,tiles.YPos,items.ItemNo FROM tiles INNER JOIN items WHERE tiles.TileID = items.TileID AND items.itemNo = 4 AND tiles.mapID = ".$sMapId); //gets the teleporter for the map
$sPlayer = $startTile->fetch_assoc();
$user->mapId = $sPlayer["MapID"];
$user->XPos = $sPlayer["XPos"];
$user->YPos = $sPlayer["YPos"];
//echo "$user->mapId<br/>";
//echo "$user->XPos<br/>";;;;
//echo "$user->YPos<br/>";

//if(!isset($user->playerId)){
	$connection->query("INSERT INTO players(MapID,XPos,YPos,HP) VALUES ($user->mapId, $user->XPos, $user->YPos, 10)");		
	$user->playerId = $connection->insert_id;
//}
$_SESSION["user"] = $user;

//var_dump($user);

header("location: ../game.php");

?>