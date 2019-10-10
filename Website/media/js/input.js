// this code contains several functions of use in obtaining and processing input from the Caladis home screen

// Object Arrays 
var BNRQueueV = new Array();
var BNRQueueR = new Array();
var variable = new Array();
var rejectedBionums = new Array();

// variables controlling how different Bionumber types are interpreted
var interpretPMAsLogNormal = 0;
var interpretToAsLogNormal = 0;

// set properties of various entities of use in the interface
function variable_obj(id,dist,param){                               // e.g. id=>"a", dist=>"norm", param=>[10,1]
    this.id=id;
    this.dist=dist;
    this.param=param;
};

function query_obj(value,code,error){                                // e.g. value=>"cos", code=>"f", error=>null
    this.value=value;
    this.code=code;
    this.error=error;    
}

function example_obj(title,desc,qi,vars){                                // e.g. title=>"My Example", desc=>"A brief description", qi=>"1 + #a", vars=>[ new variable(a, 'norm', [1,2])]
    this.title=title;
    this.desc=desc;
    this.qi=qi;    
    this.vars=vars;    
}

//******
//** processInterpretation
//** changes whether we interpret Bionumbers stored as e.g. X +- Y as normal
//** or lognormal variables. controlled by one variable interpretAsLogNormal
//** from input.js
function processInterpretation(obj) {
    switch(obj.selectedIndex)
  {
  case 0: interpretPMAsLogNormal = 0; interpretToAsLogNormal = 0; break;
  case 1: interpretPMAsLogNormal = 0; interpretToAsLogNormal = 1; break;
  case 2: interpretPMAsLogNormal = 1; interpretToAsLogNormal = 0; break;
  case 3: interpretPMAsLogNormal = 1; interpretToAsLogNormal = 1; break;
  }
}

//------------------------------------------------------------------
// Calculator Buttons
// Updates the query when operator buttons are pressed.
//------------------------------------------------------------------
function addNewOp( newOp){
    var i = variable.length + 1;
    if (newOp == '#var') {
    	$('#qi-input').val( function (a, c) { return c + newOp + i; } );
    } else {
    	$('#qi-input').val( function (a, c) { return c + newOp; } );
    }
    
    vFetch( $('#qi-input').val());    
    return;
}

