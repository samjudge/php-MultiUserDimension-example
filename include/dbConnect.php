<?php

function connectToServer($username,$password){
	$servername = 'p:localhost';
	$connection = new mysqli($servername, $username, $password, "iquest");
	return $connection;
}

?>