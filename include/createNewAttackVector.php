<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION["user"];

$Xd = $_POST["Xd"];
$Yd = $_POST["Yd"];

$connection = connectToServer($user->username, $user->password);
$connection->query("INSERT INTO vectors(PlayerID,MapID,TargetX,TargetY) VALUES ($user->playerId,$user->mapId,$Xd,$Yd)");

?>