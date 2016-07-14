//get the maps and their tiles

var mapTiles = [];
var mapXMLNodes = [];
var mapXMLHttpRequest;

var gameId; //I'll need to set a hidden field to get this value

var tX = 75;
var tY = 75;

var clientPlayerId = document.getElementById("clientPlayerId").value; //get the client's playerID
var playerX = 0;
var playerY = 0;

var playerHP;
var playerHPChangedFlag;
var respawning = false;

var playerTargetX = 0;
var playerTargetY = 0;

var playerMapId = 0;
var zerothMapId = 0; //playerMapId MOD this equals the index of the player map locally

var playerXMLNodes = [];
var playerXMLHttpRequest;

var gLoopInterval;

var playerAJAXUpdate;

var initRequests = new XMLHttpRequest();

playerAJAXUpdate = new XMLHttpRequest();


playerXMLHttpRequest = new XMLHttpRequest();

mapXMLHttpRequest = new XMLHttpRequest();

var vectorXMLRequest = new XMLHttpRequest();

var randColor;

isPassable = function (tileNode){
	var p = tileNode.childNodes[0].childNodes[0].data;
	if(p == "1"){
		//alert("!");
		return true;
	} else {
		
		return false;
	}
};

isItemNode = function (tileNode){
	var specialNode = tileNode.getElementsByTagName("ItemSpecial");
	if(specialNode.length != 0){
		//alert(specialNode[0].childNodes[0].data);
		return specialNode[0].childNodes[0].data;
	}
	return 0;
	
}

castRays = function(r){
	var viewTiles = [];
	var vPX = playerX-9;
	var vPY = playerY-9; //top left screen origin point
	var ePX = Number(playerX)+12;
	var ePY = Number(playerY)+7;
	for(var x = vPX; x < ePX; x++){
		for(var y = vPY; y < ePY; y++){
			if(x >= 0 && y >= 0 && y<150 && x<150){ //between 0 and 149
				mapTiles[playerMapId%zerothMapId][y][x].setAttribute("Vis","0");
				viewTiles.push(mapTiles[playerMapId%zerothMapId][y][x]);
			}
		}
	} //get all the tiles around the player
	for(var i = 0; i < viewTiles.length; i++){
		var infGradFlag = false;
		var oX = viewTiles[i].getAttribute("XPos");
		var oY = viewTiles[i].getAttribute("YPos");
		var gradient = 0;
		if(playerX - oX != 0){
			gradient = (playerY - oY)/(playerX - oX);
		} else {
			infGradFlag = true;
		} //if it's a straight line up or down
		var cX = playerX;
		var cY = playerY;
		
		var dX = playerX;
		var dY = playerY;
		
		var symbolX;
		var symbolY;
		//X is less than PlayerX, then tile is to the left
		if(oX < playerX){
			symbolX = 0;//zero for minus, one for plus
		} else {
			symbolX = 1;
		}
		//Y is less than PlayerY, then tile is above
		if(oY < playerY){
			symbolY = 0;
		} else {
			symbolY = 1;
		}
		while(cX != oX || cY != oY){ //iterate until the target tile is reached
			if(infGradFlag == true){
				if(symbolY == 1){
					cY = Number(cY) + 0.1;
				} else {
					cY = Number(cY) - 0.1;
				}
				cX = playerX;
				
			} else {
				var multiplier = 1;
				while(gradient*multiplier > 1){
					multiplier = multiplier/2;
				}//if the gradient is more than 1, take smaller steps to make sure that vision does not skip tiles, phasing through them
				
				if(symbolX == 1){
					cX = Number(cX) + 0.1;
				} else {
					cX = Number(cX) - 0.1;
				}
				//if(symbolY == 1){				
				cY = Number(cY) + Number(gradient)/10;
				//} else {
				//	cY = Number(cY) - Number(gradient);
				//}

			}
			var visTile = mapTiles[playerMapId%zerothMapId][Math.round(cY)][Math.round(cX)];
			mapTiles[playerMapId%zerothMapId][Math.round(cY)][Math.round(cX)].setAttribute("Vis","1");
			if(!isPassable(visTile)){ //if the tile is a wall
				break;
			}
			//testing
			//var canvasNode = document.getElementById("gameWindow");
			//var canvasContext = canvasNode.getContext("2d");
			//canvasContext.fillStyle = "#FF0000";
			//context.strokeStyle = '#FF0000';
			//canvasContext.beginPath();
			//canvasContext.moveTo((dX-vPX)*36+18,(dY-vPY)*72-36);
			//canvasContext.lineTo((cX-vPX)*36+18,(cY-vPY)*72-36);
			//canvasContext.stroke();
		}
		
		
	}
	
	
}

