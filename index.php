<?php

ob_start("ob_gzhandler");
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

function __autoload($class) {
	include 'classes/' . $class . '.php';
}

$cfg = (object) array(
	'days' => 1,
	'game' => 'fusion'
);

if (isset($_GET['days'])) {
	$temp = (int) $_GET['days'];
	
	if (!($temp < 1 || $temp > 14))
		$cfg->days = $temp;
}

if (isset($_GET['game'])) {
	$temp = (string) $_GET['game'];
	
	if ($temp == 'fusion' || $temp == 'iphone')
		$cfg->game = $temp;
	
	if ($temp == 'igalcon')
		$cfg->game = 'igalcon';
}

$pp = new Preprocessor($cfg->days, $cfg->game);
$elo = new EloRatingSystem();

$players = array();

foreach ($pp->getData() as $value) {
	if (!isset($players[$value->winner])) {
		$players[$value->winner] = new Player();
	}
	if (!isset($players[$value->loser])) {
		$players[$value->loser] = new Player();
	}
	
	if ($players[$value->winner]->gamesPlayed <= 30)
		$kValue = 25;
	else if ($players[$value->winner]->eloScore < 2400)
		$kValue = 15;
	else
		$kValue = 10;
	
	++$players[$value->winner]->gamesWon;
	++$players[$value->loser]->gamesLost;
	
	$elo->setGame($players[$value->winner]->eloScore, $players[$value->loser]->eloScore, 1, $kValue);
	
	$players[$value->winner]->eloScore = $elo->getEloPlayers(0)['newElo'];
	$players[$value->loser]->eloScore = $elo->getEloPlayers(1)['newElo'];
}

if (isset($_GET['dt'])) {
	$dtPlayers = array('data' => array());
	
	foreach ($players as $key => $value) {
		$dtPlayers['data'][] = array(
			'player' => $key,
			'eloScore' => $value->eloScore,
			'gamesWon' => $value->gamesWon,
			'gamesLost' => $value->gamesLost
		);
	}
	
	echo json_encode($dtPlayers);
} else
	echo json_encode($players);

?>