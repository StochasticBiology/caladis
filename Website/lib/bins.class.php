<?php
	
	//------------------------------------------------------------------
	// bins.class.php
	// Functions required to generate $bins array, given $data array.
	//------------------------------------------------------------------
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/util.php");
	
	class bins{
		

		
		//------------------------------------------------------------------
		// Generate 
		//------------------------------------------------------------------
		
		public function generate( $data, $hStr){
			$noBins = $this->number_of_bins( $data, $hStr);
			$bins = $this->define_bins( $data, $noBins);
			$bins = $this->fill_bins( $data, $bins);
			$bins = $this->get_ticks( $bins);
			
			return $bins;
		}
		
		
		
		//------------------------------------------------------------------
		// Number of Bins 
		// Takes 'sturges', 'scott' or 'fd'. Returns integer between 10-100.
		//------------------------------------------------------------------
		
		private function number_of_bins( $data, $hStr){
			
			$noBinsDefault = 26;
			
			if( sd( $data) == 0) return $noBinsDefault;						// If all data equal
			
			switch( $hStr){
				
				// $noBins = log(2,n) + 1
				case "sturges":
					$result = ceil( 1 + log( count( $data), 2));
					break;
					
				// $binWidth = 3.5 * sd * n^(-1/3)
				case "scott":
					sort( $data);					
					$range = percentile( $data, 1) - percentile( $data, 0);
					$width = 3.5 * sd( $data) / pow( count( $data), (1/3));
					$result = ceil( $range / $width );
					break;
				
				// $binWidth = 2 * IQR * n^(-1/3)
				case "fd":
					sort( $data); 
					$range = percentile( $data, 1) - percentile( $data, 0);
					$iqr = percentile( $data, 0.75) - percentile( $data,0.25);
					$width = 2 * $iqr / pow( count( $data), (1/3));
					$result = ceil( $range / $width );
					break;
					
				default:
					$result = $noBinsDefault;
			}
			
			$result = ($result>100) ? 100 : $result;
			$result = ($result<10) ? 10 : $result;			
			return $result;
		}
		

		
		//------------------------------------------------------------------
		// Get ticks 
		// Determine which bins should display a tick label.
		//------------------------------------------------------------------
		
		private function get_ticks( $bins){
			
			$noBins = count( $bins);
			$range = $bins[$noBins-1]["min"]-$bins[0]["min"];			
			
			// Define $tickPeriodMax and $tickStart
			$tickPeriodMax = pow( 10, ceil( log10( $range)));
			
			// Divide $tickPeriodMax by [2,5,10,20,50..] until > 5 ticks
			$denom = array( 1, 2, 5, 10, 20, 50, 100, 200, 500, 1000, 2000, 5000, 10000);
			for( $i=0; $i<count( $denom); $i++){
				$tickPeriod = $tickPeriodMax / $denom[$i];
			$tickStart = $tickPeriod * round( $bins[0]["min"] / $tickPeriod);

				$noTicks = 0;
				foreach($bins as $k => $v){
					
					// If bin boundary is divisable by $tickPeriod
					$mint = ( $v["min"]-$tickStart ) / $tickPeriod;
					if( abs( round( $mint) - $mint) < 0.001 ){
						$bins[$k]["tick"] = TRUE;
						$noTicks++;						
					} 
					else $bins[$k]["tick"] = FALSE;
				}
				
				if( $noTicks > 5 ) break;
			}
						
			return $bins;
		}

		
		
		//------------------------------------------------------------------
		// Define Bins 
		// Takes $data and $noBins. Returns an array of bin objects of the
		// form ['max', 'min', 'count', 'tick']. If all values of the $data 
		// array are the same (i.e. the input expression is not probabilistic), 
		// the bin width is set as the order of magnitude of the least 
		// significant position. Else, the bin width is (1-99 percentile 
		// range / $noBins), rounded up to the next nice number.
		//------------------------------------------------------------------
		
		private function define_bins( $data, $noBins){
			
			// If all data equal
			if( sd( $data) == 0){
				
				$binWidth = pow( 10, $this->least_sig_pos( $data[0] ));
				if( $binWidth < 1E-5) $binWidth = 1E-5;							// Minimum bin-width size (if $data is 0.1111111111111111, show bin boundaries 0.11111 -> 0.11112)
				
				$histStart =  $data[0] - ($binWidth * round( $noBins/2));
				$histStart = $binWidth * round( $histStart / $binWidth);		// Round $histStart to same precision as $binWidth
			}
			else{
				sort( $data);
				$tailLB = percentile( $data, 0.01);
				$tailUB = percentile( $data, (1-0.01));
								
				$binWidth = ceil_nn( ($tailUB - $tailLB) * 1.1 / $noBins);		// Extend range by 10% to cover boundary region
				
				$histStart = ( ($tailUB + $tailLB) / 2 ) - ($binWidth * $noBins / 2);
				$histStart = $binWidth * round( $histStart / $binWidth);		// Round $histStart to same precision as $binWidth
			}		
			
			$bins = array();
			for($i = 0; $i < $noBins ; $i++){			
				$bins[] = array( "min" => ($histStart + ($i * $binWidth)), "max" => ($histStart + ( ($i+1) * $binWidth)), "count" => 0, "tick" => FALSE);
			}
			
			return $bins;
		}
		
		private function least_sig_pos( $num){   		
			$counter = 0;
			if( $num == 0) return 0;
			elseif( round( $num) == $num ){
				while( round($num/10) == $num/10 ){
					$num /= 10;
					$counter++;
				}
			}
			else{
				while( round( $num) != $num ){
					$num *= 10;
					$counter--;
				}	
			}
			return $counter;
		}
		

		
		//------------------------------------------------------------------
		// Fill Bins 
		// Determine count of each bin.
		//------------------------------------------------------------------
		
		private function fill_bins( $data, $bins){
			foreach( $bins as $k0 => $v0 ){
				foreach( $data as $v1 ){
					if( $v1 >= $v0["min"] && $v1 < $v0["max"] ) $bins[$k0]["count"] ++;
				}
			}
			return $bins;	
		}
	}	
?>