drawMap = function(){
	//below is for testing (for when the map data has been returned)
	var canvasNode = document.getElementById("gameWindow");
	var canvasContext = canvasNode.getContext("2d");
	var vPX = playerX-5;
	var vPY = playerY-5;//view port x + y, this would be the player's position (-25, if say the w and h of the vp was 50)
	var vPW = 20;
	var vPH = 10; //view port width and height
	

	
	var marineImageBlue = document.getElementById("marine-img-blue");
	var marineImageGreen = document.getElementById("marine-img-green");
	var marineImageRed = document.getElementById("marine-img-red");
	var wallImage = document.getElementById("wall-img");
	var deadImage = document.getElementById("death-img");
	
	canvasContext.fillStyle = "#000000";
	canvasContext.fillRect(0,0,750,750);//I see a red canvas and I want it painted black
	
	castRays(4); //cast rays from the player's position in radius X (4 as writing this)
	
	for(var x = 0; x < mapTiles[playerMapId%zerothMapId].length; x++){
		for(var y = 0; y < mapTiles[playerMapId%zerothMapId][x].length; y++){
			if( x >= vPX && x <= vPX+vPW){
				if( y >= vPY && y <= vPY+vPH){
					var cTile = mapTiles[playerMapId%zerothMapId][y][x];
					var vis = 0;
					if(cTile.hasAttribute("Vis")){
							vis = cTile.getAttribute("Vis");
					}
					if(vis == 1){
						if((itemCode = isItemNode(cTile)) != 0){
							if(itemCode == 3) {
								canvasContext.fillStyle = "#FF0000";
								canvasContext.fillRect((x-vPX)*tX,(y-vPY)*tY,tX,tY);
							}
							if(itemCode == 4) {
								canvasContext.fillStyle = "#00FF00";
								canvasContext.fillRect((x-vPX)*tX,(y-vPY)*tY,tX,tY);
							}
						} else if(isPassable(cTile)){
							canvasContext.fillStyle = "#4444FF";
							canvasContext.fillRect((x-vPX)*tX,(y-vPY)*tY,tX,tY);
						} else {
							canvasContext.fillStyle = "#444444";
							canvasContext.drawImage(wallImage,(x-vPX)*tX,(y-vPY)*tY,tX,tY);
						}
					} else {
						canvasContext.fillStyle = "#000000";
						canvasContext.fillRect((x-vPX)*tX,(y-vPY)*tY,tX,tY);
					}
					
				}
			}
		}

	}//draw all tiles

	for(var i = 0;i<playerXMLNodes.length;i++){
		//for testing
		marineImageNum = playerXMLNodes[i].getElementsByTagName("PlayerID")[0].childNodes[0].data;
		marineImageNum = marineImageNum%3;
		//end testing
		tpX = playerXMLNodes[i].getElementsByTagName("XPos")[0].childNodes[0].data;
		tpY = playerXMLNodes[i].getElementsByTagName("YPos")[0].childNodes[0].data;
		var hp = playerXMLNodes[i].getElementsByTagName("HP")[0].childNodes[0].data;
		var cTile = mapTiles[playerMapId%zerothMapId][tpY][tpX];
		if(cTile.hasAttribute("Vis")){
			vis = cTile.getAttribute("Vis");
		}
		if(vis == 1){
			if(hp <= 0){
				canvasContext.drawImage(deadImage,(tpX-vPX)*tX,(tpY-vPY)*tY,tX,tY);
			} else {
				//also for testing, images will be stored on server side eventually once I scale up
				switch(marineImageNum){
					case 0:
					canvasContext.drawImage(marineImageBlue,(tpX-vPX)*tX,(tpY-vPY)*tY,tX,tY);
					break;
					case 1:
					canvasContext.drawImage(marineImageGreen,(tpX-vPX)*tX,(tpY-vPY)*tY,tX,tY);
					break;
					case 2:
					canvasContext.drawImage(marineImageRed,(tpX-vPX)*tX,(tpY-vPY)*tY,tX,tY);
					break;
				}
				//end testing
			}
		}
	} //draw all players (over tiles)
	//canvasContext.drawImage(marineImage,(playerX-vPX)*tX,(playerY-vPY)*tY,tX,tY);
	//draw the client player
	
	//show hp
	healthText = document.getElementById("currentHealth");
	healthText.innerHTML = playerHP;

}

