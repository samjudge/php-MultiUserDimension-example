<?php
include(__DIR__."/include/user.php");
include(__DIR__."/include/loggedInCheck.php");
include(__DIR__."/include/dbConnect.php");

if(!(isset($_SESSION['user']->gameId))){
	header("Location: new.php");
}

?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<!--hidden stuff-->
<input type="hidden" id="clientPlayerId" value="<?php echo $_SESSION["user"]->playerId?>"/>
<img id="marine-img-blue" src="images/marine.png" style="display:none"/>
<img id="marine-img-green" src="images/marine-green.png" style="display:none"/>
<img id="marine-img-red" src="images/marine-red.png" style="display:none"/>
<img id="wall-img" src="images/walltile.png" style="display:none"/>
<img id="death-img" src="images/death-img.png" style="display:none"/>
<script src="include/Game.js"></script>
<div id="sectionNav">
	<div id="accountBar">
	<span><a href="new.php?logout=1">Log Out</a></span>
	</div>
</div>
<div id="sectionGame">
	<canvas id="gameWindow" width="750" height="750">
	</canvas>
	<div id="statusBar">
		<span id="currentHealth"></span>/10 HP<br/>
		<span id="isRespawning"></span><br/>
		Score : <span id="score">0</span><br/>
	</div>
</div>
<div id=chat>
</div>
</body>
</html>