<!-- Caladis tutorial -->
<!-- Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. -->

<!DOCTYPE html>
<html lang="en">
<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/head.php"); ?>


<body>
<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/header.php"); ?>

<div class="container">

  <!-- this div contains the side menu for the tutorial chapters -->
  <div class="col-menu">
    <p class="menu-header">Getting Started</p>
    <ul class="menu-list">
      <li><a class="menu-item active" href="#chapter-1-1">Input</a></li>
      <li><a class="menu-item" href="#chapter-1-2">Compute</a></li>
    </ul>
    <p class="menu-header">Input Expressions</p>
    <ul class="menu-list">
      <li><a class="menu-item" href="#chapter-2-1">Operators</a></li>
      <li><a class="menu-item" href="#chapter-2-2">Functions</a></li>
      <li><a class="menu-item" href="#chapter-2-3">Constants</a></li>
    </ul>
    <p class="menu-header">Options</p>
    <ul class="menu-list">
      <li><a class="menu-item" href="#chapter-3-1">Standard Deviation Analysis</a></li>
      <li><a class="menu-item" href="#chapter-3-2">Sample Size</a></li>
      <li><a class="menu-item" href="#chapter-3-3">Using Bionumbers</a></li>
      <li><a class="menu-item" href="#chapter-3-4">Binning Method</a></li>
      <li><a class="menu-item" href="#chapter-3-5">Angle Unit</a></li>
    </ul>
  </div>

  <!-- this div contains the tutorial content -->
  <!-- indentation neglected for simplicity -->
<div class="col-main">
<!-- 1.1 -->
<div id="chapter-1-1">
<h3>Input</h3>
<p>Caladis makes it easy to perform calculations involving probability distributions. To perform a calculation, enter a mathematical expression in the input box on the home page. Mathematical expressions can include numbers, operators, functions and constants (see INPUT EXPRESSIONS for more details). A simple expression might take the form:</p>
<p class="tute-code">1 + 2</p>
<p>This expression does not include any probability distributions so will have a single solution (in this case 3). To make our expression probabilistic we must add a probability distribution variable. These are defined using the hash symbol, #, and can include upper-case letters, lower-case letters and numbers 0-9. Adding to our previous example, we define a distribution variable called "myDistribution":</p>
<p class="tute-code">1 + 2 + #myDistribution</p>
<p>As we type the variable's name, Caladis produces a popup enabling us to define what type of probability distribution our variable is:</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/vi_1.jpg" />
</div>
<p>The popup lists all of the available probability distributions in Caladis. To select a distribution type simply click on its icon.</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/vi_2.jpg" />
</div>
<p>When a distribution is selected (in this case the Normal distribution), the content of the popup changes so that the distribution parameters can be entered. In the case of the Normal distribution we must define the Mean and Standard Deviation. Once these details have been entered we click Calculate.</p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-1-2]').click()">Next Chapter</a>
</div>
</div>


<!-- 1.2 -->
<div id="chapter-1-2" style="display: none; ">
<h3>Compute</h3>
<p>The compute page shows the results from Caladis' calculations. At the top of the page is the input expression.</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/qd.jpg" />
</div>
<p>Distribution variables are highlighted in orange and a distribution's parameters can be seen by hovering the cursor over its name.</p>
<br />
<p>Caladis uses Monte Carlo sampling to generate its results. The input expression is calculated repeatedly, each time selecting a random number from each of the input probability distributions. The results of these calculations are displayed as a histogram on the Compute page:</p> 
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/hist.jpg" />
</div>
<p>Each bar of the histogram shows the percentage of data points that fell between the upper and lower bounds of that bar. The slider-bar beneath the histogram can be used to show the percentage of data points that lie between any two points. These points can be changed by moving the two orange tabs at either end of the slider-bar.</p>
<p>Summary statistics of the distribution, including mean, median, standard deviation and interquartile range, are presented in a box below the histogram.</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/stats.jpg" />
</div>
<p>A second box allows you to download the raw data that was used in producing the histogram (the set of samples from the output distribution), and the data grouped into the histogram bins.</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/download.jpg" />
</div>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-2-1]').click()">Next Chapter</a>
</div>
</div>


