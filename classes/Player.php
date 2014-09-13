<?php

class Player {
	function __construct() {
		
	}
	
	private $eloScore = 0;
	private $gamesWon = 0;
	private $gamesLost = 0;
	
	function __get($property) {
		if ($property === 'gamesPlayed')
			return $this->gamesWon + $this->gamesLost;
	}
}

?>
