<?php
	
	//------------------------------------------------------------------
	// download.class.php
	//------------------------------------------------------------------
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/util.php");
	
	class download{
		
		
		
		//------------------------------------------------------------------
		// Write
		//------------------------------------------------------------------
		
		public function write( $logID, $data, $bins){
			
			// Data	
			$dataStr = "";
			foreach($data as $k => $v){
				$dataStr .= $v."\t";
			}
			
			$binsStr = "Min\tMax\tCount\n";
			foreach($bins as $k => $v){
				$binsStr .= $v["min"] ."\t". $v["max"] ."\t". $v["count"] ."\n";
			}

			// Create txt files
			$fHandle = fopen( "../download/".$logID.".data.txt", 'w');
			fwrite($fHandle, $dataStr);
			fclose($fHandle);
			
			$fHandle = fopen( "../download/".$logID.".bins.txt", 'w');
			fwrite($fHandle, $binsStr);
			fclose($fHandle);
			
			// Create php files to force download
			
			$dataPhp = "<?php
							require_once('". $_SERVER['DOCUMENT_ROOT'] . "/lib/log.class.php');
							log_countDownload(".$logID.", 'dlData');
							header('Content-disposition: attachment; filename=".$logID.".data.txt'); 
							header('Content-type: text/plain'); 
							readfile('".$logID.".data.txt'); 
						?>";

			$binsPhp = "<?php
							require_once('". $_SERVER['DOCUMENT_ROOT'] . "/lib/log.class.php');
							log_countDownload(".$logID.", 'dlBins');
							header('Content-disposition: attachment; filename=".$logID.".bins.txt'); 
							header('Content-type: text/plain'); 
							readfile('".$logID.".bins.txt');
						?>";

			$fHandle = fopen( "../download/".$logID.".data.php", 'w');

			if(!$fHandle) {
			  echo 'Couldnt write datafile<br>';
			}
			else {
			  fwrite($fHandle, $dataPhp);
			  fclose($fHandle);
			}

			$fHandle = fopen( "../download/".$logID.".bins.php", 'w');

			if(!$fHandle) {
			  echo 'Couldnt write binfile<br>';
			}
			else {
			  fwrite($fHandle, $binsPhp);
			  fclose($fHandle);
			}			
			return;
		}
	}

?>
	