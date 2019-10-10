<?php

// This code facilitates a search of the Bionumbers SQL database

  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

$searchstr = $_GET['s'];

$pieces = explode(" ", $searchstr);

error_reporting(E_ERROR);

// In the true Caladis code these arguments are replaced by those which grant access to the Bionumbers SQL database
$con = mysql_connect("database.domain","username","password");

if (!$con)
{
  printf("-2\n");
}
else
{
  mysql_select_db("caladis", $con);

  // first we attempt a search for all terms together
  $qStr = "SELECT * FROM bionumnew WHERE Description LIKE '%" . $pieces[0] . "%' "; 
  for($i = 1; $i < count($pieces); $i++)
  {
    $qStr .= "AND Description LIKE '%" . $pieces[$i] . "%' ";
  }

  $result = mysql_query($qStr);
  $nNumber = 0;
  while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
  {
    $numberNames[] = $row[0].' -- '.$row[2].' in '.$row[3];
    $numberRefs[] = $row[0];
    $nNumber++;
  }
  $usedOr = 0;

  if($nNumber == 0)
  {
    // if the above "and" search returned no results, we try an "or" search
    $qStr = "SELECT * FROM bionumnew WHERE Description LIKE '%" . $pieces[0] . "%' "; 
    for($i = 1; $i < count($pieces); $i++)
    {
      $qStr .= "OR Description LIKE '%" . $pieces[$i] . "%' ";
    }
    $result = mysql_query($qStr);
    $nNumber = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
      $numberNames[] = $row[0].' -- '.$row[2].' in '.$row[3];
      $numberRefs[] = $row[0];
      $nNumber++;
    }
    $usedOr = 1;
  }

  if($nNumber == 0)
  {
    // still nothing: no results found
    $str .= '<select width="180" style="width:180px" onchange = "displayBionumber(3, this.value);"><option name = "placehold" value = "0">No search results found. Please try a new search.</option>';
  }
  else
  {
    // construct output html
    $str .= '<select width="180" style="width:180px" onchange = "displayBionumber(3, this.value);"><option name = "placehold" value = "0">Please select from results for "';
    for($i = 0; $i < count($pieces); $i++)
    {
      if($i > 0 && $usedOr == 0) { $str .= " AND "; }
      if($i > 0 && $usedOr == 1) { $str .= " OR "; }
     $str .= $pieces[$i]; 
    } 
   $str .= '"...</option>';
  } 

  for($i = 0; $i < $nNumber; $i++)
  {
    $str .= '<option value = "'.strval($numberRefs[$i]).'">'.$numberNames[$i].'</option>';
 }
 
  $str .= '</select>';
}

// final output
echo $str;

?>