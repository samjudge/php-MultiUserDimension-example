<?php

include(__DIR__."/loggedInCheck.php");
include(__DIR__."/dbConnect.php");

$user = $_SESSION['user'];
$connection = connectToServer();
$xml = new SimpleXMLElement('<xml/>');

if ($result = $connection->query('SELECT UpdateMap,UpdateVectors FROM updates WHERE GameId='.$user->$gameId)){
	while($row = $result->fetch_assoc()){
		$xml->addChild('mapUpdate',$row['UpdateMap']);
		$xml->addChild('vectorUpdate',$row['UpdateVectors']);
	}
} //create an XML document based on the updates table



Header('Content-type: text/xml');
print($xml->asXML());
?>