# this script parses a CSV download of the Bionumbers database
# eventually producing a PHP script that populates an SQL database with Bionumber information in a format that Caladis will process

# Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
# Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
# Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. 

echo "-- Parsing database..."

# remove or replace awkward characters from the source file, producing a temporary file bionum1.txt
echo "Removing pipes..."
sed "s/\|//g" BioNumbers.csv > bionum1.txt
echo "Removing quotes..."
sed -i "s/\"//g" bionum1.txt
echo "Replacing percents..."
sed -i "s/%/Å£/g" bionum1.txt

# replace other awkward characters with html-compatible equivalents, using a different script for technical reasons
echo "Replacing dodgy characters..."
./dodgychars.sh

# cast the CSV into a more appropriate format for subsequent processing
echo "Formatting..."
awk 'BEGIN {FS = "\t"}; { print $1, "\t", $2, "\t", $3, "\t", $4, "\t", $5, "\t", $6, "|", $4, " ", $5, " ", $6; }' bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt
cp bionum1.txt rawbn1.txt

# make exponential formatting consistent
echo "Swapping x10^"
awk 'BEGIN {FS = "|";}; 
{ 
if($1 ~ /\x10\^/) 
{ 
  split($1, a, "\x10\^"); 
  n = 0; for(i in a) n++;
  for(i = 1; i <= n; i++) 
  { 
    printf(a[i]); 
    if(i != n) 
      printf("E"); 
  } 
  printf("|%s\n", $2);
} 
else print $0;

}'  bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt
cp bionum2.txt bionuma1.txt

echo "Swapping *10^"
awk 'BEGIN {FS = "|"; n = 0;}; 
{ 
if($1 ~ /\*10\^/) 
{ 
  split($1, a, "\*10\^"); 
  n = 0; for(i in a) n++;
  for(i = 1; i <= n; i++) 
  { 
    printf(a[i]); 
    if(i != n) 
      printf("E"); 
  } 
  printf("|%s\n", $2);
} 
else print $0;

}'  bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt
cp bionum2.txt bionumb1.txt

echo "Swapping 10^"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /10\^/) 
{ 
  split($1, a, "10\^"); 
  n = 0; for(i in a) n++;
  for(i = 1; i <= n; i++) 
  { 
    printf(a[i]); 
    if(i != n) 
      printf("1E"); 
  } 
  printf("|%s\n", $2);
} 
else print $0;

}'  bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt
cp bionum2.txt bionumc1.txt

# remove characters of no further use
echo "Removing ~"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /\~/) 
{ 
  split($1, a, "\~"); 
  n = 0; for(i in a) n++;
  for(i = 1; i <= n; i++) 
  { 
    printf(a[i]); 
  } 
  printf("|%s\n", $2);
} 
else print $0;

}'  bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt
cp bionum2.txt bionume1.txt

echo "Removing ,"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /\,/) 
{ 
  split($1, a, ","); 
  n = 0; for(i in a) n++;
  for(i = 1; i <= n; i++) 
  { 
    printf(a[i]); 
  } 
  printf("|%s\n", $2);
} 
else print $0;

}'  bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt
cp bionum2.txt bionume1.txt

# make "+/-" formatting consistent
echo "Swapping +/-"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /\+\/\-/) 
{ 
  split($1, a, "\+\/\-"); 
  n = 0; for(i in a) n++;
  for(i = 1; i <= n; i++) 
  { 
    printf(a[i]); 
    if(i != n)
      printf("+-");
  } 
  printf("|%s\n", $2);
} 
else print $0;

}'  bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt

# remove links to external Tables, Figures, Databases, Graphs
echo "Removing Figure links"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /[^a-zA-Z0-9][Ff]igure/) 
{ 
  where = match($1, /[^a-zA-Z0-9][Ff]igure/);
  a = substr($1, 1, where-1);
  printf("%s|%s\n", a, $2);
}
else print $0;
}' bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt

echo "Removing Table links"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /[^a-zA-Z0-9][Tt]able/) 
{ 
  where = match($1, /[^a-zA-Z0-9][Tt]able/);
  a = substr($1, 1, where-1);
  printf("%s|%s\n", a, $2);
}
else print $0;
}' bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt

echo "Removing Database links"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /[^a-zA-Z0-9][Dd]atabase/) 
{ 
  where = match($1, /[^a-zA-Z0-9][Dd]atabase/);
  a = substr($1, 1, where-1);
  printf("%s|%s\n", a, $2);
}
else print $0;
}' bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt

echo "Removing Graph links"
awk 'BEGIN {FS = "|"}; 
{ 
if($1 ~ /[^a-zA-Z0-9][Gg]raph/) 
{ 
  where = match($1, /[^a-zA-Z0-9][Gg]raph/);
  a = substr($1, 1, where-1);
  printf("%s|%s\n", a, $2);
}
else print $0;
}' bionum1.txt > bionum2.txt
cp bionum2.txt bionum1.txt

# remove whitespace
echo "Removing whitespace"
sed -i "s/\t[ ]*/\t/g" bionum1.txt
sed -i "s/[ ]*\t/\t/g" bionum1.txt

