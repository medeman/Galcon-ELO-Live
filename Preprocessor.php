<?php

class Preprocessor {
	function __construct($days = 1, $galcon = 'fusion') {
		if ($days > 14) {
			$this->days = 14;
		} else if ($days < 1) {
			$this->days = 1;
		} else {
			$this->days = $days;
		}
		
		if ($galcon !== 'fusion' && $galcon !== 'iphone') {
			$this->galcon = 'fusion';
		} else {
			$this->galcon = $galcon;
		}
		
		$this->fillData();
		$this->sortData();
	}
	
	private $days = 0;
	private $galcon = '';
	private $data = array();
	
	private function fillData() {
		$file = file('http://www.galcon.com/' . $this->galcon . '/dump.php?days=' . $this->days, FILE_IGNORE_NEW_LINES);
		
		foreach ($file as $value) {
			$csvLine = str_getcsv($value, "\t");
			
			if (substr_count($csvLine[0], '|') == 1) {
				$players = explode('|', $csvLine[0]);
				
				$this->data[] = (object) array(
					'winner' => explode(':', $players[0])[0],
					'loser' => explode(':', $players[1])[0],
					'timestamp' => strtotime($csvLine[2])
				);
			}
		}
	}
	
	private function sortData() {
		usort($this->data, function($a, $b) {
			return $a->timestamp - $b->timestamp;
		});
	}
	
	public function getData($json = false) {
		if ($json) {
			return json_encode($this->data);
		}
		
		return $this->data;
	}
}

$pp = new Preprocessor();

print_r($pp->getData());

?>