//------------------------------------------------------------------
// Example
// Takes (optional) example type. Clears all data and generates an
// example.
//------------------------------------------------------------------
var exampleIndex = 0;
function example( callref){
    
    examples = new Array();
    examples.push( new example_obj( 'Snake eyes!', 'This basic example looks at the probability of different outcomes when rolling two dice and summing their values. Each die is represented by an uncertain variable (<b>#die1</b> and <b>#die2</b> respectively), corresponding to discrete uniform probability distributions, assigning equal probabilities to each number between 1 and 6. Caladis will compute the probability of obtaining each possible score, including snake eyes -- double ones -- which can be examined on the output screen.', '#die1 + #die2', [ new variable_obj('die1', 'duni', [1, 6]), new variable_obj('die2', 'duni', [1, 6])]));
    examples.push( new example_obj( 'Volume of a spherical cell', 'Say we\'ve observed the radius of a yeast cell and obtained a measurement of 2&mu;m, with an uncertainty of 0.5&mu;m. What are the range of possible volumes of that cell? We know that the volume of a sphere is (4/3)&pi;r<sup>3</sup>, so we use this formula, with a Normal distribution <b>#cellRadius</b> to describe our uncertainty on our measurment, to determine this range.', '(4/3)*pi*#cellRadius^3', [ new variable_obj('cellRadius', 'norm', [2, 0.5])]));    
    examples.push( new example_obj( 'Hairs on a human head', 'Here\'s another toy example: how many hairs does a human have on their head? A quick look suggests a hair density of 100-500 hairs per square centimetre, and most people\'s heads have a diameter of around 15cm. Let\'s put a Uniform distribution <b>#hairsPerSquareCm</b> on hair density and a Normal distribution <b>#diameterOfHead</b> on head diameter, and say that half the surface area of a human\'s (spherical) head has hair.', '#hairsPerSquareCm * ( 4 * pi * ( #diameterOfHead / 2 ) ^ 2 ) / 2', [ new variable_obj('hairsPerSquareCm', 'unif', [100, 500]), new variable_obj('diameterOfHead', 'norm', [15, 2])]));  
    examples.push( new example_obj( 'Size of a human egg cell', 'How large are human egg cells? This example illustrates the use of Bionumbers in calculations. Bionumber 101664 contains information on the volume of human egg cells, measured in &mu;L (microlitres). We include this Bionumber reference in the input expression by entering the # symbol followed by the Bionumber ID (hence <b>#101664</b>) and are then able to use it in our calculations. Other Bionumbers can be found on the <a href = "http://bionumbers.hms.harvard.edu/" target = "_blank">Bionumbers website</a> or through our browser (below the input expression box).', '#101664', [ new variable_obj('101664', '', [])]));     
    examples.push( new example_obj( 'Diffusion times for a protein', 'How long does it take for a protein to diffuse across the length of a bacterium? <a href = "http://bionumbers.hms.harvard.edu/" target = "_blank">Bionumbers</a> has measurements of cell length (x) and the cytoplasmic diffusion constant for GFP (D) in E. coli, in Bionumbers <b>#100001</b> and <b>#100193</b> respectively. The formula x<sup>2</sup> / 6D gives the characteristic timescale for diffusion.', '#100001^2 / (6*#100193)', [ new variable_obj('100001', '', []), new variable_obj('100193', '', [])]));    
    examples.push( new example_obj( 'Cornflakes and toys', 'This example uses a less common probability distribution. Suppose each Cornflakes box contains one of three toys, chosen randomly. How many Cornflakes do I have to eat before I collect all three toys? My first box will definitely have an uncollected toy inside. After that, the next box will have an uncollected toy with probability 2/3. After I get the second toy, the probability goes down to 1/3. A geometric distribution describes how many boxes I\'ll need to get a toy with a given probability: adding these distributions for each toy and multiplying by the distribution of Cornflakes per box (somewhere between 1000 and 10000) will give my answer.', '( #timeToFirst + #timeToSecond + #timeToThird ) * #cornflakesPerBox', [ new variable_obj('timeToFirst', 'geom', [1]), new variable_obj('timeToSecond', 'geom', [0.6666]), new variable_obj('timeToThird', 'geom', [0.3333]), new variable_obj('cornflakesPerBox', 'unif', [1000,10000])]));
    examples.push( new example_obj( 'Number of hydrogen ions in a cell', 'How many hydrogen ions are there in an E. coli cell? <a href = "http://bionumbers.hms.harvard.edu/" target = "_blank">Bionumbers</a> contains measurements of the pH (which is -log<sub>10</sub> [H<sup>+</sup>], where [H<sup>+</sup>] is the concentration of hydrogen ions), and the volume of a cell, in Bionumbers <b>#106518</b> and <b>#100003</b> respectively. We can use a simple formula, correcting for the units of measurement, to estimate the number of hydrogen ions in the cell.', '10^(-#106518) * (6*10^23) * #100003*10^(-15)', [ new variable_obj('106518', '', []), new variable_obj('100003', '', [])]));     

    var nnonnb = 3;   

    var i = exampleIndex;
    if(callref == -1)
    {
	exampleIndex = 0;
    }

    variable = [];                                                  // clear variable array
    $('.vi').remove();                                              // clear .vi elements
    $('.help-example').remove();
    i = exampleIndex;
    $('#qi-input').val( examples[i].qi );
    for( var j=0; j<examples[i].vars.length; j++){
        variable.push( examples[i].vars[j]);
        viBuild( examples[i].vars[j].id);
    }
    
    // construct HTML describing a particular example
    var strHTML =   '<div class="help help-example">' +
                        '<div class="help-arrow"></div>' +
                            '<a class="help-btn-del" onclick="$(this).parent().remove()"></a>' +
                            '<h3>' + examples[i].title + '</h3>' +
							'<p>' + examples[i].desc + '</p>' +
							'<p>Click <b>Calculate</b> to see the resulting probability distribution.</p>' +
                        '</div>';
    
    $('#help-area').prepend( strHTML);
    exampleIndex++;
    if(exampleIndex >= examples.length) exampleIndex = 0;

    return;
}

// Construct and display welcome message
function newWelcome()
{
    var str = '';

    $('.help-welcome').remove();

    str += '<div class="help help-welcome"><div class="help-arrow"></div><a class="help-btn-del" onclick="$(this).parent().remove(); $.cookie( \'welcomeToCaladis\', \'true\',  { expires: 1000, path: \'/\' } )"></a>';
    str += '<h3>Welcome to Caladis!</h3><p>Caladis is like a regular calculator but is useful when you\'re uncertain of the exact numbers to type in. The world is uncertain, so our back of the envelope calculations are always wrong. Caladis lets you calculate how wrong they could be. To use Caladis, type the expression you wish to calculate into the box above and click Calculate. Caladis uses \'probability distributions\' to represent uncertain quantities: to define a probability distribution use the &#35; symbol (e.g. <b>&#35;myDistribution</b>), or to see how it works, please click the example below.</p>';
    str += '<div class="help-btn-bar clear"><a class="help-btn" target = "_blank" href="/tutorial/">View tutorial</a><a class="help-btn" onclick="example(-1);">View first example</a><a class="help-btn" onclick="example(0);">View next example</a></div></div>';
    $('#help-area').prepend( str);

    return;
}

