<?php
  // Query the SQL database for reference q
  // return in format read by Ajax call in input.js

  // Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
  // Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
  // Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

  error_reporting(E_ERROR);
  $vid = $_GET['q']+0.0;

  // In the true Caladis code these arguments are replaced by those which grant access to the Bionumbers SQL database
  $con = mysql_connect("database.domain","username","password");
  if (!$con)
  {
    printf("-2\n");
  }
  else
  {
    mysql_select_db("caladis", $con);

    $result = mysql_query("SELECT * FROM bionumnew WHERE Reference = \"".$vid."\"");
    $c = 0;
    while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
      printf("%d %d %.5f %.5f***%s -- %s in %s (units of %s)***%s\n", $row[1], $row[4], $row[5], $row[6], $row[0], $row[2], $row[3], $row[7], $row[8]);  
      $c++;
    }

    if($c == 0)
    {
      printf("-1 %s\n", $vid);
    }
  }
?>