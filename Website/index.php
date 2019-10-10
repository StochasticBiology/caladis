<!-- Caladis home page -->
<!-- Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication.
-->

<!DOCTYPE html>
<html lang="en">

<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/head.php"); ?>

<?php
  // This module stores some info about visitors
  $ip=$_SERVER['REMOTE_ADDR'];
  $fp = fopen("moreinfo.txt", "a");
  $datetime = strftime('%c');
  fputs($fp, "$ip");
  fputs($fp, " $datetime");
  fputs($fp, "\n");
  fclose($fp);
?>

<!-- header -->
<body style="position: relative; z-index: 0;" onunload="">

<!-- load Javascript functionality for the index screen -->
<script type = "text/javascript" src = "/media/js/indexscreen.js"></script>

<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/header.php"); ?>

<!-- Bionumbers Browser (html will be rewritten with interactions) -->
<div id = "wrapper" class = "paneMiddle">
  <div id = "insidewrap"> 
    <font size = "-1" color = "#000000">
    <table width = "100%">
      <tr>
        <td><img src = 'media/img/btn/bionum.png'>&nbsp;&nbsp;<font size = "+2" color = "#F38630"><b>Bionumbers Browser</b></font>&nbsp;&nbsp;&nbsp;&nbsp;[<a target = "_blank" href = "tutorial/index.php#chapter-3-3" target="_blank">?</a>]</td>
        <td valign = "top"><right><a style="text-decoration:none" href = 'javascript:void(0)' onclick = 'processCross(0);'><font size = "+3" color = "#F38630"><b>&times;</b></font></a></td>
      </tr>
    </table>
  
    <div id = "loading">
      <center>Loading...<br><img src = 'media/img/loader/bar.gif'></center>
    </div>
    <p align = "left">[<a class = "vi-text-link" href = "http://bionumbers.hms.harvard.edu/">Bionumbers website</a>]</p>

    <p align = "left"><b>Browse all available Bionumbers:</b></p>
    <center>
    <div id = "list0" class = "select-wrap">
      <select width="180" style="width:180px">
        <option value = "0" name="placehold">Reading from database...</option>
      </select>
    </div>
    <div id = "output1"></div>

    <p align = "left"><b>or select:</b></p>
    <center>
    <font color = "#888888">Organism:</font><br>
    <div id = "list1" class = "select-wrap">
      <select width="180" style="width:180px">
        <option value = "0" name="placehold">Reading from database...</option>
      </select>
    </div>
    <font color = "#888888">Type of Bionumber (by units):</font><br>
    <div id = "list2" class = "select-wrap">
      <select width="180" style="width:180px">
        <option name="placehold">Please select an organism...</option>
      </select>
    </div>
    <font color = "#888888">Bionumber:</font><br>
    <div id = "list3" class = "select-wrap">
      <select width="180" style="width:180px">
        <option name="placehold">Please select an organism...</option>
      </select>
    </div>
    <div id = "output2"></div>

    <p align = "left"><b>or search:</b></p>
    <center>
    <input type="text" id="bionumtextsearch" name="bionumtextsearch"> <button onclick = "getBionumSearchReal()" class = "bn-search">Search</button> <br>
    <div id = "listsearch" class = "select-wrap">
      <select width="180" style="width:180px">
        <option value = "0" name="placehold">Please enter a search term...</option>
      </select>
    </div>
    <div id = "output3"></div>
  </div>
</div>
<!-- end Bionumbers browser -->
   
