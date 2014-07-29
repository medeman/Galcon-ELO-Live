<?php
ob_start("ob_gzhandler");
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (isset($_GET['days'])) {
	$days = (int) $_GET['days'];
	
	if ($days > 14) $days = 14;
	if ($days < 1) $days = 1;
	
} else {
	$days = 1;
}

if (isset($_GET['galcon'])) {
	$galcon = $_GET['galcon'];
	
	switch ($galcon) {
		case 'fusion': case 'iphone':
			break;
		case 'igalcon':
			$galcon = 'iphone';
			break;
		default:
			$galcon = 'fusion';
			break;
	}
} else $galcon = 'fusion';

if (isset($_GET['player'])) {
	$player = strtolower($_GET['player']);
} else $player = false;

$dumpData = file('http://www.galcon.com/' . $galcon . '/dump.php?days=' . $days, FILE_IGNORE_NEW_LINES);

$dumpArray = array();

$wins = 0;
$losses = 0;

foreach ($dumpData as $value) {
	$csvLine = str_getcsv($value, "\t");
	
	if (substr_count($csvLine[0], '|') == 1) {
		$players = explode('|', $csvLine[0]);
		
		$game = (object) array(
			'winner' => explode(':', $players[0])[0],
			'loser' => explode(':', $players[1])[0],
			'datetime' => $csvLine[2]
		);
		
		if ($player) {
			if (strtolower($game->winner) == $player) {
				++$wins;
				$dumpArray[] = $game;
			}
			
			if (strtolower($game->loser) == $player) {
				++$losses;
				$dumpArray[] = $game;
			}
		} else {
			$dumpArray[] = $game;
		}
	}
}

if ($player && isset($_GET['wl'])) {
	if ($losses == 0) {
		echo $wins;
	} else {
		echo $wins / $losses;
	}
} else echo json_encode($dumpArray);

?>