<?php

/**
 * Calculate Elo-Ratings
 * http://de.wikipedia.org/wiki/Elo-Zahl
 * 
 * @author SELFPHP OHG
 * @copyright 2009, SELFPHP OHG
 * @license BSD License
 * @link http://www.selfphp.de 
 * 
 * Minnimum Elo ist 1200
 * Maximum Elo ist 2999
 */

class EloRatingSystem {

   	/**
     * @var integer Elo-Zahl Spieler 1
     */
	private $ratingPlayerA	=	0;
	
	/**
     * @var integer Elo-Zahl Spieler 2
     */
	private $ratingPlayerB	=	0;
	
	/**
     * @var Double Spielausgang
     * 1   = Sieg
     * 0.5 = Unentschieden
     * 0   = Niederlage
     */
	private $gameResult	=	0;
	
	/**
     * @var Integer Faktor
     * 25 = bis mindestens 30 Partien
     * 15 = solange das Rating (Elo-Zahl) kleiner als 2400
     * 10 = wenn Rating (Elo-Zahl) grφίer als 2400 und mindestens 30 Partien
     * danach (nach 2400) bleibt der Wert konstant bei 10
     */
	private $kValue =	25;
	
	/**
     * @var Double Wert (+ / -) der Erwartung nach Prof. Arpad Elo
     */
	private $expectation = 0;
	
	/**
     * @var Integer Differenz der beiden Elo-Zahlen
     */
	private $difference = 0;
	
	
	/**
	 * Spielergebnis setzen
	 * 
	 * @param	integer  $playerA	Elo-Zahl von Spieler A
	 * @param	integer  $playerB	Elo-Zahl von Spieler B
	 * @param	integer  $result	Ergebnis: 1 bei Sieg von Spieler A, 0.5 bei Remis, 0 bei Niederlage von Spieler A
	 * @param	integer  $constK	Konstant K (25, 15 oder 10)
	 * 
	 * @return	bool	 true
	 */
	public function setGame( $playerA = NULL, $playerB = NULL, $result = NULL, $constK = NULL ) {
		
		if ( !empty( $playerA ) && $playerA >= 0 )
		    $this->ratingPlayerA = $playerA;
		else
		    $this->ratingPlayerA = 0;
		    
		if ( !empty( $playerB ) && $playerB >= 0 )
		    $this->ratingPlayerB = $playerB;
		else
		    $this->ratingPlayerB = 0;
		    
		if ( $result == 1 )
		    $this->gameResult = 1;
		elseif ( $result == 0.5 )
		    $this->gameResult = 0.5;
		else
		    $this->gameResult = 0;
		    
	    $this->setKValue( $constK );
	    
	    $this->setDifference();
	    
	    $this->setExpectation();
		    
		return true;
		
	}
	
	/**
	 * Setzt die Konstante K
	 * 
	 * @return	bool	 true
	 */
	private function setKValue( $value ){
	    switch ( $value ) {
	        case 10:
	            $this->kValue = 10;
	            break;
	        case 15:
	            $this->kValue = 15;
	            break;
	        default:
	            $this->kValue = 25;
	            break;
	    }
	    
	    return true;
	}
	
	/**
	 * Setzt die Differenz der beiden Elo-Zahlen der Spieler
	 * 
	 * @return	bool	 true
	 */
	private function setDifference(){
		
		$this->difference = $this->ratingPlayerA - $this->ratingPlayerB;
		
		return true;
		
	}
	
	/**
	 * Berechnet den Wert (+ / -) fόr die Erwartung des Spielausgangs nach Prof. Arpad Elo
	 * 
	 * @return	double	 Der erwartete Wert
	 */
	private function setExpectation(){
		
		if ( abs( $this->difference ) > 350)
			$diff = 350;
		else 
			$diff = $this->difference;
			
		$value = 0.5 + 1.4217 * bcpow( 10, -3, 3 ) * $diff
           -2.4336 * bcpow( 10, -7, 7 ) * $diff * abs( $diff )
           -2.5140 * bcpow( 10, -9, 9 ) * $diff * bcpow( abs( $diff ), 2 )
           +1.9910 * bcpow( 10, -12, 12 ) * $diff * bcpow( abs( $diff ), 3 );
          
        $this->expectation = round( $value, 2 );
        
        return $this->expectation;
           
	}
	
	/**
	 * Liefert die neue Elo-Zahl fόr den jeweiligen Spieler
	 * 
	 * @param	integer  $value		0 = Spieler A, 1 = Spieler B
	 * 
	 * @return	array	 			Ein Array mit den Werten
	 */
	public function getEloPlayers( $value ){
		
		if ( $value == 0 ){
		    $player = $this->ratingPlayerA;
		    $newElo['modification'] = round( ( $this->gameResult - $this->expectation ) * $this->kValue );
		}
		else{
		    $player = $this->ratingPlayerB;
		    $newElo['modification'] = -round( ( $this->gameResult - $this->expectation ) * $this->kValue );		    
		}
		    
		$newElo['difference'] = $this->difference;		
		
		$newElo['oldElo'] = $player;
		
		$newElo['newElo'] = $player + $newElo['modification'];
		
		$newElo['expectation'] = $this->expectation;
		
		return $newElo;
		
	}

}

?>