<!-- main input bar --> 
<div class="container">
  <div id="rebuke-area" class="area"></div>
  <div class="indent" id = "iaincontainer">
  <div class="qi">
    <input id="qi-input" class="qi-input" type="text" placeholder="Enter an expression..." autocomplete="off" autocapitalize="off" spellcheck="false"/>
    <a id="qi-btn" class="qi-btn">Calculate</a>
    <a id="qi-clr" class="qi-clr" onclick = "clearInput();">Clear</a>
  </div>
  <br>
  Enter what you want to calculate, using the <b>#</b> symbol to denote any uncertain quantities, e.g. <b>4/3*pi*#cellRadius^3</b>. Caladis will then ask you for information about uncertain quantities, and calculate the probabilities of different answers. 
  <!-- Input buttons, primarily for mobile devices -->
  <div class="indent" id = "buttonsbit">
    Go ahead! Use these buttons and/or the space above to input an expression to calculate. <br><br><center>
    <button type="button" class="operator variable" title="Include a new probability distribution" onclick="addNewOp('#var')">New uncertain quantity</button>
    <br>
    <button type="button" class="operator" title="Add" onclick="addNewOp('+')">+</button>
    <button type="button" class="operator" title="Subtract" onclick="addNewOp('-')">-</button>
    <button type="button" class="operator" title="Multiply" onclick="addNewOp('*')">&times;</button>
    <button type="button" class="operator" title="Divide" onclick="addNewOp('/')">&divide;</button>
    <br>
    <button type="button" class="operator" title="Raise to the power of" onclick="addNewOp('^')">^</button>
    <button type="button" class="operator" title="Open bracket" onclick="addNewOp('(')">(</button>
    <button type="button" class="operator" title="Close bracket" onclick="addNewOp(')')">)</button>
    <button type="button" class="operator" title="Hash (labels an uncertain quantity)" onclick="addNewOp('#')">#</button> 
    <br>
    <button type="button" class="operator" title="0" onclick="addNewOp('0')">0</button>
    <button type="button" class="operator" title="1" onclick="addNewOp('1')">1</button>
    <button type="button" class="operator" title="2" onclick="addNewOp('2')">2</button>
    <button type="button" class="operator" title="3" onclick="addNewOp('3')">3</button>
    <button type="button" class="operator" title="4" onclick="addNewOp('4')">4</button>
    <button type="button" class="operator" title="5" onclick="addNewOp('5')">5</button>
    <button type="button" class="operator" title="6" onclick="addNewOp('6')">6</button>
    <button type="button" class="operator" title="7" onclick="addNewOp('7')">7</button>
    <button type="button" class="operator" title="8" onclick="addNewOp('8')">8</button>
    <button type="button" class="operator" title="9" onclick="addNewOp('9')">9</button>
    <button type="button" class="operator" title="Decimal point" onclick="addNewOp('.')">.</button>
    </center>
  </div>
  <!-- end buttons -->
  <a id="qi-btn-alt" class="qi-btn-alt">Calculate</a>

  <!-- options for calculation -->
  <ul class="qi-option-list clear">
    <li>
      <p class="label">Standard Deviation Analysis [<a target = "_blank" href = "tutorial/index.php#chapter-3-1" target="_blank">?</a>]:</p>
      <div class="select-wrap">
        <select id="select-x">
	  <option value="off" selected = "selected" name = "placehold">Off</option>
	  <option value="on">On</option>
        </select>
      </div>                      
    </li>
    <li>
      <p class="label">Sample size [<a target = "_blank" href = "tutorial/index.php#chapter-3-2" target="_blank">?</a>]:</p>
      <div class="select-wrap">
        <select id="select-n">
	  <option value="s">Small (10000)</option>
	  <option value="m" selected="selected" name = "placehold">Medium (20000)</option>
	  <option value="l">Large (50000)</option>
        </select>
      </div>       
    </li>
    <li>
      <p class="label">Binning method [<a href = "tutorial/index.php#chapter-3-4" target="_blank">?</a>]:</p>
      <div class="select-wrap">
         <select id="select-h">
           <option value="fd" selected = "selected" name = "placehold" >Freedman-Diaconis</option>
           <option value="sturges">Sturges</option>
           <option value="scott">Scott</option>
         </select>
      </div>       
    </li>
    <li>
      <p class="label">Angle unit [<a href = "tutorial/index.php#chapter-3-5" target="_blank">?</a>]:</p>
      <div class="select-wrap">
        <select id="select-a">
          <option value="rad" selected = "selected" name = "placehold">Radians</option>
          <option value="deg">Degrees</option>
        </select>
      </div>                      
    </li>
    <!-- New addition: allows the user to specify whether +/- Bionumbers are interpreted as Normal or logNormal distributions. hidden if bionumbers is turned off. -->
    <li>
      <div style="width:170px" id = "interpret_switch">
        <p align = "left" class="label">Interpret Bionumbers [<a target = "_blank" href = "tutorial/index.php#chapter-3-3" target="_blank">?</a>]:</p>
	  <div class="select-wrap">
	    <select id="select-a" onchange = "processInterpretation(this)">
	      <option value="interpret_normal_uniform" selected = "selected" name = "placehold">"x +/- y" normal; "x to y" uniform</option>
	      <option value="interpret_normal_lognormal">"x +/- y" normal; "x to y" log-normal</option>
	      <option value="interpret_lognormal_uniform">"x +/- y" log-normal; "x to y" uniform</option>
	      <option value="interpret_lognormal_lognormal">"x +/- y" log-normal; "x to y" log-normal</option>
            </select>
	  </div>           
        </div>           
      </li>
    </ul>