mapXMLHttpRequest.onreadystatechange=function(){
	if(mapXMLHttpRequest.readyState == 4){
		mapXMLNodes = mapXMLHttpRequest.responseXML.getElementsByTagName("Map");
		zerothMapId = mapXMLNodes[0].getAttribute("Id");
		for(var i = 0;i < mapXMLNodes.length;i++){
			var iMapTiles = mapXMLNodes[i];//contains a map node
			var xTileArray = [];
			var yTileArray = [];
			for(var x = 0; x < iMapTiles.childNodes.length;x++){ //for each child of the map node
				var cTile = iMapTiles.childNodes[x];
				var XPos = cTile.getAttribute("XPos");
				var YPos = cTile.getAttribute("YPos");
				if(XPos == 0 && YPos != 0){ //if it's a new row (and not the first)
					yTileArray.push(xTileArray);//add the row to the yArray
					xTileArray = [];//clear the row
				}
				xTileArray.push(cTile);//contains the tile
//				mapTiles[i][XPos][YPos] = cTile; //where 'i' is the map number,x is the tiles xpos and y is the tiles ypos
			}
			yTileArray.push(xTileArray);
			mapTiles.push(yTileArray);//all the rows of cells to the map array
		} //for every map returned
		
		//window.clearInterval(gLoopInterval);;
		
		drawMap();
		gLoopInterval = window.setInterval(gameLoop,150);
		
	}

	
}; //sets up the map

playerXMLHttpRequest.onreadystatechange=function(){
	if(playerXMLHttpRequest.status == 200){
		//alert("A player has left and the game has been abandoned.");
		//window.location.replace("new.php");
	}
	
	if(playerXMLHttpRequest.readyState == 4){
		
		playerXMLNodes = playerXMLHttpRequest.responseXML.getElementsByTagName("Player");
		for(var i = 0;i<playerXMLNodes.length;i++){
			var pIDNode = playerXMLNodes[i].getElementsByTagName("PlayerID");
			var pID = pIDNode[0].childNodes[0].data;
			if(pID == clientPlayerId){
				playerX = playerXMLNodes[i].getElementsByTagName("XPos")[0].childNodes[0].data;
				playerY = playerXMLNodes[i].getElementsByTagName("YPos")[0].childNodes[0].data;
				playerMapId = playerXMLNodes[i].getElementsByTagName("MapID")[0].childNodes[0].data;
				playerHP = playerXMLNodes[i].getElementsByTagName("HP")[0].childNodes[0].data;
			}
		}
	}
}

clientPlayerUpdate = function(x, y){ //gotta throttle somehow
	if(isPassable(mapTiles[playerMapId%zerothMapId][y][x])){
		playerAJAXUpdate.open("POST","include/updateClientPlayer.php",true);
		playerAJAXUpdate.setRequestHeader("Content-type","application/x-www-form-urlencoded");
		if(playerHPChangedFlag == true){
			playerAJAXUpdate.send("XPos="+x+"&YPos="+y+"&PlayerID="+clientPlayerId+"&HP="+playerHP);
			playerHPChangedFlag = false;
		} else {
			playerAJAXUpdate.send("XPos="+x+"&YPos="+y+"&PlayerID="+clientPlayerId);
		}
	}
}


