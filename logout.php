<?php

include(__DIR__."/include/dbConnect.php");
include(__DIR__."/include/user.php");

session_start();

sleep(5);

$user = $_SESSION["user"];
$connection = connectToServer($user->username, $user->password);

$connection->query("DELETE FROM players WHERE PlayerID=".$user->playerId);
$connection->query("DELETE FROM games WHERE GameID=".$user->gameId);

session_unset();
$_SESSION = array();
session_destroy();


?>

<span>You have been logged out.</span> <br/>
<span><a href="login.php">Return to login</a></span>