# re-introduce percentage signs (replaced earlier for ease of processing)
echo "Re-replacing percents..."
sed -i "s/Å£/%/g" bionum1.txt

#### We now interpret the cleaned database as distributions according to the format of the uncertainty presented for each Bionumber

echo "-- Interpreting output as distributions..."

awk '
BEGIN {FS = "\t" }; 
{   
  if($5 ~ /[\)][-]/) 
  {
    split($5, a, "\)-\(");
    str1 = sprintf("echo \"%s\" | sed \"s/(//g\"", a[1]);
    str2 = sprintf("echo \"%s\" | sed \"s/)//g\"", a[2]);
    str1 | getline out1;
    str2 | getline out2;
    print $1, "| 0 |", $2, "|", $3, "| 1 |", out1, "|", out2, "|", $6;   
  }
  else if($5 ~ /[0-9][ ]?[-]/) 
  {
    split($5, a, "-");
    print $1, "| 1 |", $2, "|", $3, "| 1 |", a[1], "|", a[2], "|", $6;   
  }
  else if($5 ~ /\</) 
  {
    split($5, a, "\<");
    str1 = sprintf("echo \"%s\" | sed \"s/=//g\"", a[2]);
    str1 | getline out1;
    print $1, "| 2 |", $2, "|", $3, "| 1 |", 0, "|", out1, "|", $6;   
  }
  else if($5 ~ /[+][-]/)
  {
    split($5, a, "\+\-");
    print $1, "| 3 |", $2, "|", $3, "| 0 |", $4, "|", a[2], "|", $6;
  }
  else if($5 ~ /CV/)
  {
    split($5, a, " ");
    print $1, "| 4 |", $2, "|", $3, "| 0 |", $4, "|", a[1]*$4, "|", $6;
  }
  else if($5 ~ /to/)
  {
    split($5, a, "to");
    print $1, "| 5 |", $2, "|", $3, "| 1 |", a[1], "|", a[2], "|", $6;
  }
  else if($4 && $5 ~ /\|/)
  {
    print $1, "| 6 |", $2, "|", $3, "| 0 |", $4, "|", $4/2, "|", $6;
  }
  else if(!$4 && $5)
  {
    print $1, "| 7 |", $2, "|", $3, "| 0 |", $5, "|", $5/2, "|", $6;   
  }
  else if($4 && !$5)
  {
    print $1, "| 8 |", $2, "|", $3, "| 0 |", $4, "|", $4/2, "|", $6;
  }
  else 
  {
#    print $1, "| 9 |", $2, "|", $3, "| 0 |", $4, "|", $5, "|", $6;
  }
}' bionum1.txt > bigcv1.txt

## Finally, we produce a PHP script to upload records of these distributions in SQL format to a chosen database location

echo "-- Generating PHP & SQL script..."

# In the subsequent line beginning 'print "<?php', the first three text elements enclosed by pound signs (Å£) respectively give the location, login, and password to access the SQL database where Bionumbers will be stored. The next Å£-enclosed element gives the name of the database. These should be modified according to the desired location.
# What this line does is print the preamble output for the generative PHP script, with pound signs (Å£) replacing quotation marks (") to facilitate straightforward output. sed will be used to later replace pound signs with quotation marks.
awk '
BEGIN {
  FS = "|"; 
  print "<?php\n$con = mysql_connect(Å£locationÅ£,Å£loginÅ£,Å£passwordÅ£);\nif (!$con)\n{\ndie(#Could not connect: # . mysql_error());\n}\nmysql_select_db(Å£databaseÅ£, $con);\n";
  print "mysql_query(Å£CREATE TABLE bionumnew (Reference varchar(1000), Type int(11), Description varchar(1000), Organism varchar(1000), Distn int(11), Param1 double, Param2 double, Units varchar(1000), Raw varchar(1000));Å£);\necho(Å£Cleaning table...<br>Å£);mysql_query(Å£TRUNCATE TABLE bionumnew;Å£);\n";
};

{ 
  print "$result = mysql_query(Å£INSERT INTO bionumnew (Reference, Type, Description, Organism, Distn, Param1, Param2, Units, Raw) VALUES (#", $1, "#, ", $2, ", #", $3, "#, #", $4, "#, ", $5, ", ", $6, ", ", $7, ", #", $8, "#, #", $9, "#);Å£);";
  print "if(!$result) echo Å£Problem with ", $1, "Å£;";
}

END {
  print "?>\n";
}' bigcv1.txt > sql.txt 

# re-replace and clean problematics formatting
sed -i "s/Å£/\"/g" sql.txt
sed -i "s/  #/\'/g" sql.txt
sed -i "s/#  /\'/g" sql.txt
sed -i "s/ #/\'/g" sql.txt
sed -i "s/# /\'/g" sql.txt
sed -i "s/#/\'/g" sql.txt
sed -i "s/\%/percent/g" sql.txt
cp sql.txt builddb.php

## Now builddb.php contains a script to upload the retrieved information. Several temporary txt files remain for debugging and interpretation.