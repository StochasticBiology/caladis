<?php
	
	//------------------------------------------------------------------
	// build.class.php
	// All the dynamic html elements for compute/index.php are built here.
	//------------------------------------------------------------------
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 
		
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/util.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/bins.class.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/data.class.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/download.class.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/log.class.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/random.class.php");
	
	class build{
	
		
		
		//------------------------------------------------------------------
		// Get
		// Customised version of the _GET() function. Can except coded or
		// uncoded url parameters. If the url parameter is not found, the
		// default parameter will be returned, apart from 'q' parameter.
		//------------------------------------------------------------------
		
		private function get( $i){
			
			$default = array( "v"=>NULL, "h"=>"fd", "a"=>"rad", "x"=>"off", "n"=>"s", "p"=>"0" );
			
			if( isset( $_GET[$i])){
				$raw = $_GET[$i];
				if( strpos( $raw,'%') !== FALSE) return urldecode( $raw);
				else return $raw;
			}
			elseif( $i == "q") header("HTTP/1.1 500 No query input (build.class.php, 25)");
			else return $default[$i];
		}
		
		
		
		//------------------------------------------------------------------
		// qd (Query Display)
		// Takes the 'q' and 'v' url parameters and prints the html for the
		// top box of the /compute/index.php page. The query is formatted to
		// look pretty and variable properties are attached to each variable 
		// as data- attributes. These can then be read by tooltips.js.
		//------------------------------------------------------------------
		
		public function qd(){
		
			$qStr = $this->get("q");
			$vStr = $this->get("v");
						
			$dataClass = new data;
			$qArr = $dataClass->parse_query( $qStr, TRUE);
			$vArr = $dataClass->parse_variable( $vStr);
			
			$qdHTML = "";
			foreach( $qArr as $k => $v){
				
				// add space
				if( $k>0){
					if( !($qArr[$k]["code"] == "bo" && $qArr[$k-1]["code"] == "f" )){ 
						$qdHTML .= " "; 
					}
				}
				
				if( $v["code"] == "v"){
					
					$qdVarInfo = "";
					if( isset( $vArr[ $v["value"] ])){
						$qdVarInfo .= $vArr[ $v["value"] ]["dist"];
						for( $i=0; $i<count( $vArr[ $v["value"] ]["param"]); $i++){
							$qdVarInfo .= ";".$vArr[ $v["value"] ]["param"][$i];
						}
					}
					$qdHTML .= "<span class='qd-var' data-dist=". $qdVarInfo .">#". $v["value"] ."</span>";
				}
				elseif( $v["value"] == "*") $qdHTML .= "&times;";
				else $qdHTML .= $v["value"];
			}
			
			echo "<p class='qd'><a class='qd-btn' href='../?". $_SERVER['QUERY_STRING'] ."'>Edit</a>".$qdHTML."</p>";		
			
			return;
		}
		
		
		
		//------------------------------------------------------------------
		// Compute
		// Takes the 'q', 'v', 'x', 'n', 'h' and 'a' url parameters and 
		// prints the html for the content of the /compute/index.php page.
		//------------------------------------------------------------------
		
		public function compute(){	

		       // this gives the maximum time we can spend on a calculation
		       $timecutoff = 60;

			$qStr = $this->get("q");
			$vStr = $this->get("v");
			$xStr = $this->get("x");
			$nStr = $this->get("n");
			$hStr = $this->get("h");
			$aStr = $this->get("a");
			
			// Log in database
			$logClass = new log;
			$logID = $logClass->add( $qStr, $vStr, $xStr, $nStr, $hStr, $aStr);
						
			// Sample size
			switch( $nStr){
				case "s":
					$noData = 10000;
					break;
				case "m":
					$noData = 20000;
					break;
				case "l":
					$noData = 50000;
					break;
				default:
					$noData = 10000;
			}
			
			// Start timer
			$timeArr = explode(" ", microtime()); 
			$timeStart = $timeArr[1] + $timeArr[0]; 	
			
			// Generate data 
			$dataClass = new data;
			$data = $dataClass->generate( $qStr, $vStr, $aStr, $noData, $timecutoff);

			
			// Generate histogram
			$binsClass = new bins;
			$bins = $binsClass->generate( $data, $hStr);

			// Write files for download
			$downloadClass = new download;
			$downloadClass->write( $logID, $data, $bins);
			
			// print
			echo "<div class='stage'>";
			$this->hist( $bins, $noData, sd_sanity($data));
			echo "</div>";

			if( $xStr == "on"){
				echo "<div class='stage'>";
				echo "<p class='stage-banner'>Standard Deviation Analysis</p>";
				$this->x( $qStr, $vStr, $aStr, $noData, $data, $timecutoff);
				echo "</div>";
			}

				
			echo "<div class='col-left'>";
			echo "<div class='stage'>";
			echo "<p class='stage-banner'>Distribution Details</p>";
			$this->stats( $data, $noData, $timeStart, $timecutoff);
			echo "</div>";
			echo "</div>";

			echo "<div class='col-right'>";
			echo "<div class='stage'>";
			echo "<p class='stage-banner'>Download</p>";
			$this->download( $logID);
			echo "</div>";
			echo "</div>";

			echo '<br />';

			
			if( $xStr != "on"){
				$hrefOn = "?q=". urlencode( $this->get("q")) ."&v=". urlencode( $this->get("v")) ."&x=on&n=". urlencode( $this->get("n")) ."&h=". urlencode( $this->get("h")) ."&a=". urlencode( $this->get("a"));   
				echo "<p style='clear: both; padding: 16px 0 0; color: #555;' class='turn'>This calculation was run with Standard Deviation Analysis turned off. <a href='". $hrefOn ."' style='color: inherit;'>Turn Standard Deviation Analysis on</a>.</p>";
			}
			
			return;
		}
		

		 
		//------------------------------------------------------------------
		// Hist
		// Takes $data and $hStr and prints html for the histogram box 
		// (including slider). The bins are defined and filled by 
		// hist.class.php.
		//------------------------------------------------------------------
		
		private function hist( $bins, $noData, $sanityresult){			
			
			$noBins = count( $bins);
			
			// Find maximum count
			$maxCount = 0; $maxX = 0;
			foreach( $bins as $v ){ 
				if( $maxCount < $v["count"]) $maxCount = $v["count"]; 
                                if( $maxX < abs($v["max"])) $maxX = abs($v["max"]);
			}
						
			// Print
			echo "<div class='hist-wrap'>";
			echo "<h3>Probability Distribution</h3>";
			echo "<p>Estimated probability distribution based on ". $noData ." calculations of the input expression.</p>";
			echo "<ul class='hist'>";
			
			foreach( $bins as $k => $v ){
				echo "<li style='width:" . (99.9 / $noBins) . "%;' data-min='". $v["min"] ."' data-max='". $v["max"] ."' data-prob='". round($v["count"] / $noData, 5) ."'>";
				echo "<div class='bar' style='height:" . round(100 * $v["count"] / $maxCount) . "%;'>";
				echo "<p class='bar-value'>" . round(100 * $v["count"] / $noData, 1) . "%</p>";
				echo "</div>";

				// this module employs different formatting depending on the magnitude of the number range to be presented
				if( $v["tick"] === TRUE ) 
				{
					if(abs($maxX) < 1e5 && abs($maxX) > 1e-3) { $ostr = "<p class='tick'>" . round_sf($v["min"], 3) . "</p>"; } else { $ostr = sprintf("<p class = 'tick'>%.2e</p>", $v["min"]); }
					echo $ostr;
				}
				else echo "<div class='tick'></div>";
				echo "</li>";
			}
			
			echo "</ul>";
			echo "</div>";
			
			// slider
			echo "<div class='slider-wrap'>";
			echo "<div class='slider'></div>";
			echo "<br />";
			echo "<p>The total probability between <span id='slider-lb'></span> and <span id='slider-ub'></span> is <b><span id='slider-prob'></span>&#37;</b>. To adjust this range simply move the orange tabs along the slider above.</p>";
			echo "</div>";

			$sliderMin = $bins[0]["min"];
			$sliderMax = $bins[ $noBins-1]["max"];
			$sliderStep = round_sf($bins[0]["max"] - $bins[0]["min"], 1);
			
			echo "<script type='text/javascript'>";
			echo "$('.slider').slider({ range: true, min: ". $sliderMin .", max: ". $sliderMax .", step: ". $sliderStep .", values: [ ". $sliderMin .", ". $sliderMax ." ], slide: function( event, ui){ update_slider(ui.values[0], ui.values[1]); }});";
			echo "update_slider( $('.slider').slider('values', 0), $('.slider').slider('values', 1));";
			echo "</script>";
			
			return;
		}
		
		
		
		//------------------------------------------------------------------
		// x (Standard Deviation Analysis)
		//------------------------------------------------------------------
		
		private function x( $qStr, $vStr, $aStr, $noData, $data, $timecutoff){
			
			// Reduce standard deviation by a fraction x
			$x = 0.1;

			// output array: [ a=> str, sd, b=>...]
			$outputArr = array();

			$dataClass = new data;	
			$vArr = $dataClass->parse_variable( $vStr);

			// this rather clunky module decides whether to use scientific notation or not in the output formatting, depending on the magnitudes of the numbers involved
			$useScientific = 0;
			foreach( $vArr as $k => $v){
				switch( $v["dist"] ){
					case "unif":
						if(abs($vArr[$k]["param"][0]) > 1e5 || abs($vArr[$k]["param"][1]) > 1e5 || (abs($vArr[$k]["param"][0]) < 1e-3 && abs($vArr[$k]["param"][1]) < 1e-3) ) $useScientific = 1; break;
					case "norm":
						if(abs($vArr[$k]["param"][0]) > 1e5 || abs($vArr[$k]["param"][1]) > 1e5 || (abs($vArr[$k]["param"][0]) < 1e-3 && abs($vArr[$k]["param"][1]) < 1e-3) ) $useScientific = 1; break;
					case "logn":
						if(abs($vArr[$k]["param"][0]) > 1e5 || abs($vArr[$k]["param"][1]) > 1e5 || (abs($vArr[$k]["param"][0]) < 1e-3 && abs($vArr[$k]["param"][1]) < 1e-3)) $useScientific = 1; break;
				}
			}

			// compute the changes to make to each distribution to decrease the s.d. by 10%, and run analysis with these changes
			foreach( $vArr as $k => $v){
				$vArrAlt = $vArr;	
				switch( $v["dist"] ){			
					case "unif":
						$range = $v["param"][1] - $v["param"][0];
						$vArrAlt[$k]["param"][0] += ($x/2) * $range;
						$vArrAlt[$k]["param"][1] -= ($x/2) * $range;
						$dataAlt = $dataClass->generate( $qStr, $vArrAlt, $aStr, $noData, $timecutoff);
						if($dataAlt == -1) return;
						$medianData = median($dataAlt);
						$iqr = $medianData[2]-$medianData[0];
						if($useScientific == 1) {
						  $str1 = sprintf("%.2e", $vArrAlt[$k]["param"][0]);
						  $str2 = sprintf("%.2e", $vArrAlt[$k]["param"][1]);
						  $str3 = sprintf("%.2e", sd($dataAlt));
						  $str4 = sprintf("%.2e", $iqr);
						  $outputArr[$k] = array( "str" => "Minimum: ". $str1 .", Maximum: ". $str2 , "sd" => sd( $dataAlt), "iqr" => $iqr);
						}
						else {
						  $outputArr[$k] = array( "str" => "Minimum: ". round_sf( $vArrAlt[$k]["param"][0], 3) .", Maximum: ". round_sf( $vArrAlt[$k]["param"][1], 3) , "sd" => round_sf(sd( $dataAlt), 3), "iqr" => round_sf($iqr, 3));
						}
						unset($dataAlt);
						break;
					
					case "norm":
						$vArrAlt[$k]["param"][1] *= ( 1 - $x );
						$dataAlt = $dataClass->generate( $qStr, $vArrAlt, $aStr, $noData, $timecutoff);
						if($dataAlt == -1) return;
						$medianData = median($dataAlt);
						$iqr = $medianData[2]-$medianData[0];

						if($useScientific == 1) {
						  $str1 = sprintf("%.2e", $vArrAlt[$k]["param"][0]);
						  $str2 = sprintf("%.2e", $vArrAlt[$k]["param"][1]);
						  $str3 = sprintf("%.2e", sd($dataAlt));
						  $str4 = sprintf("%.2e", $iqr);
						  $outputArr[$k] = array( "str" => "Mean: ". $str1 .", Standard Deviation: ". $str2 , "sd" => sd( $dataAlt), "iqr" => $iqr);
						}
						else {
						  $outputArr[$k] = array( "str" => "Mean: ". round_sf( $vArrAlt[$k]["param"][0], 3) .", Standard Deviation: ". round_sf( $vArrAlt[$k]["param"][1], 3) , "sd" => round_sf(sd( $dataAlt), 3), "iqr" => round_sf($iqr, 3));
						}
						unset($dataAlt);								break;

					case "logn":
						$vArrAlt[$k]["param"][1] *= ( 1 - $x );
						$dataAlt = $dataClass->generate( $qStr, $vArrAlt, $aStr, $noData, $timecutoff);
						if($dataAlt == -1) return;
						$medianData = median($dataAlt);
						$iqr = $medianData[2]-$medianData[0];
						if($useScientific == 1) {
						  $str1 = sprintf("%.2e", $vArrAlt[$k]["param"][0]);
						  $str2 = sprintf("%.2e", $vArrAlt[$k]["param"][1]);
						  $str3 = sprintf("%.2e", sd($dataAlt));
						  $str4 = sprintf("%.2e", $iqr);
						  $outputArr[$k] = array( "str" => "Mean: ". $str1 .", Standard Deviation: ". $str2 , "sd" => sd( $dataAlt), "iqr" => $iqr);
						}
						else {
						  $outputArr[$k] = array( "str" => "Mean: ". round_sf( $vArrAlt[$k]["param"][0], 3) .", Standard Deviation: ". round_sf( $vArrAlt[$k]["param"][1], 3) , "sd" => round_sf(sd( $dataAlt), 3), "iqr" => round_sf($iqr, 3));
						}
						unset($dataAlt);								break;
						
					default:
						break;
				}

			}
			
			// Print
			echo "<p>This tool analyses how the standard deviation and interquartile range of input variables affects the standard deviation and interquartile range of the output. The standard deviation of each probability distribution variable is reduced by 10% while keeping the mean constant.</p>";

			// display warning messages if sanity checking flagged a problem
			if(sd_sanity($data) == 0) {
			  echo "<p><b>Caution: standard deviation analysis may be unreliable as this distribution appears not to have converged. Please try more samples.</b></p>";
			}
			if(sd_sanity($data) == 1) {
			  echo "<p><b>Caution: this distribution appears not to have fully converged, so standard deviation analysis may be unreliable, but robust statistics (median and IQR) should be reliable. Please use these or try more samples.</b></p>";
			}

			if( count( $outputArr) === 0){
				echo "<p><b>Your expression did not contain distributions suitable for Standard Deviation Analysis.</b></p>";
			}
			else {
				// output SDA summary table
				echo "<table class='table table-striped'>";
				echo "<thead><tr><th>Variable</th><th>Trial parameters</th><th>New s.d.</th><th>S.d. change</th><th>New IQR</th><th>IQR change</th></tr></thead>";
				echo "<tbody>";
				
				$sdOrig = sd( $data);
				$medOrig = median($data);
				$iqrOrig = $medOrig[2]-$medOrig[0];

				foreach( $outputArr as $k => $v){
					echo "<tr>";
					echo "<td>&#35;". $k ."</td>";
					echo "<td>". $v["str"] ."</td>";
					if($useScientific == 1) {
					  $str1 = sprintf("%.2e", $v["sd"]); 
					  $str2 = sprintf("%.2e", ($v["sd"] / $sdOrig - 1.) * 100); 
					  $str3 = sprintf("%.2e", $v["iqr"]);
					  $str4 = sprintf("%.2e", ($v["iqr"] / $iqrOrig - 1.) * 100);
					  echo "<td>". $str1 ."</td>";
					  echo "<td>". round_sf( ($v["sd"] / $sdOrig - 1.) * 100, 3) ."&#37;</td>";
					  echo "<td>". $str3 ."</td>";
					  echo "<td>". round_sf( ($v["iqr"] / $iqrOrig - 1.) * 100, 3) ."&#37;</td>";
					}
					else {
					  echo "<td>". round_sf( $v["sd"], 3) ."</td>";
					  echo "<td>". round_sf( ($v["sd"] / $sdOrig - 1.) * 100, 3) ."&#37;</td>";
					  echo "<td>". round_sf( $v["iqr"], 3) ."</td>";
					  echo "<td>". round_sf( ($v["iqr"] / $iqrOrig - 1.) * 100, 3) ."&#37;</td>";
					}
					echo "</tr>";
				}
				echo "</tbody>";
				echo "</table>";
			}
						
			return;
		}
		
		
		
		//------------------------------------------------------------------
		// Stats
		//------------------------------------------------------------------
		
		private function stats( $data, $noData, $timeStart, $timecutoff){
			
			$timeArr = explode(" ", microtime()); 
			$timeEnd = $timeArr[1] + $timeArr[0]; 

			// kill the process if we're in danger of exceeding the maximum processing time
			if(time() - $_SERVER['REQUEST_TIME'] >= $timecutoff-3)
			  die("<div class='error'><p>Our apologies: due to technical restrictions, Caladis currently cannot spend more than $timecutoff seconds on a calculation. We are working to increase this limit: meantime, please try re-running with fewer samples, or turning off standard deviation analysis if it is currently on.</p></div>");

			// output summary, with caution messages if sanity checking flagged an issue
			echo "<p>Statistical and computational properties of the output distribution:</p>";
			if(sd_sanity($data) == 0) {
			  echo "<p><b>Caution: Caladis has detected that this calculation may not have converged suitably, perhaps due to a highly skewed form. Statistics may be unreliable -- please try more samples.</b></p>";
			}
			else if(sd_sanity($data) == 1) {
			  echo "<p><b>Caution: Caladis has detected that this calculation may not have fully converged, but robust statistics (median and IQR) should be reliable. Please use these or try more samples.</b></p>";
			}

			echo "<table class='table'>";
			echo "<tbody>";
			
			// decide which notation to use (scientific or standard) based on magnitude of numbers involved
			$medianstats = median($data);
			$newiqr = $medianstats[2]-$medianstats[0];
			if(abs(mean($data)) > 1e5 || abs(sd($data)) > 1e5 || abs($medianstats[0]) > 1e5 || abs($medianstats[1]) > 1e5 || abs($medianstats[2]) > 1e5 || abs($newiqr) > 1e5) { 
			  $snotation = 1; 
			} 
			else if(abs(mean($data)) < 1e-3 && abs(sd($data)) < 1e-3 && abs($medianstats[0]) < 1e-3 && abs($medianstats[1]) < 1e-3 && abs($medianstats[2]) < 1e-3 && abs($newiqr) < 1e-3) { 
			  $snotation = 1; 
			} 
			else { 
			  $snotation = 0; 
			}

			if($snotation == 1) { $ostr = sprintf("%.2e", mean($data)); } else { $ostr = round_sf(mean($data), 3); }
			echo "<tr><td>Mean</td><td>". $ostr ."</td></tr>";
			if($snotation == 1) { $ostr = sprintf("%.2e", sd($data)); } else { $ostr = round_sf(sd($data), 3); }
			echo "<tr><td>Standard Deviation</td><td>". $ostr ."</td></tr>";
			if($snotation == 1) { $ostr = sprintf("%.2e", $medianstats[0]); } else { $ostr = round_sf($medianstats[0], 3); }
			echo "<tr><td>First quartile</td><td>". $ostr ."</td></tr>";
			if($snotation == 1) { $ostr = sprintf("%.2e", $medianstats[1]); } else { $ostr = round_sf($medianstats[1], 3); }
			echo "<tr><td>Median</td><td>". $ostr ."</td></tr>";
			if($snotation == 1) { $ostr = sprintf("%.2e", $medianstats[2]); } else { $ostr = round_sf($medianstats[2], 3); }
			echo "<tr><td>Third quartile</td><td>". $ostr ."</td></tr>";
			if($snotation == 1) { $ostr = sprintf("%.2e", $newiqr); } else { $ostr = round_sf($newiqr, 3); }
			echo "<tr><td>Interquartile range</td><td>". $ostr ."</td></tr>";

			echo "<tr><td>Sample Size</td><td>". $noData ."</td></tr>";
			echo "<tr><td>Calculation time</td><td>". round_sf( ($timeEnd - $timeStart)*1000, 4) ." ms</td></tr>";
			echo "</tbody>";
			echo "</table>";
			
			return;
		}
		
		 
		
		//------------------------------------------------------------------
		// Download
		//------------------------------------------------------------------
		
			private function download( $logID){
			echo "<p>Download .txt file containing all data values:</p>";
			echo "<div class='download-btn-bar'>";
			echo "<a class='btn' href='../download/". $logID .".data.php'>Download Data</a>";
			echo "</div>";
		
			echo "<p>Download .txt file containing the histogram bin parameters:</p>";
			echo "<div class='download-btn-bar'>";
			echo "<a class='btn' href='../download/". $logID .".bins.php'>Download Bins</a>";
			echo "</div>";
                        echo "<p>Revisit this output with the URL below:<br>";
echo "<textarea style = 'width: 80%;'>www.caladis.org/compute/?".$_SERVER['QUERY_STRING']."</textarea></p>";

			
			return;
		}
		
		
		//------------------------------------------------------------------
		// Log
		//------------------------------------------------------------------
		
		public function log(){
			
			// Log in database
			$logClass = new log;
			$logData = $logClass->selectAll();
			$noRows = mysql_numrows( $logData );

			echo "<table class='table table-striped'>";
			echo "<thead>";
			echo "<tr><th>ID</th><th>Date</th><th>Query</th><th>Variables</th><th>x</th><th>n</th><th>h</th><th>a</th><th>[Data]</th><th>[Bins]</th></tr>";
			echo "</thead>";
			echo "<tbody>";
			
			if( $noRows > 200){ $noRows = 200; }
			
			$i = $noRows-1;
			while($i >= 0){
				echo "<tr>";
				echo "<td>". mysql_result( $logData, $i, "id") ."</td>";
				echo "<td style='white-space:nowrap;'>". mysql_result( $logData, $i, "date") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "qStr") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "vStr") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "xStr") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "nStr") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "hStr") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "aStr") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "dlData") ."</td>";
				echo "<td>". mysql_result( $logData, $i, "dlBins") ."</td>";
				echo "</tr>";
				$i--;
			}
			
			echo "</tbody>";
			echo "</table>";

			return;
		}
	}
?>