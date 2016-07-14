<?php

class user{
	public $username;
	public $password;
	public $gameId;
	public $mapId;
	public $playerId;
	public $xPos;
	public $yPos;
	
	public function __construct($username, $password){
		$this->password = $password;
		$this->username = $username;
	}
	
	public function getUsername(){
		return $this->username;
	}
}
?>