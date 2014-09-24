<?php

class Player {
	function __construct() {
		
	}
	
	private $eloScore = 0;
	private $gamesPlayed = 0;
	private $gamesWon = 0;
	private $gamesLost = 0;
	
	public function addGame($winner) {
		if ($winner) {
			++$gamesWon;
		} else ++$gamesLost;
		
		++$gamesPlayed;
	}
}

?>