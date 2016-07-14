<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION["user"];

$connection = connectToServer($user->username, $user->password);
$xml = new SimpleXMLElement('<xml/>');

$vectorResult = $connection->query("SELECT vectors.VectorID, players.XPos, players.YPos,vectors.TargetX,vectors.TargetY FROM vectors INNER JOIN players WHERE vectors.MapID = $user->mapId AND vectors.PlayerID=players.PlayerID");
while($row = $vectorResult->fetch_assoc()){
	$playerNode = $xml->addChild('Vector');
	$playerNode->addChild("XPos",$row["TargetX"]);
	$playerNode->addChild("YPos",$row["TargetY"]);
}

Header('Content-type: text/xml');
print($xml->asXML());
?>