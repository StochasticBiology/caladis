<?php

// Mathematical utilities
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

	//------------------------------------------------------------------
	// Ceil NN 
	// Rounds number UP to the next nice number (..1,2,5,10,20,50,..)
	//------------------------------------------------------------------
	
	function ceil_nn( $number){ 
		$dp = floor( 1 - log10( abs( $number)));
		$sigFig =  ceil( $number * pow( 10, $dp));
		
		if( $sigFig == 3 || $sigFig == 4 ) $sigFig = 5; 
		if( $sigFig > 5 ) $sigFig = 10;
		
		return $sigFig / pow( 10, $dp); 
	}
	
	
	
	//------------------------------------------------------------------
	// Ceil SF 
	// Rounds number UP to specified number of significant figures
	//------------------------------------------------------------------
	
	function ceil_sf( $number, $sf){ 
		$dp = floor( $sf - log10( abs( $number)));
		return ceil( $number * pow( 10, $dp)) / pow( 10, $dp); 
	}
	
	
	
	//------------------------------------------------------------------
	// Mean
	//------------------------------------------------------------------
	
	function mean( $data){
		return array_sum( $data) / count( $data); 
	}
	
	
	
	//------------------------------------------------------------------
	// Round SF 
	// Rounds number to specified number of significant figures
	//------------------------------------------------------------------
	
	function round_sf( $number, $sf){ 
		$dp = floor( $sf - log10( abs( $number)));
		return round( $number, $dp); 
	}
	
	
	
	//------------------------------------------------------------------
	// Percentile 
	// $data must be sorted.
	//------------------------------------------------------------------	

	function percentile( $data, $percent){		
		$k = ( count( $data) - 1) * $percent;
		$f = floor( $k);
		$c = ceil( $k);
		
		if( $f == $c) return $data[$k];
			
		$d0 = $data[$f] * ($c-$k);
		$d1 = $data[$c] * ($k-$f);
		return ( $d0 + $d1 );
	}

//--- 
// median & IQR
//---

function median($data)
{
  $tmpdata = $data;
  $sats = array(0,0,0);
  sort($tmpdata);
           
  $n = count($tmpdata);
  $h = intval($n / 2);
  $h1 = intval($n/4);
  $h2 = intval(3*$n/4);

  $stats[0] = $tmpdata[$h1];
  $stats[1] = $tmpdata[$h];
  $stats[2] = $tmpdata[$h2];

  return $stats;
}

	
	//----------------------------
        // Sanity Check for SD
        // split array in two. if sd's from each half differ 
        // dramatically, flag as dodgy.
        //---------------------

	function sd_sanity( $data){
		 if(count( array_unique( $data)) == 1 ) return 2;

		 $data1 = array_slice($data, 0, count($data)/2);
		 $data2 = array_slice($data, count($data)/2, count($data));

		 $result1 = sqrt( array_sum( array_map( "sd_diff_squared", $data1, array_fill(0,count( $data1), ( array_sum( $data1) / count( $data1))))) / ( count( $data1)-1));
		 $result2 = sqrt(array_sum( array_map( "sd_diff_squared", $data2, array_fill(0,count( $data2), ( array_sum( $data2) / count( $data2))))) / ( count( $data2)-1));		
		 if( $result1 < 1E-12 || $result2 < 1E-12) return 2;

		 $stats1 = median($data1);
		 $stats2 = median($data2);
		 $iqr1 = $stats1[2]-$stats1[0];
		 $iqr2 = $stats2[2]-$stats2[0];

		 if(abs($result1-$result2)/($result1+$result2) > 0.05 && abs($iqr1-$iqr2)/($iqr1+$iqr2) > 0.05) return 0;
		 else if(abs($result1-$result2)/($result1+$result2) > 0.05) return 1;

		return 2;
	}

	//----------------------------
        // Alternative Sanity Check for SD
        // split array in two. if sd's from each half differ 
        // dramatically, flag as dodgy.
        //---------------------

	function sd_nick_sanity( $data){
		 if(count( array_unique( $data)) == 1 ) return 1;

		 $steps = array(10, 50, 100, 500, 1000, 5000, 10000);
		 $result = 0*$steps;
		 $currentpos = 0;

		 for($step = 0; $step < count($steps); $step++)
		 {
			$data = array_slice($data, $currentpos, $steps[$step]);
			$result[$step] = sqrt( array_sum( array_map( "sd_diff_squared", $data, array_fill(0,count( $data), ( array_sum( $data) / count( $data))))) / ( count( $data)-1));
		}

		if( $result[count($steps)-1] < 1E-12) return 1;

		if(abs($result1-$result2)/($result1+$result2) > 0.1) return 0;

		return 1;
	}
	
	//------------------------------------------------------------------
	// Standard Deviation
	//------------------------------------------------------------------
	
	function sd( $data){
		$result = sqrt( array_sum( array_map( "sd_diff_squared", $data, array_fill(0,count( $data), ( array_sum( $data) / count( $data))))) / ( count( $data)-1));
		
		if( $result < 1E-12 || count( array_unique( $data)) == 1 ) $result = 0;			// Ignore v. small values
		return $result;
	}
	
	function sd_diff_squared( $x, $mean){ 
		return pow( $x - $mean, 2); 
	}
?>