getPlayerData = function(){
	playerXMLHttpRequest.open("POST","include/requestPlayerXML.php",true);
	playerXMLHttpRequest.send();
}

gameLoop = function(){
	if(playerHP <= 0 && respawning == false){
		//respawning
		respawnTimer = window.setInterval(updateSpawnTimer,1000);
		window.setTimeout(respawn,respawnClock); //respawn if dead in respwn clock ms
		respawning = true;
	}
	checkVectors();
	getPlayerData();
	drawMap();
}

respawn = function(){
	playerHP = 10;
	playerHPChangedFlag = true;
	var spawnTile = findSpawnTile(); 
	var x = spawnTile.getAttribute("XPos");
	var y = spawnTile.getAttribute("YPos");
	clientPlayerUpdate(x,y);
	respawning = false;
}


var respawnClock = 30000;
var respawnTimer;

updateSpawnTimer = function(){
	var respawnNode = document.getElementById("isRespawning");
	if(respawning == true){
			respawnClock = respawnClock-1000;
			respawnClockInS = Math.floor(respawnClock/1000);
			respawnNode.innerHTML = "You will respawn in " + respawnClockInS + " seconds...";
	} else {
		respawnClock = 30000;
		respawnNode.innerHTML = "";
		clearInterval(respawnTimer);
	}
}

shoot = function(e){
	if(playerHP <= 0){
		return;
	}
	var canvasNode = document.getElementById("gameWindow");
	var rect = canvasNode.getBoundingClientRect();
	var tarX = Math.floor((e.pageX - rect.left)/68) + Number(playerX) - 5;
	var tarY = Math.floor((e.pageY - rect.top)/68) + Number(playerY) - 5;
	var createVectorXMLRequest = new XMLHttpRequest();
	var infGradFlag = false;
	var g = 0;
	if(playerX - tarX != 0){
		g = (playerY - tarY)/(playerX - tarX);
	} else {
		infGradFlag = true;
	}

	var hasHit = false;
	var cX = playerX;
	var cY = playerY;
	
	if(tarX < playerX){
		symbolX = 0;//zero for minus, one for plus
	} else {
		symbolX = 1;
	}
	if(tarY < playerY){
		symbolY = 0;
	} else {
		symbolY = 1;
	}
	
	while(hasHit == false){
		if(infGradFlag == true){
			if(symbolY == 1){
				cY = Number(cY) + 0.1;
			} else {
				cY = Number(cY) - 0.1;
			}
			cX = playerX;	
		} else {
			if(symbolX == 1){
				cX = Number(cX) + 0.1;
				cY = Number(cY) + Number(g)/10;
			} else {
				cX = Number(cX) - 0.1;
				cY = Number(cY) - Number(g)/10;
			}
			
		}
		for(var i = 0;i<playerXMLNodes.length;i++){
			var hp = playerXMLNodes[i].getElementsByTagName("HP")[0].childNodes[0].data;
			
			if(hp <= 0){
				continue; //dont hit dead players
			}
			
			var tpX = playerXMLNodes[i].getElementsByTagName("XPos")[0].childNodes[0].data;
			var tpY = playerXMLNodes[i].getElementsByTagName("YPos")[0].childNodes[0].data;
			
			if (Math.round(cY) == tpY && Math.round(cX) == tpX){
				if(tpX == playerX && tpY == playerY){
					continue;
				}
				hasHit=true;//hit a player
				cY = Math.round(cY);
				cX = Math.round(cX);
			}
		}
		if(!isPassable(mapTiles[playerMapId%zerothMapId][Math.round(cY)][Math.round(cX)])){
			hasHit=true;//hit a wall
			cY = Math.round(cY);
			cX = Math.round(cX);
		}
	}
	createVectorXMLRequest.open("POST","include/createNewAttackVector.php");
	createVectorXMLRequest.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	createVectorXMLRequest.send("Xd="+cX+"&Yd="+cY);
	window.setTimeout(removeVectors,150);
}

removeVectors = function(){
	var removeVectorXMLRequest = new XMLHttpRequest();
	removeVectorXMLRequest.open("POST","include/removeAttackVectors.php");
	removeVectorXMLRequest.send();
} //delete's this player's attacks.