</div>
<!-- end main input bar -->

<!-- interface control buttons -->
<div id = "link-list-iainmod">           
  <ul class="qi-link-list clear">
    <li><a onclick="$('.qi-option-list').slideToggle();"><i class="icon-list"></i>Options</a></li>
    <li><a onclick="example(1);"><i class="icon-hist"></i>(next) Example Calculation</a></li>
    <li><a onclick = "processCross(1);"><i class="icon-bnb"></i>&nbsp;Bionumbers Browser</a></li>
    <li><a onclick="togglebuttons(1);"><i class="icon-list"></i>&nbsp;Toggle Input Buttons</a></li>
    <li><a onclick="newWelcome(); $('.help-welcome').fadeIn('fast');"><i class="icon-msg"></i>&nbsp;Welcome Message</a></li>
  </ul>
</div>
<!-- end interface control buttons -->            

<!-- other areas for use with popups -->
<div id="help-area" class="area"></div>
<div id="vi-area" class="area"></div>

</div>

<!-- check cookies: if welcome screen has been dismissed, don't show it, otherwise do. if a mobile device is detected, set buttons to visible, otherwise invisible  -->
<script type="text/javascript">
  if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    document.getElementById('buttonsbit').style.display = 'block';
  }
  else {
    document.getElementById('buttonsbit').style.display = 'none';
  }

  if( !$.cookie('welcomeToCaladis')) { 
    newWelcome(); 
    $('.help-welcome').fadeIn('fast');
  }
</script>

<!-- footer -->
<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/footer.php"); ?>

<!-- Input Handler -->
<script type="text/javascript">
  window.onresize = function(event)
  { 
    // if the Bionumbers browser is open when window is resized, reposition it
    if(document.getElementById('wrapper').style.display == 'block') { 
      processCross(1); 
    } 
  }

  $('#bionumtextsearch').keyup(function(e){
    // process Enter key in bionumbers text search
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13)  getBionumSearchReal();
  });

  $('#qi-input').keyup(function(e){
    // process Enter key in calculation input
    var code = (e.keyCode ? e.keyCode : e.which);
    if(code == 13)  $('#qi-btn').click();
    vFetch( $('#qi-input').val());
  });

  $('#qi-btn, #qi-btn-alt').click(function(){
    // process Calculate button click by checking query
    // then either rebuking, if errors found, or submitting
    var query = qParse( $('#qi-input').val())
    if( query.length == 0){
      rebuke(query); 
      return;
    }
    query = vCheck( qCheck( query));		   
    if( !qFlawless(query)){ 
      rebuke(query); 
      return;
    }
    submit( query);
  });

  $('.vi').remove();
  loadUrl();
</script>

</body>
</html>
