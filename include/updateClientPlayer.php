<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION["user"];

$XPos = $_POST["XPos"];
$YPos = $_POST["YPos"];
$PlayerID = $_POST["PlayerID"];


$connection = connectToServer($user->username, $user->password);
if(isset($_POST["HP"])){
	$HP = $_POST["HP"];
	$connection->query("UPDATE players SET XPos = $XPos,YPos = $YPos, HP = $HP WHERE PlayerID = $PlayerID");
} else {
	$connection->query("UPDATE players SET XPos = $XPos,YPos = $YPos WHERE PlayerID = $PlayerID");
}


?>