//------------------------------------------------------------------
// loadUrl 
// Gets q, v, a and h url parameters. Inserts q parameter into #qi-val.
// Inserts v parameter into variable array with distribution properties
// (if distribution is recognised). Inserts a and h parameters into select
// buttons.
//------------------------------------------------------------------
function loadUrl(){

    if( getUrl('q')) $('#qi-input').val( getUrl('q').replace( /\$/g, '#'));
	
    if( getUrl('v')){
        var vars = getUrl('v').split(';');
        for( var i=0; i<vars.length; i++){
            var var_prop = vars[i].split(':');
	    var dist_param = var_prop[1].split(',');
	    if( search( dist, dist_param[0]) > -1){                 // if legitimate distribution
		if( search( variable, var_prop[0]) == -1){          // if variable doesnt exist
                    variable.push( new variable_obj( var_prop[0], dist_param[0], dist_param.slice(1)));
                    viBuild( var_prop[0]);
                }
	    }
	    else{
		variable.push( new variable_obj( var_prop[0], null, [null]));
		viBuild( var_prop[0]);
	    }
        }
    }	
	
    if( getUrl('h')) $('#select-h').val( getUrl('h'));
    if( getUrl('a')) $('#select-a').val( getUrl('a'));
    if( getUrl('x')) $('#select-x').val( getUrl('x'));
    if( getUrl('n')) $('#select-n').val( getUrl('n'));
}


