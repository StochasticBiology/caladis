 <?php 
	
	//------------------------------------------------------------------
	// data.class.php
	// Functions used to generate the array of data values.
	//------------------------------------------------------------------
  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 
	
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/util.php");
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/random.class.php");
	
	class data{

		
		//------------------------------------------------------------------
		// Generate 
		//------------------------------------------------------------------
		
		public function generate( $qStr, $vStr, $aStr="rad", $noData, $timecutoff){	

			if( is_array($vStr)) $vArr = $vStr;						// Allows parsed variable array to be passed directly;
			else $vArr = $this->parse_variable( $vStr);
			
			$qArr = $this->parse_query( $qStr);
			$qArr = $this->rejig_exponents( $qArr);
			if( $aStr=="deg") $qArr = $this->rejig_trig( $qArr);
			
			// Build
			$data = array();
			$randomClass = new random;

			$c1 = $c2 = 0;
			for($i = 0; $i < $noData; $i++){
			  // this module imposes a time cutoff on calculations
			  $c1++;
			  if($c1 == 100) {
			    $c1 = 0; $c2 += 5;
			    set_time_limit(20);
			    if(time() - $_SERVER['REQUEST_TIME'] >= $timecutoff) {
  			      echo("<div class='error'><p>Our apologies: due to technical restrictions, Caladis currently cannot spend more than $timecutoff seconds on a calculation. We are working to increase this limit: meantime, please try re-running with fewer samples, or turning off standard deviation analysis if it is currently on.</p></div>");
			      return -1;
			    }
			  }				
				// Get variable values
				foreach( $vArr as $k => $v ){			
					switch( $v["dist"]){
						case "beta":
							$vArr[$k]["value"] = $randomClass->beta( $v["param"][0],  $v["param"][1]);
							break;
						case "bino":
							$vArr[$k]["value"] = $randomClass->binomial( $v["param"][0], $v["param"][1]);
							break;
						case "expo":
							$vArr[$k]["value"] = $randomClass->exponential( $v["param"][0]);
							break;
						case "gamm":
							$vArr[$k]["value"] = $randomClass->gamma( $v["param"][0],  $v["param"][1]);
							break;
						case "geom":
							$vArr[$k]["value"] = $randomClass->geometric( $v["param"][0]);
							break;
						case "norm":
							$vArr[$k]["value"] = $randomClass->normal( $v["param"][0], $v["param"][1]);
							break;
						case "logn":
							$vArr[$k]["value"] = $randomClass->lognormal( $v["param"][0], $v["param"][1]);
							break;
						case "pois":
							$vArr[$k]["value"] = $randomClass->poisson( $v["param"][0]);
							break;
						case "unif":
							$vArr[$k]["value"] = $randomClass->uniform( $v["param"][0], $v["param"][1]);
							break;
						case "duni":
							$vArr[$k]["value"] = $randomClass->duniform( $v["param"][0], $v["param"][1]);
							break;

						default:
							return FALSE;
					}
				}
				
				$strToComp = "";
				foreach( $qArr as $k => $v ){
					if( $v["code"] == "v" ) $strToComp .= '('.$vArr[ $v["value"] ]["value"].')';
					else $strToComp .= $v["value"];
				}
				
				// Safety check and compute
				// This uses "eval", the use of which is discouraged http://php.net/manual/en/function.eval.php as it can be exploited to run user-generated code. This is a calculated risk in Caladis. We parse user-generated expressions for safety (parse_query), but more secure implementations are certainly possible.
				// safety check
				if($this->safety_check($strToComp) == false)
				{
				  echo("<div class='error'><p>The input expression appears to be malformed. Please use the Caladis front end to input a calculation.</p></div>");
				  return -1;
				}
				// compute
				$cresult = eval("\$computed = (".$strToComp.");");
				if( $computed === FALSE) die("<div class='error'><p>Caladis experienced a 'math error', meaning that a calculation result is undefined or infinite, possibly due to a divide-by-zero. Please check your input for the possibility of undefined results.</p></div>");
				else $data[] = $computed;
			}

			return $data;
		}
		
				
	// parse the expression to be eval'd and ensure that it only contains mathematical expressions and syntax -- for security			
	public function safety_check($equation) {
    	       // Remove whitespaces
	       $equation = preg_replace('/\s+/', '', $equation);
	       $number = '((?:0|[1-9]\d*)(?:\.\d*)?(?:[eE][+\-]?\d+)?|pi| )'; // What is a number
	       $functions = '(?:sinh?|cosh?|tanh?|acosh?|asinh?|atanh?|exp|log(10)?|deg2rad|rad2deg|sqrt|pow|abs|intval|ceil|floor|round|(mt_)?rand|gmp_fact)'; // Allowed PHP functions
	       $operators = '[\/*\^\+-,]'; // Allowed math operators
	       $regexp = '/^([+-]?('.$number.'|'.$functions.'\s*\((?1)+\)|\((?1)+\))(?:'.$operators.'(?1))?)+$/'; // Final regexp, heavily using recursive patterns
	       if (preg_match($regexp, $equation)) return true;
     	       else return false;
	 }


		//------------------------------------------------------------------
		// Parse Variables 
		// Takes string of the form var1:dist,param1,param2;var2:..
		// Returns array of the form [ var1 => [dist, [param], value], var2 =>..].
		//------------------------------------------------------------------
		
		public function parse_variable( $str){
			$arr = array();
			
			foreach( explode(";", $str) as $val){
				if( strlen( $val) > 1){
					$var_prop = explode( ":", $val);
					$dist_param = explode( ",", $var_prop[1]);
					$arr[ $var_prop[0] ] = array( "dist" => $dist_param[0], "param" => array_slice($dist_param, 1), "value" => NULL);
				}
			}
			return $arr;
		}		
		
		
		
		//------------------------------------------------------------------
		// Parse Query 
		// Takes query string. 
		// Returns array of the form [ 0 => [value, code],.. ]
		//------------------------------------------------------------------
		// code includes "n" number, "v" variable, "f" function, "o" operator, "bo"/"bc" brackets
		// only 0-9, a-z, (), +-*^% characters are allowed through -- no unsafe end quotations etc are passed to eval
		public function parse_query( $str, $symbolic=FALSE){
			$arr = array();
			
			for( $i=0; $i<strlen( $str); $i++){
				$sub = substr( $str, $i, 1);
				
				// space
				if( $sub == " "){}
				
				// number
				elseif( preg_match( "/[0-9\.]/", $sub)){
					while( preg_match( "/[e0-9\.]/", substr( $str, $i+1, 1)) || ( (preg_match( "/[\-]/", substr( $str, $i+1, 1)) || preg_match( "/[\+]/", substr( $str, $i+1, 1)))  && preg_match( "/[e]/", substr( $str, $i, 1)) ) ){
						$sub .= substr( $str, $i+1, 1);
						$i++;
					}
					$arr[] = array( "value" => $sub, "code" => "n");
				}
				
				// variable
				elseif( $sub == "$"){
					$sub = "";
					while( preg_match( "/[a-z0-9]/i", substr( $str, $i+1, 1)) ){
						$sub .= substr( $str, $i+1, 1);
						$i++;
					}
					$arr[] = array( "value" => $sub, "code" => "v");
				}
				
				// math function / number
				elseif( preg_match( "/[a-z]/i", $sub)){
					while( preg_match( "/[a-z]/i", substr( $str, $i+1, 1)) ){
						$sub .= substr( $str, $i+1, 1);
						$i++;
					}
					
					if( preg_match( "/^pi$/i", $sub)){
						if( $symbolic) $arr[] = array( "value" => "&pi;", "code" => "n");
						else $arr[] = array( "value" => "3.1415926535", "code" => "n");
					}
					elseif( preg_match("/^e$/i", $sub)){
						if( $symbolic) $arr[] = array( "value" => "e", "code" => "n");
						else $arr[] = array( "value" => "2.7182818284", "code" => "n");
					}
					else{
						$arr[] = array( "value" => $sub, "code" => "f");
						
					}
				}
				
				// operator
				elseif( preg_match( "/[\+\-\^\/\%\*]/", $sub)){
					$arr[] = array( "value" => $sub, "code" => "o");
				}
				
				// open bracket
				elseif( preg_match( "/[\(]/", $sub)){
					$arr[] = array( "value" => $sub, "code" => "bo");
				}
				
				// close bracket
				elseif( preg_match( "/[\)]/", $sub)){
					$arr[] = array( "value" => $sub, "code" => "bc");
				}
			}
			return $arr;
		}
		
				
		
		//------------------------------------------------------------------
		// Rejig Trig
		// Takes array of query objects. Converts all trigometric functions
		// from radians to degrees.
		//------------------------------------------------------------------
		
		private function rejig_trig( $arr){
			for($i=0; $i<count( $arr); $i++){
				if( $arr[$i]["code"] == "f"){
					
					// trig functions
					if( preg_match( "/^(cos|sin|tan)$/", $arr[$i]["value"])){
						
						$arg = array();
						$stackBrack = array();
						$j = $i;
						do{
							$j++;
							$arg[] = $arr[$j];
							
							if( $arr[$j]["code"] == "bo") $stackBrack[] = true;
							elseif( $arr[$j]["code"] == "bc"){
								if( count( $stackBrack) > 0) array_pop( $stackBrack);
								else die("Bracketing error");
							}
						} while( count( $stackBrack) > 0 && $j < count( $arr));
						
						$arrBuff1 = $arr;
						$arrBuff2 = $arr;				
						$arr= array_splice( $arrBuff1, 0, $i+1);
						$arr[] = array( "value" => "(", "code" => "bo");
						$arr[] = array( "value" => "deg2rad", "code" => "f");
						$arr = array_merge( $arr, $arg);
						$arr[] = array( "value" => ")", "code" => "bc");
						$arr = array_merge( $arr, array_splice( $arrBuff2, $j+1));
					}
					
					// trig functions
					elseif( preg_match( "/^(acos|asin|atan)$/", $arr[$i]["value"])){
						
						$arg = array();
						$stackBrack = array();
						$j = $i;
						do{
							$j++;
							$arg[] = $arr[$j];
							
							if( $arr[$j]["code"] == "bo") $stackBrack[] = true;
							elseif( $arr[$j]["code"] == "bc"){
								if( count( $stackBrack) > 0) array_pop( $stackBrack);
								else die("Bracketing error");
							}
						} while( count( $stackBrack) > 0 && $j < count( $arr));
						
						$arrBuff1 = $arr;
						$arrBuff2 = $arr;				
						$arr = array_splice( $arrBuff1, 0, $i);
						$arr[] = array( "value" => "rad2deg", "code" => "f");
						$arr[] = array( "value" => "(", "code" => "bo");
						$arr[] = $arrBuff2[$i];
						$arr = array_merge( $arr, $arg);
						$arr[] = array( "value" => ")", "code" => "bc");
						$arr = array_merge( $arr, array_splice( $arrBuff2, $j+1));
						$i += 2;
					}
				}
			}
			return $arr;
		}
		

		
		//------------------------------------------------------------------
		// Rejig Exponenets
		// Takes array of query objects. Converts expressions of teh form a^b
		// to (pow(a,b)). Returns rejigged array of objects.
		//------------------------------------------------------------------
		
		private function rejig_exponents( $arr){
			
			for($i = 0; $i < count( $arr); $i++){
				if( $arr[$i]["value"] == "^" ){
					
					$base = array();
					$stackBrack = array();
					$j = $i;
					do{
						$j--;
						$base[] = $arr[$j];
						
						if( $arr[$j]["code"] == "bc") $stackBrack[] = true;
						elseif( $arr[$j]["code"] == "bo"){
							if( count( $stackBrack) > 0) array_pop( $stackBrack);
							else die("Bracketing error");
						}
					} while( count( $stackBrack) > 0 && $j > 0);
					
					// reverse base order
					$base = array_reverse( $base);
					
					$exp = array();
					unset( $stackBrack);
					$k = $i;
					do{
						$k++;
						$exp[] = $arr[$k];
						
						if( $arr[$k]["code"] == "bo") $stackBrack[] = true;
						elseif( $arr[$k]["code"] == "bc"){
							if( count( $stackBrack) > 0) array_pop( $stackBrack);
							else die("Bracketing error");
						}
					} while( count( $stackBrack) > 0 && $k < count( $arr));
					
					
					// recursive to remove carets from exponent
					$exp = $this->rejig_exponents( $exp);
					
					// build
					$arrBuff1 = $arr;
					$arrBuff2 = $arr;				
					$arr = array_splice( $arrBuff1, 0, $j);
					$arr[] = array( "value" => "(", "code" => "bo");
					$arr[] = array( "value" => "pow", "code" => "f");
					$arr[] = array( "value" => "(", "code" => "bo");
					$arr = array_merge( $arr, $base);
					$arr[] = array( "value" => ",", "code" => "o");
					$arr = array_merge( $arr, $exp);
					$arr[] = array( "value" => ")", "code" => "bc");
					$arr[] = array( "value" => ")", "code" => "bc");
					$i = count( $arr) - 1;			
					$arr = array_merge( $arr, array_splice( $arrBuff2, $k+1));
				}
			}
			return $arr;
		}
	}
?>		
