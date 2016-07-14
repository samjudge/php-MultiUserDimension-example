<?php

include(__DIR__."/user.php");
include(__DIR__."/dbConnect.php");
include(__DIR__."/loggedInCheck.php");

$user = $_SESSION["user"];

$pId = $user->playerId;

$connection = connectToServer($user->username, $user->password);
$connection->query("DELETE FROM vectors WHERE PlayerID=$pId");

?>