<!-- 2.1 -->
<div id="chapter-2-1" style="display: none; ">
<h3>Operators</h3>
<p>The following operators can be used in input expressions.</p>
<table class="table table-striped">
<thead>
<tr>
<th>Operator</th>
<th>Description</th>
</tr>                
</thead>
<tbody>
<tr>
<td>+</td>
<td>Addition</td>
</tr>
<tr>
<td>-</td>
<td>Subtraction</td>
</tr>
<tr>
<td>*</td>
<td>Multiplication</td>
</tr>                                                                        
<tr>
<td>/</td>
<td>Division</td>
</tr>  
<tr>
<td>^</td>
<td>Exponentiation, i.e. "to the power of"</td>
</tr>  
</tbody>
</table>
<br>
<p>If negative numbers or functions are to be included as exponents (following the ^ operator), please enclose them in brackets:
<p class = "tute-code">10^(-3) * 2^(exp(#n))</p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-2-2]').click()">Next Chapter</a>
</div>
</div>



<!-- 2.2 -->
<div id="chapter-2-2" style="display: none; ">
<h3>Functions</h3>
<p>The following functions can be used in input expressions.</p>
<table class="table table-striped">
<thead>
<tr>
<th>Function</th>
<th>Description</th>
</tr>                
</thead>
<tbody>
<tr>
<td>abs()</td>
<td>Returns the absolute value of a number.</td>
</tr>
<tr>
<td>acos()</td>
<td>Returns the arccosine of a number.</td>
</tr>
<tr>
<td>acosh()</td>
<td>Returns the inverse hyperbolic cosine of a number.</td>
</tr>
<tr>
<td>asin()</td>
<td>Returns the arcsine of a number.</td>
</tr>                        
<tr>
<td>asinh()</td>
<td>Returns the inverse hyperbolic sine of a number.</td>
</tr>  
<tr>
<td>atan()</td>
<td>Returns the arctangent of a number as a numeric value between -π/2 and π/2 radians.</td>
</tr>  
<tr>
<td>atanh()</td>
<td>Returns the inverse hyperbolic tangent of a number.</td>
</tr>
<tr>
<td>ceil()</td>
<td>Returns the value of a number rounded upwards to the nearest integer.</td>
</tr>                        
<tr>
<td>cos()</td>
<td>Returns the cosine of a number.</td>
</tr>
<tr>
<td>cosh()</td>
<td>Returns the hyperbolic cosine of a number.</td>
</tr>                          
<tr>
<td>exp()</td>
<td>Returns the value of e^x.</td>
</tr>
<tr>
<td>floor()</td>
<td>Returns the value of a number rounded downwards to the nearest integer.</td>
</tr>
<tr>
<td>log()</td>
<td>Returns the natural logarithm (base e) of a number.</td>
</tr>
<tr>
<td>round()</td>
<td>Rounds a number to the nearest integer.</td>
</tr>  
<tr>
<td>sin()</td>
<td>Returns the sine of a number.</td>
</tr>
<tr>
<td>sinh()</td>
<td>Returns the hyperbolic sine of a number.</td>
</tr>
<tr>
<td>tan()</td>
<td>Returns the tangent of an angle.</td>
</tr>
<tr>
<td>tanh()</td>
<td>Returns the hyperbolic tangent of an angle.</td>
</tr>                                                
</tbody>
</table>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-2-3]').click()">Next Chapter</a>
</div>
</div>


<!-- 2.3 -->
<div id="chapter-2-3" style="display: none; ">
<h3>Constants</h3>
<p>Entering</p>
<p class = "tute-code">pi</p>
<p>in the input expression automatically includes &pi;, approximately equal to 3.14159.</p>
<p>To enter numerical constants in scientific notation, use inputs of the form
<p class = "tute-code">1.5 * 10^5</p>
<p>If negative numbers or functions are to be used as exponents, enclose them in brackets:</p>
<p class = "tute-code">10^(-3) * 2^(exp(#n))</p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-3-1]').click()">Next Chapter</a>
</div>
</div>

<!-- 3.1 -->
<div id="chapter-3-1" style="display: none; ">
<h3>Standard Deviation Analysis</h3>
<p>Standard Deviation Analysis investigates how the standard deviation of the input variables affect the standard deviation and interquartile range of the output. For each distribution defined, the standard deviation is reduced by 10% and the effect of this change on the output distribution is calculated. The results of this analysis are shown beneath the histogram on the Compute page.</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/x.jpg" />
</div>
<br/>
<p><b>By default, Standard Deviation Analysis is turned off.</b></p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-3-2]').click()">Next Chapter</a>
</div>
</div>


<!-- 3.2 -->
<div id="chapter-3-2" style="display: none; ">
<h3>Sample Size</h3>
<p>The Sample Size option determines how many calculations will be used to generate the results. Increasing the Sample Size improves the accuracy of the results but takes longer to compute.</p>
<br/>
<p><b>By default, the sample size is 20,000.</b></p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-3-3]').click()">Next Chapter</a>
</div>
</div>

<!-- 3.3 -->
<div id="chapter-3-3" style="display: none; ">
<h3>Use Bionumbers</h3>
<p>The <a href = "http://bionumbers.hms.harvard.edu">Bionumbers website</a> is a repository of useful numbers in biology, encompassing a wide range of topics from rates of metabolic chemical reactions to the number of bacteria in a termite's gut. These numbers have been recorded from experiments and are usually stored as a value with some associated uncertainty (perhaps due to experimental errors, or natural variability). Caladis can help perform biological calculations by automatically including your choice of Bionumbers in your calculations.</p>
<p>Caladis automatically recognises Bionumber references preceded by hashes and include the corresponding distribution in your calculations: for example, entering "#100001" references the Bionumber corresponding to the length of an E. coli bacterium. Caladis interprets the raw data from the Bionumbers database as probability distributions by examining the format of the number and its associated range.</p> 
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/bn_2.jpg" />
</div>
<p>The "Bionumbers Browser" allows you to browse the full list of Bionumber references, to select Bionumbers using a three-tiered process by organism and type of Bionumber, or to search the Bionumbers database.</p>
<p>To select a Bionumber using the three-tiered process, first select the organism that you are interested in obtaining a Bionumber for from the top menu. The next menu will now display all types of Bionumber available for that organism, listed by the associated units of the Bionumbers: for example, cellular volumes may have associated units of &mu;m<sup>3</sup>. Select the type of Bionumber you are interested in from this menu. The bottom menu will now contain all Bionumbers of that unit in that organism, from which you may select the quantity of interest.</p>
<p>To search for a Bionumber, enter your search term(s) into the search bar available. Caladis will search the descriptions of each Bionumber for your terms. If you enter more than one search term, separated by spaces, Caladis will first attempt to find description that match all of the terms you enter. If no Bionumbers exist matching all your search search, Caladis will next attempt to find Bionumber matching at least one of your search terms. If any Bionumbers match these searches, they will be presented in the menu below the search bar.</p>
<div class="tute-img-wrap">
<img class="tute-img" src="../media/img/screenshot/bb.png" />
</div>
<p>After selecting a Bionumber from any of the above three approaches, a link will appear below your choice allowing you to insert that Bionumber directly into Caladis' expression bar.</p>
<p>Some Bionumbers appear in the database without an estimate of their associated uncertainty. Caladis will automatically assign a large uncertainty (a standard deviation of half the Bionumber's value) to these numbers, and provide a link to the website entry so you can check the appropriate values.</p>
<br/>
<p>An option in Caladis' option set enables you to influence how Caladis interprets some Bionumbers. Some values are present in the database with errors in the form "<b>x</b> +/- <b>y</b>", and some as "<b>a</b> to <b>b</b>" or "<b>a</b>-<b>b</b>" (as well as other variations). By default, Caladis interprets these types of value respectively as normally distributed with mean <b>x</b> and standard deviation <b>y</b> and uniformly distributed between <b>a</b> and <b>b</b>. The menu option allows you to interpret data of these type as log-normally distributed, which is more appropriate in some biological contexts (for example, when the Bionumber is known to be non-negative). Respectively, the log-normal distributions used have mean <b>x</b> and standard deviation <b>y</b>, or <b>a</b> and <b>b</b> as the +/- one standard deviation points in the distribution.</p>
<br/>
<p><b>By default, Bionumbers with "x +/- y" range information are intepreted as normal, and those with "x to y" range information are interpreted as uniform.</b></p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-3-4]').click()">Next Chapter</a>
</div>
</div>


<!-- 3.4 -->
<div id="chapter-3-4" style="display: none; ">
<h3>Binning Method</h3>
<p>The Binning Method option determines how the number of bins in the histogram be calculated. Rather than inputting the number of bins directly, Caladis lets users choose from three methods for deriving the optimal number of bins:</p>

<br />
<h5>Freedman-Diaconis (default)</h5>
<p>The Freedman-Diaconis rule is based on the interquartile range. It gives the width of each bin as:</p>
<p><code>w = 2 * IQR * ( n ^ (-1/3) )</code></p>
<p>where <code>IQR</code> is the interquartile range and <code>n</code> is the number of data points.</p>

<br />
<h5>Scott</h5>
<p>Scott's rule is optimal for random samples of normally distributed data, in the sense that it minimises the integrated mean squared error of the density estimate. It gives the width of each bin as:</p>
<p><code>w = 3.5 * &sigma; * ( n ^ (-1/3) )</code></p>
<p>where <code>&sigma;</code> is the standard deviation and <code>n</code> is the number of data points.</p>

<br />
<h5>Sturges</h5>
<p>Sturges' formula is derived from a binomial distribution and implicitly assumes an approximately normal distribution. It gives the number of bins as:</p>
<p><code>k = ceil( log<sub>2</sub>n + 1</code> )</p>
<p>where <code>n</code> is the number of data points.</p>

<div class="tute-btn-bar">
<a class="btn" onclick="$('a[href=#chapter-3-5]').click()">Next Chapter</a>
</div>
</div>


<!-- 3.5 -->
<div id="chapter-3-5" style="display: none; ">
<h3>Angle Unit</h3>
<p>The Angle Unit option determines the unit used in trigonometric functions.</p>
<br/>
<p><b>By default, the angle unit is radians.</b></p>
</div>

</div><!--/.col-main-->

</div><!--/.container-->

		
<?php include( $_SERVER['DOCUMENT_ROOT'] . "/elements/footer.php"); ?>


</body>
</html>
