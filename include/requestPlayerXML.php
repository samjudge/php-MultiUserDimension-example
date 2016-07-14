<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION["user"];

$connection = connectToServer($user->username, $user->password);
$xml = new SimpleXMLElement('<xml/>');

$playerResult = $connection->query("SELECT * FROM players WHERE MapId=$user->mapId");

while($row = $playerResult->fetch_assoc()){
	$playerNode = $xml->addChild('Player');
	$playerNode->addChild("PlayerID",$row["PlayerID"]);
	$playerNode->addChild("MapID",$row["MapID"]);
	$playerNode->addChild("XPos",$row["XPos"]);
	$playerNode->addChild("YPos",$row["YPos"]);
	$playerNode->addChild("HP",$row["HP"]);
}

Header('Content-type: text/xml');
print($xml->asXML());
?>