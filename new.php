<?php

include(__DIR__."/include/dbConnect.php");
include(__DIR__."/include/user.php");
include(__DIR__."/include/loggedInCheck.php");

$user = $_SESSION["user"];
$connection = connectToServer($user->username, $user->password);
echo $user->playerId;
if(isset($_GET['logout'])){
	if(isset($user->playerId)){
		$connection->query("DELETE FROM players WHERE PlayerID=".$user->playerId);
		$connection->query("DELETE FROM games WHERE GameID=".$user->gameId);
		session_destroy();
		header("Location: new.php?logout=1"); //seriously, wtf? php REFUSES to destory the session the first time no matter what, thus a redirect to itself.
	} else {
		session_destroy();
		header("Location: login.php"); 
	}
}


//echo connectToServer($user->username, $user->password)->get_client_info();

?>

<html>
<span>You are now logged in.</span>
<br/>
<br/>
<span><a href="rules.php">Rules</a></span>
<br/>
<span><a href="include/Map.php">Make new game</a></span>
<br/>
<span><a href="new.php?logout=1">Log Out</a></span>
<br/>
<br/>
<?php

$result = $connection->query("SELECT * FROM games");
if($result->num_rows > 0){
	echo "<span>Join a game below : </span>";

	while($row = $result->fetch_assoc()){
		$gameId = $row["GameID"];
		echo "<br/><span><a href='include/join.php?GameID=".$gameId."'>Game #".$gameId."</a></span>";
	}
} else {
	echo "<span>There are currrently no games in progress.</span>";

}
?>
</html>