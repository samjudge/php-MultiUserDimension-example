<?php
include(__DIR__."/include/loggedInCheck.php");
?>

<html>
<h1>Rules</h1>
<h2>Game Rules</h2>
<span>Internet Quest is a private Multi-User Dungeon crawl. To create a new game,
you must be in a party of at least 4 people.</span>
<br/>
<span>Players are either human or Dingleberry, selected at random.</span>
<br/>
<span>The game consists of 4 random floors and a central HUB.</span>
<br/>
<span>Only human players can enter the central HUB.</span>
<br/>
<span>Humans can take the elevator from the central HUB to the elevator on any floor they like.</span>
<br/>
<span>The Dingleberry can teleport to any floor it wants, although the position will be random.</span>
<br/>
<span>The goal of the Humans is to find 4 keys (one from each level) and return them to
the central HUB in a certain amount of time.</span>
<br/>
<span>The goal of the Dingleberry is to kill the Human players, and prevent them from escape.</span>
<br/>
<span>When a human dies, they will respawn after 2 minutes in the central hub. If they were carrying a key, it will be dropped.</span>
<br/>
<span>When the game begins, only 1 human is spawned. Each other player will be spawned 30 seconds after this one until all Humans have spawned.</span>
<br/>
<span>The Dingleberry is a very strong special melee class with special rules associated with it.</span>
<br/>
<ul>
<li>A Dingleberry regenerates HP over time.</li>
<li>The Dingleberry knows how many human players there are on each floor.</li>
<li>The Dingleberry knows what all the maps look like without having to explore.</li>
<li>When the game begins, the Dingleberry is given 1 minute to look over the floors.</li>
<li>The Dingleberry will respawn after only 1 minute (In a random area).</li>
</ul>
<h2>Controls</h2>
<span>'WASD' to move</span>
<br/>
<span>Left-click on a tile to shoot.</span>
<br/>
<span>Right-click to use current skill.</span>
<br/>
<span>'R' to reload.</span>
<br/>
<h2>Skills</h2>
<h3>Dingleberry</h3>
<span>Has 32 HP and deals 8 Damage each attack. Melee.</span>
<br/>
<span>Camo - (60s Cooldown) Assume another player's name and model until your next attack (And use their name in chat).</span>
<br/>
<span>Sense - (30s Cooldown) Gives which direction the nearest human is at.</span>
<br/>
<span>Dimensional Jump - (10s Cooldown) Teleports the Dingleberry randomly to another floor.</span>
<br/>
<span>Dig - (10s Cooldown) Remove a wall tile and replace it with floor.</span>
<br/>
<span>Stealth (Passive) - Will not aggro neutral units.</span>
<br/>
<h3>Marine</h3>
<span>Has 12 HP and deals 4 Damage each attack. Ranged.</span>
<br/>
<span>Stim - (30s Cooldown) Deal double damage for your next 3 attacks at the cost of half your HP.</span>
<br/>
<span>Teleport - (180s Cooldown) Teleports the player back to the HUB.</span>
<br/>
<span>Radar (Passive) - Reveal all units that have moved in a 5 tile radius.</span>
<br/>
<br/>
<span><a href="new.php">Back</a></span>
</html>