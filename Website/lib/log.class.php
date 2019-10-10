<?php
		
	//------------------------------------------------------------------
	// log.class.php
	//------------------------------------------------------------------
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/util.php");
	
	class log{

		// In the true Caladis code these variables are replaced by those which grant access to the SQL database logging queries
		var $dbHost = "location";
		var $dbName = "database";
		var $dbUsername = "login";
		var $dbPassword = "password";
				
		public function add( $qStr, $vStr, $xStr, $nStr, $hStr, $aStr){
					
			// Insert into log table 
			$dbHandle = mysql_connect( $this->dbHost, $this->dbUsername, $this->dbPassword);
			mysql_select_db($this->dbName, $dbHandle);
			mysql_query("INSERT INTO log(date, qStr, vStr, xStr, nStr, hStr, aStr, dlData, dlBins, dlStatus) VALUES ( CURDATE(), '". $qStr ."', '". $vStr ."', '". $xStr ."', '". $nStr ."', '". $hStr ."', '". $aStr ."', 0, 0, 1)");
			$logID = mysql_insert_id();
			mysql_close($dbHandle);
			
			// Clean log
			$this->clean();
			
			return $logID;
		}
		
		
		
		public function selectAll(){
			
			$dbHandle = mysql_connect( $this->dbHost, $this->dbUsername, $this->dbPassword);
			mysql_select_db($this->dbName, $dbHandle);
			$select = mysql_query( "SELECT * FROM log" );
			mysql_close($dbHandle);

			return $select;
		}
		
		
		
		
		//------------------------------------------------------------------
		// clean
		// Removes all files over two days old.
		//------------------------------------------------------------------
		
		private function clean(){
			
			// Select rows older than 2 days with active download status 
			$dbHandle = mysql_connect( $this->dbHost, $this->dbUsername, $this->dbPassword);
			mysql_select_db($this->dbName, $dbHandle);
			$logData = mysql_query( "SELECT id FROM log WHERE date < DATE_SUB(NOW(), INTERVAL 2 DAY) AND dlStatus=1");
			
			$noRows = mysql_numrows( $logData );			
			
			$i = $noRows-1;
			while($i >= 0){
				
				// Delete files
				unlink("../download/". mysql_result( $logData, $i, "id") .".data.txt");
				unlink("../download/". mysql_result( $logData, $i, "id") .".bins.txt");
				unlink("../download/". mysql_result( $logData, $i, "id") .".data.php");
				unlink("../download/". mysql_result( $logData, $i, "id") .".bins.php");
				
				// Change dbStatus field
				mysql_query( "UPDATE log SET dlStatus=0 WHERE id=".mysql_result( $logData, $i, "id"));
				
				$i--;
			}
			
			mysql_close($dbHandle);

			return;
		}
	}
	
	
	
	//------------------------------------------------------------------
	// log_countDownload
	// Called when data or bin file is download. Iterates the dlData or 
	// dlBins field in the relevant data column.
	//------------------------------------------------------------------
	
	function log_countDownload( $logID, $logField){
				
		$logClass = new log;
		$dbHandle = mysql_connect( $logClass->dbHost, $logClass->dbUsername, $logClass->dbPassword);
		mysql_select_db($logClass->dbName, $dbHandle);		
		$logData = mysql_query( "SELECT ". $logField ." FROM log WHERE id=". $logID);
		$newValue = mysql_result( $logData, 0, $logField) + 1;
		mysql_query( "UPDATE log SET ". $logField ."=". $newValue ." WHERE id=". $logID);
		mysql_close($dbHandle);
		
		return;
	}
	
?>		
	