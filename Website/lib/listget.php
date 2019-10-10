<?php

// This code retrieves data from the Bionumbers database to populate various levels of the lists in the Bionumbers Browser

  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

// These arguments control which data is retrieved
$nn = $_GET['nn'];
$n1 = $_GET['n1'];
$n2 = $_GET['n2'];
$n3 = $_GET['n3'];

if($nn == -1)
{
  // this module retrieves a brief summary of all entries for use in the top list

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

    $result = mysql_query("SELECT * FROM bionumnew");
    $nNumber = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
      $numberNames[] = $row[0].' -- '.$row[2].' in '.$row[3];
      $numberRefs[] = $row[0];
      $nNumber++;
    }

    $str .= '<select width="180" style="width:180px" onchange = "displayBionumber(1, this.value);"><option name = "placehold" value = "0">Please select a Bionumber...</option>';
    for($i = 0; $i < $nNumber; $i++)
    {
      $str .= '<option value = "'.strval($numberRefs[$i]).'">'.$numberNames[$i].'</option>';
    }
    $str .= '</select>';
  }
}
else {

  // this module retrieves data for use in the lower lists

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

    $result = mysql_query("SELECT DISTINCT Organism FROM bionumnew");
    $nOrg = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
      $orgNames[] = $row[0];
      $nOrg++;
    }

  if($nn >= 1)
  {
    $result = mysql_query("SELECT DISTINCT Units FROM bionumnew WHERE Organism = \"".$orgNames[$n1-1]."\"");
    $nType = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
      $typeNames[] = $row[0];
      $nType++;
    }
  }

  if($nn >= 2)
  {
    $result = mysql_query("SELECT * FROM bionumnew WHERE Organism = \"".$orgNames[$n1-1]."\" AND Units = \"".$typeNames[$n2-1]."\"");
    $nNumber = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
      $numberNames[] = $row[0].' -- '.$row[2];
      $numberRefs[] = $row[0];
      $nNumber++;
    }
  }
}

// we now output the html corresponding to the data we have retrieved

$str = '';
if($nn == 0)
{
  $str .= '<select  width="180" style="width:180px" onchange = "getList2(this.value);"><option name = "placehold" value = "0">Please select an organism...</option>';
  for($i = 0; $i < $nOrg; $i++)
  {
    $str .= '<option value = "'.strval($i+1).'">'.$orgNames[$i].'</option>';
  }
  $str .= '</select>';
}
else if($nn == 1)
{
  $str .= '<select  width="180" style="width:180px" onchange = "getList3('.strval($n1).', this.value);"><option name = "placehold" value = "0">Please select a type of Bionumber...</option>';
  for($i = 0; $i < $nType; $i++)
  {
    $str .= '<option value = "'.strval($i+1).'">'.$typeNames[$i].'</option>';
  }
  $str .= '</select>';
}
else if($nn == 2)
{
  $str .= '<select  width="180" style="width:180px" onchange = "displayBionumber(2, this.value);"><option name = "placehold" value = "0">Please select a Bionumber...</option>';
  for($i = 0; $i < $nNumber; $i++)
  {
    $str .= '<option value = "'.strval($numberRefs[$i]).'">'.$numberNames[$i].'</option>';
  }
  $str .= '</select>';
}
}

// final output
echo $str;

?>