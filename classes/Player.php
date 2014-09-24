<?php

class Player {
	function __construct() {
		
	}
	
	public $eloScore = 1200;
	public $gamesWon = 0;
	public $gamesLost = 0;
	
	function __get($property) {
		if ($property === 'gamesPlayed')
			return $this->gamesWon + $this->gamesLost;
	}
}

?>
