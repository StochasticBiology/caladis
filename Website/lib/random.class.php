<?php
	
	//------------------------------------------------------------------
	// random.class.php
	// Contains functions for generating random numbers.
	//------------------------------------------------------------------
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 
	
	class random{		
		
		
		
		//------------------------------------------------------------------
		// Beta 
		//------------------------------------------------------------------
		
		function beta( $alpha, $beta){
			if( $alpha <= 0 || $beta <= 0 ) return FALSE;
			
			$x = $this->gamma($alpha, 1);
			$y = $this->gamma($beta, 1);
			return ( $x / ( $x + $y ) );
		}
		
		
		
		//------------------------------------------------------------------
		// Binomial 
		//------------------------------------------------------------------

		function binomial($n, $p) {
			if( $p < 0 || $p > 1 ) return FALSE;
			
			$x = 0;
			for($i = 0; $i<$n; $i++) {
				if( $this->uniform(0,1) < $p) $x++;
			}
			return $x;
		}
		
		
		
		//------------------------------------------------------------------
		// Exponential 
		//------------------------------------------------------------------
		
		function exponential( $lambda){
			if( $lambda <= 0 ) return FALSE;

			do{			
			  do{
			    $x = $this->uniform(0,1);
			  }while( $x == 0 );
			  $iainr = -log( $x ) / $lambda;
			}while($iainr == 0);
			return $iainr;
		}
		
		
		
		//------------------------------------------------------------------
		// Gamma 
		// Uses the Ahrens-Dieter acceptance-rejection method.
		//------------------------------------------------------------------
		
		function gamma( $k, $theta){
			if( $k <= 0 || $theta <= 0 ) return FALSE;
			
			$sum = 0;
			for( $i=0; $i < floor($k); $i++){
				$sum += log( $this->uniform(0,1) );
			}
			
			$k_frac = $k - floor( $k);

			if($k_frac == 0){
				$result = -1*$theta*$sum;
			}
			else{
				$v0 = exp(1) / ( exp(1) + $k_frac );
				$breaker = 0;
				do{
					if( $this->uniform(0,1) < $v0 ){
					    	$ksi = pow( $this->uniform(0,1), 1/$k_frac );
					    	$eta = $this->uniform(0,1) * pow( $ksi, $k_frac-1 );
					}
					else{
						$ksi = 1 - log( $this->uniform(0,1) );
						$eta = $this->uniform(0,1) * exp( -$ksi );
					}
					$breaker++;
				}while( $eta > pow( $ksi, $k_frac-1)*exp(-$ksi) && $breaker<50);
			
				$result = $theta * ( $eta - $sum);
				if( is_infinite( $result) ) $result = $this->gamma( $k, $theta);				// Prevent return of INF
			}
			return $result;
		}
		
		
		
		//------------------------------------------------------------------
		// Geometric 
		//------------------------------------------------------------------
		
		function geometric( $p){
			if( $p < 0 || $p > 1 ) return FALSE;
						
			$x =  $this->uniform(0,1);
			for( $i=1; $i<10000; $i++){
				if( $x > 1 - pow( (1-$p), ($i-1)) && $x <= 1 - pow( (1-$p), $i)) return $i;
			}
						
			return $this->geometric( $p);	// If $i > 10000, re-run.
		}
		
		
		
		//------------------------------------------------------------------
		// Normal 
		//------------------------------------------------------------------
		
		function normal( $mean, $sd){
			$x = rand() / getrandmax();
			$y = rand() / getrandmax();
			$z = sqrt( -2*log( $x)) * cos( 2*pi()*$y);
			return ($z*$sd) + $mean;
		}
		

		//------------------------------------------------------------------
		// Log-Normal 
		//------------------------------------------------------------------
		
		function lognormal( $mean, $sd){
			$sigma = sqrt(log(1+$sd*$sd/($mean*$mean)));
			$mu = log($mean*$mean/sqrt($sd*$sd+$mean*$mean));
			$x = rand() / getrandmax();
			$y = rand() / getrandmax();
			$z = sqrt( -2*log( $x)) * cos( 2*pi()*$y);
			
			return exp($mu + $z*$sigma);
		}
		
		
		//------------------------------------------------------------------
		// Poisson 
		//------------------------------------------------------------------
		
		function poisson( $lambda){
			$L = exp( -$lambda);
			$p = 1.0;
			$k = 0;
			do{
				$k++;
				$p *= $this->uniform(0,1);
			} while ($p > $L);
			return $k - 1;
		}
		
		
		
		//------------------------------------------------------------------
		// Uniform
		// Scales $min, $max so that they are seperated by roughtly $ints 
		// integers. Random integer generated and then scaled back.
		//------------------------------------------------------------------

		function uniform( $min, $max){
			$ints = 10000;
			$x = $ints/round_sf( abs( $max-$min), 1);
			$r = rand( $min * $x, $max * $x) / $x;
			if($r == 0 && $min == 0 && $max != 0) {
			      	return $this->uniform($min, $max);
			}
			else{
				return $r;
			}
		}

		/// Discrete uniform
		function duniform( $min, $max){
			return rand( $min, $max);
		}

	}	
?>
