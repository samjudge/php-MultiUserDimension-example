<?php

include(__DIR__."/include/user.php");
include(__DIR__."/include/dbConnect.php");

session_start();

if(isset($_SESSION['user'])){
	header("Location: new.php");
}

if (isset($_POST['username']) && isset($_POST['password'])) {
	$username = $_POST['username'];
	$password = $_POST['password'];
	$user = new user($username, $password);
	$connection;
	//$connection = connectToServer($username,$password);
	if (!connectToServer($username,$password)){
		header("Location: login.php?error=1"); //if unable to connect, send user back to login page
		die();
	}
	
	$_SESSION['user'] = $user;
	
	header("Location: new.php"); //redirect to hero select
	
}

?>

<html>
<head>
<title>Internet Quest</title>
</head>
<body>
<h1>Internet Quest<img src="images/marine-face.png"></h1>
<?php
if(isset($_GET['error'])){
	if($_GET['error'] == 1){
		echo '<span style="color:#FF0000;">Your login credentials are incorrect.</span><br/><br/>';
	}
}
?>
<form method="POST">
Username : <br/><input type="text" name="username"/><br/>
Password : <br/><input type="password" name="password"/><br/><br/>
<input type = "submit" value="Log In"/><br/>
</form>
<br/>
<span style="font-size:75%">
Internet Quest is a private multi-user dungeon (MUD).
If you would like to join, please email me at sam.m.w.judge@googlemail.com with a suggested username
(please don't send a suggested password) with the reasons you would like to join and await approval.
</span>
</body>
</html>