checkVectors = function(){
	vectorXMLRequest.open("POST","include/requestAttackVectorsXML.php");
	vectorXMLRequest.send();
	//if you've been hit
}

vectorXMLRequest.onreadystatechange = function(){
	var vPX = playerX-5;
	var vPY = playerY-5;//view port x + y, this would be the player's position (-25, if say the w and h of the vp was 50)
	var vPW = 20;
	var vPH = 10; //view port width and height
	if(vectorXMLRequest.readyState == 4){
		var vectorNodes = vectorXMLRequest.responseXML.getElementsByTagName("Vector");
		var canvasNode = document.getElementById("gameWindow");
		var canvasContext = canvasNode.getContext("2d");
		for(var i = 0; i < vectorNodes.length; i++){
			if(playerHP <= 0){
				continue;
			}
			var xP = vectorNodes[i].getElementsByTagName("XPos")[0].childNodes[0].data;
			var yP = vectorNodes[i].getElementsByTagName("YPos")[0].childNodes[0].data;
			if(playerX == xP && playerY == yP){
				canvasContext.fillStyle = "#FF0000"; //red for a hit (on this player)
				playerHP--;//if the player has been hit (game logic here later)
				playerHPChangedFlag = true;
				clientPlayerUpdate(playerX,playerY);
			} else {
				canvasContext.fillStyle = "#FFFF00"; //yellow for a miss
			}
			var cTile = mapTiles[playerMapId%zerothMapId][yP][xP];

			canvasContext.fillRect((xP-vPX)*tX,(yP-vPY)*tY,tX,tY);
		}
	}
}

keyControls = function(e){
	if(playerHP <= 0){
		return;
	}
	playerTargetX = playerX;
	playerTargetY = playerY;
	switch(e.keyCode){
		case 33:
			playerTargetX++;
			playerTargetY--;
			break;
		case 34:
			playerTargetX++;
			playerTargetY++;
			break;
		case 35:
			playerTargetX--;
			playerTargetY++;
			break;
		case 36:
			playerTargetX--;
			playerTargetY--;
			break;
		case 37://left
			playerTargetX--;
			break;
		case 38://up
			playerTargetY--;
			break;
		case 39://right
			playerTargetX++;
			break;
		case 40://down
			playerTargetY++;
			break;
	}
	clientPlayerUpdate(playerTargetX,playerTargetY);
	
}

initRequests.onreadystatechange = function() {
	if(initRequests.readyState == 4){
		playerXMLNodes = initRequests.responseXML.getElementsByTagName("Player");
		for(var i = 0;i<playerXMLNodes.length;i++){
			var pIDNode = playerXMLNodes[i].getElementsByTagName("PlayerID");
			var pID = pIDNode[0].childNodes[0].data;
			if(pID == clientPlayerId){
				playerX = playerXMLNodes[i].getElementsByTagName("XPos")[0].childNodes[0].data;
				playerY = playerXMLNodes[i].getElementsByTagName("YPos")[0].childNodes[0].data;
				playerMapId = playerXMLNodes[i].getElementsByTagName("MapID")[0].childNodes[0].data;
				playerHP = playerXMLNodes[i].getElementsByTagName("HP")[0].childNodes[0].data;
			}
		}
		mapXMLHttpRequest.open("POST","include/requestMapXML.php",true);
		mapXMLHttpRequest.send();
	}
}

findSpawnTile = function(){
	for(var x = 0; x < mapTiles[playerMapId%zerothMapId].length; x++){
		for(var y = 0; y < mapTiles[playerMapId%zerothMapId][x].length; y++){
			var cTile = mapTiles[playerMapId%zerothMapId][x][y];
			if(isItemNode(cTile) == 4){
				return cTile;
			}
		}
	}
}




window.onload = function(){
	randColor = Math.floor(Math.random()*3)%3;
	getPlayerData();
	initRequests.open("POST","include/requestPlayerXML.php",true);
	initRequests.send();
	var canvasNode = document.getElementById("gameWindow");
	window.addEventListener("keydown",keyControls, true);
	canvasNode.addEventListener("mousedown",shoot,true)
}



