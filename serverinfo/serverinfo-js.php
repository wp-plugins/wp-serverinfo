<?php
/*
+----------------------------------------------------------------+
|																							|
|	WordPress 2.1 Plugin: WP-ServerInfo 1.00								|
|	Copyright (c) 2007 Lester "GaMerZ" Chan									|
|																							|
|	File Written By:																	|
|	- Lester "GaMerZ" Chan															|
|	- http://www.lesterchan.net													|
|																							|
|	File Information:																	|
|	- Server Info JavaScript File													|
|	- wp-content/plugins/serverinfo/serverinfo-js.php						|
|																							|
+----------------------------------------------------------------+
*/
?>
// Display General Overview
function toggle_general() {
	document.getElementById('GeneralOverview').style.display = "block";
	document.getElementById('PHPinfo').style.display = "none";
	document.getElementById('MYSQLinfo').style.display = "none";
}

// Display PHP Information
function toggle_php() {
	document.getElementById('GeneralOverview').style.display = "none";
	document.getElementById('PHPinfo').style.display = "block";
	document.getElementById('MYSQLinfo').style.display = "none";
}

// Display MYSQL Information
function toggle_mysql() {
	document.getElementById('GeneralOverview').style.display = "none";
	document.getElementById('PHPinfo').style.display = "none";
	document.getElementById('MYSQLinfo').style.display = "block";
}