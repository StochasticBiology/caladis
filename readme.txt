Caladis -- A Probabilistic Calculator
-------------------------------------
www.caladis.org

Johnston, I. G., Rickett, B. C., and Jones, N. S., Explicit tracking of uncertainty increases the power of quantitative rule-of-thumb reasoning in cell biology, [PLACEHOLDER -- CITATION DETAILS]
Contact: contact@caladis.org

Copyright (C) 2014 Systems & Signals Research Group, Imperial College London

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, version 3 of the License.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Please cite the above paper if using this code or this tool in a publication.

This package contains:
Website/
  -- Source code for the web tool Caladis, a probabilistic calculator, http://www.caladis.org ;
Bionumbers/
  -- Code used to build a database of "Bionumber distributions" from data downloaded from the Bionumbers website http://bionumbers.hms.harvard.edu/ .
Detailed descriptions of the function of each file are provided in file_index.txt .

The Website/ subdirectory contains the web tool source code. This is split into several further subdirectories. The source code mainly takes the form of PHP and Javascript scripts with some HTML wrapping. The Bionumbers/ directory contains a bash script to parse data downloaded from the Bionumbers website into a PHP script to populate an SQL database, and the resultant PHP script that we used to build our database.
Two SQL tables are required in conjunction with this code for full functionality. One, "bionumnew", contains Bionumber distribution information and is automatically constructed by the code herein. The other, "log", facilitates logging and downloading of previous queries and requires construction, as described below, before the interface is activated.

To implement this code, 
1. Read and be aware of the subtleties listed below. 
2. To create your own Bionumbers database, go to 3. To use our setup, go to 5.
3. Obtain a downloaded dump of the data from the Bionumbers website. If it does not match the details below, results from our code may vary; perform modifications in Bionumbers/parsescr.sh if they are necessary.
4. Secure access to an SQL platform. Run Bionumbers/parsescr.sh to create a new version of Bionumbers/builddb.php .
5. Modify Bionumbers/builddb.php to include the details of your chosen SQL database. Run Bionumbers/builddb.php . If you wish to log entries from the web interface, construct a log database as described below.
6. Modify the database-dependent files within Website/ (listed below) to reference your chosen SQL database.
7. Upload the files within Website/ to a web server.

Some subtleties exist in implementing this code on a new web server:
1. Root directory location. The code "as is" assumes that the contents of the Website/ subdirectory (index.php and several subdirectories) will be placed at the highest level of the website, i.e. at "DOCUMENT_ROOT". In other words, that index.php will be referenced by, for example www.mywebsite.org/index.php and not www.mywebsite.org/mysubdirectory/index.php . If the latter situation is the case, the relative paths referenced throughout the code should be updated to reflect the placement relative to the top level. Using grep, these paths can be identified using the command
grep -r "DOCUMENT_ROOT" .
from the Website/ directory.
2. SQL database location. SQL databases are used to store Bionumber distributions and to record queries in Caladis. Server-side functions linking to these databases require information about the location of, and credentials for, these databases. This information has been omitted from this released source, and the code has been clearly labelled where replacement information should be provided. The specific instances are
Website/lib/getdata.php
Website/lib/listget.php
Website/lib/listsearch.php
Website/lib/log.class.php
Bionumbers/parsescr.sh
To enable logging and data downloading, a table called "log" should be created in the SQL database, with the following fields (and thus the following generating command)
CREATE TABLE log (id int(11), date date, qStr varchar(255), vStr varchar(255), xStr varchar(10), nStr varchar(10), hStr varchar(10), aStr varchar(10), dlData int(11), dlBins int(11), dlStatus int(11));
3. Bionumbers database construction. We use a script to parse the downloaded contents of the Bionumbers database into a PHP script which, when run, populates an SQL database with Bionumbers distribution information. The version of the Bionumbers database download we use is available as a file BioNumber.csv with MD5 checksum
9c4fce27479947e3dc94d41dd85579b8
We do not include this file in this package as it is the property of the Bionumbrs database; however, we do include the final PHP script resulting from our processing of the data. Different versions of the downloadable data may have different formatting and different entries and so may throw errors with our parsing code; modifications may be required in such cases.
4. Calculation times. The calculation time in Caladis is currently set to 60s ( $timecutoff in compute() in Website/lib/build.class.php ). This relies on the ability to reset time limits for server-side processes ( generate(...) in Website/lib/data.class.php ). If your server does not permit this resetting, or you wish to alter the allowed calculation times, the time cutoff should be modified to avoid unwanted behaviour.
5. Source code download. To avoid absurd recursion, we do not include the zipped version of this source code package as part of the uploadable website. Instead, the front end link to the downloadable source code, contained in Website/elements/footer.php , provides an absolute reference to www.caladis.org/caladis_source.tar.gz .
6. Permissions. To enable logging and retrieval of calculation data, the download/ subdirectory should allow public write access.
7. Licensing. This code is released under the GNU GPL 3.0 license http://www.gnu.org/licenses/ , basically meaning it can be used for anything, as long as this licensing remains intact. Some code from third parties is utilised, and exists in Website/media/js/thirdparty/ (this code is GPL licensed) and Website/media/js/iphone/ (this code is MIT licensed), with the copyright information present in the corresponding source code files. We would appreciate a citation to our accompanying academic paper (reference at the top of this file) if this code is used in a published project.
