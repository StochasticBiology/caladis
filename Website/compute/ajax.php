<!-- Caladis calculation page -- Ajax -->
<!-- Copyright (C) 2014 Systems & Signals Research Group, Imperial College London
Released as free software under GNU GPL 3.0 http://www.gnu.org/licenses/ ; see readme.txt
Please cite the accompanying paper [see readme.txt] if using this code or this tool in a publication. -->

<?php
	// call the PHP processes involved in performing the calculation
	require_once($_SERVER['DOCUMENT_ROOT'] . "/lib/build.class.php");
	$buildClass = new build;
	$buildClass->compute();
?>