function getUrl(name){
    var results = new RegExp('[\\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if( results) return decodeURIComponent( results[1]);
    else return null;
}



//------------------------------------------------------------------
// Math N/F
// Takes a string and, if recognised as either a mathematical constant
// (a mathematical function), returns its lower-case self.
//------------------------------------------------------------------
function math_n( str){
    if( str.match( /^(pi)$/i)) return str.toLowerCase();
    return false;
}

function math_f( str){
    if(str.match( /^(abs|acos|acosh|asin|asinh|atan|atanh|ceil|cos|cosh|exp|floor|log|round|sin|sinh|tan|tanh)$/i)) return str.toLowerCase();
    return false;
}


//------------------------------------------------------------------
// qCheck
// Takes an array of query objects. Returns an array of query objects
// which has been corrected for mathematical correctness. Where it 
// is not possible to correct an error, errors are logged.
//------------------------------------------------------------------

function qCheck( arr){
    var brackStack = new Array();
    
    // Rule: First object cannot be ^,/,%,*,)
    if( arr[0].value.match( /[\^\/\%\*\)]/ )) arr[0].error = 'Please don\'t use an operator to start the expression.';//ot start with <b>' + arr[0].value + '</b> operator';
    
    else if( arr[0].code == 'bo' ) brackStack.push(true);
    
    for( var i=1; i<arr.length; i++){                               
        // Latter objects
        if( arr[i].code.match( /^(n|v)$/)){
            // Rule: [n/v/bc] [n/v] -> [n/bc] * [n/v]
            if( arr[i-1].code.match( /^(n|v|bc)$/) ){ 
                arr.splice(i, 0, new query_obj( '*', 'o', null));
            }
            
            // Rule: f [n/v] -> f( [n/v] )
            if( arr[i-1].code == 'f' ){
                arr.splice(i, 0, new query_obj( '(', 'bo', null));
                arr.splice(i+2, 0, new query_obj( ')', 'bc', null));
            }
        }
		
        else if( arr[i].value.match( /[\^\/\%\*]/ )){
			
            // Rule: ^,/,%,* operators cannot follow f or o
            if( arr[i-1].code.match( /^(f|o)$/)){
                arr[i].error = '<b>' + arr[i].value + '</b> operator should not follow a function or operator.';
            }
        }

        else if(arr[i].value.match( /[+\-]/ )) { 
          if(arr[i-1].value.match( /[+\-]/ ) ){
                arr[i].error = 'Please avoid +/- pairings.';
          }
      }
       
      if(arr[i].value.match( /[\^]/ )) {
        if(i == arr.length-1 || !arr[i+1].value.match( /[0-9\(]/ )) {
          arr[i].error = 'Please place negative or functional exponents in brackets e.g. 10^(-3), 2^(exp(#n)).';
        } 
      }

      else if( arr[i].code == 'bo' ) brackStack.push(true);
		
      else if( arr[i].code == 'bc'){
          if( brackStack.length==0){ 
              arr[i].error = 'Please ensure brackets are balanced.';
          }
          else{
              brackStack.pop();
                 
              // Rule: bc cannot follow f or o
              if( arr[i-1].code.match( /^(f|o)$/) ){
                  arr[i].error = 'Closing bracket should not follow <b>' + arr[i-1].value + '</b>';
              }
          }
      }
  }
    
  // Rule: Last object cannot be ^,/,*
  if( arr[ arr.length-1].value.match( /[\^\/\*]/ )) arr[ arr.length-1].error = 'Please don\'t use an operator to end the expression.';
    
  //add brackets
  for( var i=0; i<brackStack.length; i++){
    arr.push( new query_obj(')', 'b', null));
  }
  return arr;
}

//------------------------------------------------------------------
// qFlawless
// Takes array of query objects as input. Returns true iff all
// errors are null.
//------------------------------------------------------------------
function qFlawless( arr){
    for( var i=0; i<arr.length; i++){
        if( arr[i].error) return false;
    }
    return true;
}



//------------------------------------------------------------------
// qParse 
// Breaks str into array of query objects.
// Returns array of objects of the form [value, code, error].
//------------------------------------------------------------------
function qParse( str){
    var arr = new Array();
    var brackStack = new Array();
    var sub='';
    var numEChar = 0;
    var numPeriod = 0;

    for( var i=0; i<str.length; i++){
        sub = str.charAt(i);
        
        // space
        if( sub == ' '){}
        
	// numbers, including those of form 1e+4, 1e-3
        else if( sub.match( /[0-9]/)){
            while( str.charAt(i+1).match( /[e0-9\.]/)){
                if(str.charAt(i+1).match( /[e]/)) {
                   if(numEChar == 0 && str.charAt(i+2).match( /[\-\+0-9]/ ) ) {
                       numEChar = 1;
                       numPeriod = 0;
                   }
                   else arr.push( new query_obj('e', 'u', 'Unbalanced e signs in number format.'));
                }
                else if(str.charAt(i+1).match( /[\.]/)) {
                   if( numPeriod == 0 ) {
                      numPeriod = 1;
                   }
                   else arr.push( new query_obj('.', 'u', 'Unbalanced . signs in number format.'));
                }

                i++;
                sub += str.charAt(i);
            }
            numEChar = 0;
            numPeriod = 0;

            arr.push( new query_obj(sub, 'n', null));
        }
 
       
        // variables
        else if( sub=='#'){
            sub='';
            while( str.charAt(i+1).match( /[a-z0-9]/i)){
                i++;
                sub += str.charAt(i);
            }
            if( sub) arr.push( new query_obj(sub, 'v', null));
            else arr.push( new query_obj('#', 'u', 'Please only use # symbol as a variable identifier.'));
        }
        
        // math function / number
        else if( sub.match( /[a-z]/i)){
            while( str.charAt(i+1).match( /[a-z]/i)){
                i++;
                sub += str.charAt(i);
            }
            
            if( math_n( sub)) arr.push( new query_obj(sub, 'n', null));
            else if( math_f( sub)) arr.push( new query_obj(math_f( sub), 'f', null));
            else arr.push( new query_obj(sub, 'f', 'Couldn\'t recognise function <b>' + sub + '</b>'));
        }
        
        // operator
        else if( sub.match( /[\+\-\^\/\*]/ )){
            arr.push( new query_obj(sub, 'o', null));            
        }
        
        // open bracket
        else if( sub.match( /[\(\{\[]/ )){
            arr.push( new query_obj('(', 'bo', null));
        }

        // close bracket
        else if( sub.match( /[\)\}\]]/ )){
            arr.push( new query_obj(')', 'bc', null));
        }

        // unknown
        else{
            arr.push( new query_obj(sub, 'u', 'Couldn\'t recognise character <b>' + sub + '</b>'));
        }        
    }
    return arr;
}
							  


//------------------------------------------------------------------
// Rebuke
// Takes array of objects [value, class, errCode, errMsg]. Parts that
// contain errors are output to rebuke.
//------------------------------------------------------------------
function rebuke( arr){
    str =   '<div class="error">' +
                '<p>Sorry, there was a problem with your expression:</p>' + 
                    '<ul>';
    if( arr.length==0) str += '<li>No expression entered</li>';
    for( var i=0; i<arr.length; i++){
        if( arr[i].error){ str += '<li>' + arr[i].error + '</li>'; }      
    }
    str +=  '</ul></div>';
    $('#rebuke-area').append( str);
    
    // fade out
    setTimeout(function(){ 
        $('.error:first').animate({ opacity: 0, height: 0 }, 1000, function(){ $(this).remove() })
    }, 8000);
    
    return;
}

//------------------------------------------------------------------
// Submit 
// Takes array of arrays of the form [value, code, errCode, errMsg]. Constructs 
// url of the form input/?q=A*B&v=A:unif,1,10;B:norm,100,2&h=custom&a=rad
//------------------------------------------------------------------
function submit( arr){    
    var qStr = '', vStr = '';
        
    for( var i=0; i<arr.length; i++){        
        if( arr[i].code == 'v' ){
            qStr += '$' + arr[i].value;
        
            var v = search( variable, arr[i].value);
            if(vStr.length > 0) vStr += ';';
            vStr += variable[v].id + ':' + variable[v].dist + ',';
            for( p=0; p<variable[v].param.length; p++){
                if( p>0) vStr += ',';
                vStr += variable[v].param[p];
            }
        }
        else qStr += arr[i].value;
    }
    window.location.href = 'compute/?q=' + encodeURIComponent(qStr) + '&v=' + encodeURIComponent(vStr) + '&x=' + $('#select-x').val() + '&n=' + $('#select-n').val() + '&h=' + $('#select-h').val() + '&a=' + $('#select-a').val();
} 



//------------------------------------------------------------------
// vCheck
// Takes array of query objects. Checks that all variables have all 
// properties defined. Returns array of querry objects with errors.
// 
// cCheckParam: Takes variable id. Returns true if all distribution
// parameters have been defined.
//------------------------------------------------------------------
function vCheck( arr){    
    for( var i=0; i<arr.length; i++){
        if( arr[i].code == 'v'){
            if( !vCheckParam( arr[i].value)){ 
                arr[i].error = 'Please ensure that properties for #' + arr[i].value + ' are entered correctly.';
            }
        var dcResponse = vDistCheckParam(arr[i].value);

	    // interpret error responses
      switch(dcResponse)
            {
               case -1: arr[i].error = 'Please ensure that properties for #' + arr[i].value + ' are entered correctly.'; break;
               case 1: arr[i].error = '#' + arr[i].value + ' is a Normal distribution: please ensure sigma &ge; 0.'; break;
               case 2: arr[i].error = '#' + arr[i].value + ' is a uniform distribution: please ensure max > min.'; break;
               case 3: arr[i].error = '#' + arr[i].value + ' is a binomial distribution: please ensure N &ge; 0 and 0 &le; p < 1.'; break;
               case 4: arr[i].error = '#' + arr[i].value + ' is a Poisson distribution: please ensure lambda &ge; 0.'; break;
               case 5: arr[i].error = '#' + arr[i].value + ' is a Beta distribution: please ensure a > 0 and b > 0.'; break;
               case 6: arr[i].error = '#' + arr[i].value + ' is an exponential distribution: please ensure lambda > 0.'; break;
               case 7: arr[i].error = '#' + arr[i].value + ' is a Gamma distribution: please ensure k > 0 and theta > 0.'; break;
               case 8: arr[i].error = '#' + arr[i].value + ' is a Geometric distribution: please ensure 0 < p &le; 1.'; break;
               case 9: arr[i].error = 'Please ensure that properties for #' + arr[i].value + ' are entered as valid decimal numbers (e.g. 2.3, -4.56, 1e7).'; break;
               case 10: arr[i].error = '#' + arr[i].value + ' is a Log-normal distribution: please ensure mu > 0 and sigma &ge; 0.'; break;
               case 11: arr[i].error = '#' + arr[i].value + ' is a discrete Uniform distribution: please ensure max and min are integers and max > min.'; break;
           }
        }
    }
    return arr;
}

// check that a variable is a valid number
function isNumber(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

//------------------------------------------------------------------
// check that parameter choices for a given distribution make sense
//------------------------------------------------------------------
function vDistCheckParam( v_id){
    var v = search( variable, v_id);
    var v_dist = variable[v].dist; 
    if( !v_dist ) return -1; 

    var p1, p2;
    var d = search( dist, v_dist);

    // analyse parameters given distribution choice
    switch(v_dist)
    {
      case 'norm': p1 = variable[v].param[0]; p2 = variable[v].param[1];
              if((!isNumber(p1) && p1 != "0") || !isNumber(p2)) return 9;
              if(parseFloat(p2) < 0) return 1;
              break;
      case 'unif': p1 = variable[v].param[0]; p2 = variable[v].param[1];
              if(!isNumber(p1) || !isNumber(p2)) return 9;
              if(parseFloat(p1) == parseFloat(p2) || parseFloat(p1) >= parseFloat(p2)) return 2;
              break;
      case 'bino': p1 = variable[v].param[0]; p2 = variable[v].param[1];
              if(!isNumber(p1) || !isNumber(p2)) return 9;
              if(parseFloat(p1) < 0 || parseFloat(p2) < 0 || parseFloat(p2) >= 1) return 3;
              break;
      case 'pois': p1 = variable[v].param[0]; 
              if(!isNumber(p1)) return 9;
              if(parseFloat(p1) <= 0) return 4;
              break;
      case 'logn': p1 = variable[v].param[0]; p2 = variable[v].param[1];
              if((!isNumber(p1) && p1 != "0") || !isNumber(p2)) return 9;
              if(parseFloat(p1) <= 0 || parseFloat(p2) <= 0 || (parseFloat(p1) == 0 && parseFloat(p2) == 0)) return 10;
              break;
      case 'beta': p1 = variable[v].param[0]; p2 = variable[v].param[1]
              if(!isNumber(p1) || !isNumber(p2)) return 9;
              if(parseFloat(p1) <= 0 || parseFloat(p2) <= 0) return 5;
              break;
      case 'expo': p1 = variable[v].param[0];
              if(!isNumber(p1)) return 9;
              if(parseFloat(p1) <= 0) return 6;
              break;
      case 'gamm': p1 = variable[v].param[0]; p2 = variable[v].param[1];
              if(!isNumber(p1) || !isNumber(p2)) return 9;
              if(parseFloat(p1) <= 0 || parseFloat(p2) <= 0) return 7;
              break;
      case 'geom': p1 = variable[v].param[0]; 
              if(!isNumber(p1)) return 9;
              if(parseFloat(p1) <= 0 || parseFloat(p1) > 1) return 8;
              break;
      case 'duni': p1 = variable[v].param[0]; p2 = variable[v].param[1];
              if(!isNumber(p1) || !isNumber(p2)) return 9;
              if(parseFloat(p1) == parseFloat(p2) || parseFloat(p1) >= parseFloat(p2) || !(p1 % 1 === 0) || !(p2 % 1 === 0)) return 11;
              break;

    }

    return 0;
}


// vCheckParam
// check whether parameters have been entered for a given distribution
function vCheckParam( v_id){
    var v = search( variable, v_id);
    var v_dist = variable[v].dist; 
    if( !v_dist ) return false;                                     // if no distribution defined

    // pragmatically, this works in combination with the above; but the commented section below provides an alternative
    return true;    

/*    var d = search( dist, v_dist);
    for( var p=0; p<dist[d].param.length; p++){
        if(!variable[v].param[p]) return false;
    }
    return true;*/
}



//------------------------------------------------------------------
// vFetch
// Takes query str. Hide's all vi elements, then shows/builds the vi 
// elements for variables found in str.
//------------------------------------------------------------------
function vFetch( str){

    $('.vi').hide();

    for( var i=0; i<str.length; i++){        
        if( str.charAt(i)=='#'){
            var varStr='';
            while( str.charAt(i+1).match( /[a-z0-9]/i)){
                i++;
                varStr += str.charAt(i);
            }
            
            if( varStr){                                               // if variable defined
                if( search( variable, varStr) < 0){                    // if variable not recognised 
                    variable.push( new variable_obj(varStr, null, [null]));
                    viBuild( varStr);
                }
                else $('#vi-' + varStr).show();            
            }
        }
    }
    return;
}



//------------------------------------------------------------------
// vUpdate
// Takes an input element and uses its value to update the varProp 
// array. Changes the content of vi as neccessary.
//
// vUpdateClear: clears the variables properties and re-builds the 
// distribution btns.
//------------------------------------------------------------------

function vUpdate( e){
    var v = search( variable, $(e).data('v'));
    
    if( $(e).data('dist')){                                         // if distribution button
        variable[v].dist = $(e).data('dist');
        variable[v].param = [null]; 

        viBuildInner( $(e).data('v'));
    }
    else{
        variable[v].param[$(e).data('param')] = e.value;
    }
}

// vUpdateClear -- as above, but also remembers the ID of the distribution as a cleared entity
function vUpdateClear( e, v_id){
    var v = search( variable, $(e).data('v'));
    variable[v].dist = null;
    variable[v].param = [null]; 
    if((v_id > 100000 && v_id < 200000))
    rejectedBionums.push(v_id);
    viBuildInner( $(e).data('v'));
}

//****************
//** useBionum
//** gets data for the referenced Bionumber with Ajax
//** the reference is the name of the variable in e
//*****************
function useBionum( e){
  var v = search( variable, $(e).data('v'));
  var responsestr = '';

  if (window.XMLHttpRequest)
  {  // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      responsestr=xmlhttp.responseText;
      if(parseInt(responsestr.split(" ")[0]) >= 0)
      { // found the bionumber
	variable[v].dist = dist[parseInt(responsestr.split(" ")[1])].id;
	variable[v].param[0] = parseFloat(responsestr.split(" ")[2]);
        variable[v].param[1] = parseFloat(responsestr.split(" ")[3]);

        bnBuildInner( $(e).data('v'), responsestr);
      }
      else
      { 
        bnBuildInner( $(e).data('v'), responsestr);
      }
    }
  }

  xmlhttp.open("GET","/lib/getdata.php?q="+$(e).data('v'),true);
  xmlhttp.send();

}

//****************
//** useBionumByRef
//** gets data for the referenced Bionumber with Ajax
//** the reference is ref
//*****************
function useBionumByRef(v, ref){
  var responsestr = '';

  if (window.XMLHttpRequest)
  {  // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  }
  else
  {  // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      responsestr=xmlhttp.responseText;

      if(parseInt(responsestr.split(" ")[0]) >= 0)
      { // found the bionumber
        // retrieve bionumber description
        variable[BNRQueueV[0]].dist = dist[parseInt(responsestr.split(" ")[1])].id;
        variable[BNRQueueV[0]].param[0] = parseFloat(responsestr.split(" ")[2]);
        variable[BNRQueueV[0]].param[1] = parseFloat(responsestr.split(" ")[3]);
        // interpret as a distribution according to current settings
        if(parseInt(responsestr.split(" ")[1]) == 0 && interpretPMAsLogNormal == 1)
        {
          // use lognormal distribution
	  variable[BNRQueueV[0]].dist = dist[5].id;
        }
        if(parseInt(responsestr.split(" ")[1]) == 1 && interpretToAsLogNormal == 1)
        {
          // compute appropriate lognormal parameters and use
          var a = variable[BNRQueueV[0]].param[0];
          var b = variable[BNRQueueV[0]].param[1];
          if(a > 0 && b > 0)
          {
            variable[BNRQueueV[0]].dist = dist[5].id;
            var m = (Math.log(a)+Math.log(b))/2.;
            var s = m - Math.log(a);
            variable[BNRQueueV[0]].param[0] = Math.exp(m + s*s/2.);
            variable[BNRQueueV[0]].param[1] = Math.sqrt((Math.exp(s*s) - 1.)*Math.exp(2.*m + s*s));
          }
        }
        bnBuildInner( BNRQueueR[0], responsestr);
      }

      // update queue of submitted bionumber queries
      BNRQueueV.shift();
      BNRQueueR.shift();
      if(BNRQueueV.length > 0)
      {
        xmlhttp.open("GET","/lib/getdata.php?q="+BNRQueueR[0],true);
        xmlhttp.send();
      }
    }
  }

  BNRQueueV[BNRQueueV.length] = v;
  BNRQueueR[BNRQueueR.length] = ref;

  xmlhttp.open("GET","/lib/getdata.php?q="+BNRQueueR[0],true);
  xmlhttp.send();
}

//*********
//** bnBuildInner
//** build the end box for a Bionumber identification
//** if found, push the appropriate distn & params
//** otherwise display a message
//*********
function bnBuildInner( v_id, descstr){
  var str = '';
  var endstr = '';
  var optionstr = '';

  var v = search( variable, v_id);

  if(parseInt(descstr.split(" ")[0]) == -2)
  {
    // couldn't reach the SQL database
    endstr += '<p class = "vi-bold">Caladis was unable to access its Bionumber database. Sorry! Please use a different distribution.</p>';
  }
  else if(parseInt(descstr.split(" ")[0]) == -1)
  {
    // couldn't find the Bionumber
    endstr += '<p class = "vi-bold">Caladis was unable to identify Bionumber ' + descstr.split(" ")[1] + '.';
    if(parseInt(descstr.split(" ")[1]) >= 100000 && parseInt(descstr.split(" ")[1]) < 109000) endstr += '</p><p>This Bionumber may exist: if so, Caladis has been unable to interpret it as a probability distribution. Please refer to the appropriate <a class = "vi-text-link" href = "http://bionumbers.hms.harvard.edu/bionumber.aspx?&id=' + descstr.split(" ")[1] + '" target = "_blank">Bionumbers entry</a> for more information.</p>';
    else endstr += '</p><p>Bionumbers are referenced by a 6-digit number between 100000 and 109000. Please identify a Bionumber reference using the Bionumbers Browser or on the <a class = "vi-text-link" href = "http://bionumbers.hms.harvard.edu/" target = "_blank">database website</a>, or use a different distribution.</p>';
  }
  else
  {
    var infostr = descstr.split("***")[1];

    // parse the retrieved information to construct the Bionumber distribution
    endstr += '<p class = "vi-bold"><img src = "/media/img/btn/bionum.png">&nbsp;Bionumber ' + descstr.split("***")[1] + '</p>';
    endstr += '<p><a class="vi-text-link" href = "http://bionumbers.hms.harvard.edu/bionumber.aspx?&id=' + infostr.split(" ")[0] + '" target = "_blank">Entry on Bionumbers website</a></p>';
    endstr += '<p>Caladis has intepreted the raw data "' + descstr.split("***")[2] + '" as the following distribution.</p>';
    if(parseInt(descstr.split(" ")[0]) == 7 || parseInt(descstr.split(" ")[0]) == 8)
    { // automatic CV
      endstr += '<p><b>Caution: as this Bionumber seems to appear with no range information, Caladis has automatically assigned a CV of 50%. This may be inappropriate: please see the website above for more information. If this number is known to be exact, set the standard deviation below to zero.</b></p>';  
    }
    if(parseInt(descstr.split(" ")[0]) == 6 || parseInt(descstr.split(" ")[0]) == 9)
    { // dodgy interpretation
      endstr += '<p><b>Caution: this Bionumber seems to appear in a non-standard form, or may not contain interpretable information. Please verify that Caladis has correctly interpreted any values. See the website above for more information.</b></p>';  
    }

    var d = search( dist, variable[v].dist);
    endstr +=  '<p class="vi-bold">' + dist[d].name + ' Distribution</p>' +
'<p class="vi-text">' + dist[d].desc + ' <a class="vi-text-link" href="http://' + dist[d].link + '" target="_blank">' + dist[d].link + '</a></p>';

    // html for parameter boxes
    for( var p=0; p<dist[d].param.length; p++)
    {
      var value = '';
      if( variable[v].param[p])  value = variable[v].param[p];
      endstr +=  '<div class="vi-input-wrap">' +
'<p class="label">' + dist[d].param[p]  + ':</p>' +
'<input class="vi-input" value="' + value + '" data-v="' + v_id + '" data-param="' + p + '" onkeyup="vUpdate(this)" type="text" autocomplete="off" autocapitalize="off" spellcheck="false"/>' +
'</div>';  
    }
  }

  // final html for the distribution box
  if((v_id > 100000 && v_id < 200000))
    endstr += '<div class="vi-btn-bar"><a class="vi-btn-back" data-v="' + v_id + '" onclick="vUpdateClear(this, ' + v_id + ')">Change Distribution</a></div>';
  else
    endstr += '<div class="vi-btn-bar"><a class="vi-btn-back" data-v="' + v_id + '" onclick="vUpdateClear(this, ' + 0 + ')">Change Distribution</a></div>';

  $('#vi-' + v_id + ' .vi-content').html( str + optionstr + endstr);
}


// doesInclude
// crude but multi-platform function to check if an array contains a given value
function doesInclude(arr, obj) {
    for(var i=0; i<arr.length; i++) {
        if (arr[i] == obj) return true;
    }
    return false;
}

//------------------------------------------------------------------
// viBuild 
// Takes the variable v and build the vi element for it.
// 
// viBuildInner: Takes the variable v (and the distribution type 
// distType). If distType is not defined, the distribution btn set 
// will be built. Else, the relevant parameter inputs will be built. 
//------------------------------------------------------------------
function viBuild( v_id){
    var str =   '<div class="vi" id="vi-' + v_id + '">' +
                    '<p class="vi-title">#' + v_id + '</p>' +
                    '<div class="stage">' +
                        '<div class="vi-content clear">' +        
                        '</div>' +
                    '</div>' +
                '</div>'; 
    $('#vi-area').append( str);
    
    viBuildInner( v_id);
    return;
}

function viBuildInner( v_id){
    var str = '';
    var v = search( variable, v_id);

    // insert distribution buttons
    if( !variable[v].dist){
        str += '<p class="vi-text">Select a distribution type for this variable:';

        if((v_id > 100000 && v_id < 200000))
        {
          str += '<div class="vi-bnbtn-bar">';
          str += '<div class = "vi-bnbtn-btn" data-v = "'+ v_id + '"  onclick="useBionum(this)"><a class = "vi-bold" data-v = "' + v_id + '"  onclick="useBionum(this)"><img src = "/media/img/btn/bionum.png">&nbsp;Search for this Bionumber&nbsp;</a></div></div></p>';
        }

        // construct the HTML for the distribution box
        str += '<div class="vi-dist-holder">';

        for( var d=0; d<dist.length; d++){
            str +=  '<div class="vi-dist-wrap">' +
                        '<a class="vi-dist" data-v="' + v_id + '" data-dist="' + dist[d].id + '" onclick="vUpdate(this)">' +
                            '<p class="label img-' + dist[d].id + '">' + dist[d].name + '</p>' +
                        '</a>' +
                    '</div>';
        }
        str +=  '</div>';


        str +='<div class="vi-btn-bar">'+
                    '<a class="vi-btn-all" onclick="viShowAll(this)">Show All</a>' +
                '</div>';
    }
    
    // insert parameter inputs
    else{
        var d = search( dist, variable[v].dist);
		str +=  '<p class="vi-bold">' + dist[d].name + ' Distribution</p>' +
							  '<p class="vi-text">' + dist[d].desc + ' <a class="vi-text-link" href="http://' + dist[d].link + '" target="_blank">' + dist[d].link + '</a></p>';
        
        for( var p=0; p<dist[d].param.length; p++){
            
            var value = '';
            if( variable[v].param[p])  value = variable[v].param[p];
        
            str +=  '<div class="vi-input-wrap">' +
                        '<p class="label">' + dist[d].param[p]  + ':</p>' +
                        '<input class="vi-input" value="' + value + '" data-v="' + v_id + '" data-param="' + p + '" onkeyup="vUpdate(this)" type="text" autocomplete="off" autocapitalize="off" spellcheck="false"/>' +
                    '</div>';  
        }

        // finish up html for the bottom of the box
        if((v_id > 100000 && v_id < 200000))
          str += '<div class="vi-btn-bar"><a class="vi-btn-back" data-v="' + v_id + '" onclick="vUpdateClear(this, ' + v_id + ')">Change Distribution</a></div>'
        else
          str += '<div class="vi-btn-bar"><a class="vi-btn-back" data-v="' + v_id + '" onclick="vUpdateClear(this, ' + 0 + ')">Change Distribution</a></div>'
    }
    
    $('#vi-' + v_id + ' .vi-content').html( str );

    if((v_id > 100000 && v_id < 200000) && doesInclude(rejectedBionums, v_id) == false)
    {
      useBionumByRef( v, v_id);
    }

    return;
}

// viShowAll
// show all (or fewer) available distribution types
function viShowAll( e){
    $distHolder = $(e).parent().parent().find('.vi-dist-holder');
    var h_i = $distHolder.height();
    var w_i = $distHolder.width();
    
    if( h_i == 100){
        $distHolder.css('position', 'absolute').css('left', '99999px').css('height', 'auto').css('width', w_i);
        var h_f = $distHolder.height();
        $distHolder.css('position', 'relative').css('left', '0').css('height', h_i).css('width', 'auto');
        $distHolder.animate({ 'height': h_f}, function(){
            $distHolder.css('height', 'auto')
        });
        
        $(e).text("Show Less").toggleClass( 'vi-btn-all' ).toggleClass( 'vi-btn-less' );
    } 
    else{
        $distHolder.animate({ 'height': 100});
        $(e).text("Show All").toggleClass( 'vi-btn-all' ).toggleClass( 'vi-btn-less' );
    }
        
    return;    
}
