<?php
/*
Plugin Name: WP-ServerInfo
Plugin URI: http://www.lesterchan.net/portfolio/programming.php
Description: Display your host's server PHP and MYSQL information (integrated into WordPress Admin Style) on your WordPress dashboard.
Version: 1.00
Author: Lester 'GaMerZ' Chan
Author URI: http://www.lesterchan.net
*/


/*  
	Copyright 2007  Lester Chan  (email : gamerz84@hotmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


### Create Text Domain For Translation
load_plugin_textdomain('wp-serverinfo', 'wp-content/plugins/serverinfo');


### Function: WP-ServerInfo Menu
add_action('admin_menu', 'serverinfo_menu');
function serverinfo_menu() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('index.php',  __('WP-ServerInfo', 'wp-serverinfo'),  __('WP-ServerInfo', 'wp-serverinfo'), 1, 'serverinfo/serverinfo.php', 'display_serverinfo');
	}
}


### Function: ServerInfo Header
add_action('admin_head', 'serverinfo_head');
function serverinfo_head() {
	wp_register_script('wp-serverinfo', '/wp-content/plugins/serverinfo/serverinfo-js.php', false, '1.00');
	wp_print_scripts('wp-serverinfo');
	echo '<link rel="stylesheet" href="'.get_option('siteurl').'/wp-content/plugins/serverinfo/serverinfo-css.css" type="text/css" media="screen" />'."\n";
}


### Display WP-ServerInfo Admin Page
function display_serverinfo() {
	serverinfo_subnavi();
	get_generalinfo();
	get_phpinfo();
	get_mysqlinfo();
}


### Get General Information
function get_generalinfo() {
	global $wpdb;
	// Get MYSQL Version
	$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
	// If MYSQL Version More Than 3.23, Get More Info
	if($sqlversion >= '3.23') {
		$tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");
		foreach($tablesstatus as  $tablestatus) {
			$data_usage += $tablestatus->Data_length;
			$index_usage +=  $tablestatus->Index_length;
		}
		if (!$data_usage){ $data_usage = __('N/A', 'wp-serverinfo'); }
		if (!$index_usage){ $index_usage = __('N/A', 'wp-serverinfo'); }
	} else {
		$data_usage = __('N/A', 'wp-serverinfo');
		$index_usage = __('N/A', 'wp-serverinfo');
	}
	// Get MYSQL Max Allowed Packet
	$packet_max_query = $wpdb->get_row("SHOW VARIABLES LIKE 'max_allowed_packet'");
	$packet_max = $packet_max_query->Value;
	if(!$packet_max) {
		$packet_max = __('N/A', 'wp-serverinfo');
	}
	// Get MYSQL Max Allowed Conntions
	$connection_max_query = $wpdb->get_row("SHOW VARIABLES LIKE 'max_connections'");
	$connection_max = $connection_max_query->Value;
	if(!$connection_max) {
		$connection_max = __('N/A', 'wp-serverinfo');
	}
	// Get PHP Short Tag
	if(ini_get('short_open_tag')) {
		$short_tag = __('On', 'wp-serverinfo');
	} else {
		$short_tag = __('Off', 'wp-serverinfo');	
	}
	// Get PHP Safe Mode
	if(ini_get('safe_mode')) {
		$safe_mode = __('On', 'wp-serverinfo');
	} else {
		$safe_mode = __('Off', 'wp-serverinfo');
	}
	// Get PHP Magic Quotes GPC
	if(get_magic_quotes_gpc()) {
		$magic_quotes_gpc = __('On', 'wp-serverinfo');
	} else {
		$magic_quotes_gpc = __('Off', 'wp-serverinfo');
	}
	// Get PHP Max Upload Size
	if(ini_get('upload_max_filesize')) {
		$upload_max = ini_get('upload_max_filesize');	
	} else {
		$upload_max = __('N/A', 'wp-serverinfo');
	}
	// Get PHP Max Post Size
	if(ini_get('post_max_size')) {
		$post_max = ini_get('post_max_size');
	} else {
		$post_max = __('N/A', 'wp-serverinfo');
	}
	if(ini_get('max_execution_time')) {
		$max_execute = ini_get('max_execution_time');
	} else {
		$max_execute = __('N/A', 'wp-serverinfo');
	}
	// Get PHP Memory Limit 
	if(ini_get('memory_limit')) {
		$memory_limit = ini_get('memory_limit');
	} else {
		$memory_limit = __('N/A', 'wp-serverinfo');
	}
	// Get GD Version
	if (function_exists('gd_info')) { 
		$gd = gd_info();
		$gd = $gd["GD Version"];
	} else {
		ob_start();
		phpinfo(8);
		$phpinfo = ob_get_contents();
		ob_end_clean();
		$phpinfo = strip_tags($phpinfo);
		$phpinfo = stristr($phpinfo,"gd version");
		$phpinfo = stristr($phpinfo,"version");
		$gd = substr($phpinfo,0,strpos($phpinfo,"\n"));
	}
	if(empty($gd)) {
		$gd = __('N/A', 'wp-serverinfo');
	}
?>
	<div class="wrap" id="GeneralOverview">
		<h2><?php _e('General Overview','wp-serverinfo'); ?></h2>
		<table width="100%"  border="0" cellspacing="3" cellpadding="3">
			<tr class="h">
				<th><?php _e('Variable Name', 'wp-serverinfo'); ?></th>
				<th><?php _e('Value', 'wp-serverinfo'); ?></th>
				<th><?php _e('Variable Name', 'wp-serverinfo'); ?></th>
				<th><?php _e('Value', 'wp-serverinfo'); ?></th>
			</tr>
			<tr>
				<td><?php _e('OS', 'wp-serverinfo'); ?></td>
				<td><?php echo PHP_OS; ?></td>
				<td><?php _e('Database Data Disk Usage', 'wp-serverinfo'); ?></td>
				<td><?php echo format_size($data_usage); ?></td>
			</tr>
			<tr class="alternate">
				<td><?php _e('Server', 'wp-serverinfo'); ?></td>
				<td><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
				<td><?php _e('Database Index Disk Usage', 'wp-serverinfo'); ?></td>
				<td><?php echo format_size($index_usage); ?></td>
			</tr>
			<tr>
				<td>PHP</td>
				<td>v<?php echo PHP_VERSION; ?></td>
				<td><?php _e('MYSQL Maximum Packet Size', 'wp-serverinfo'); ?></td>
				<td><?php echo format_size($packet_max); ?></td>
			</tr>
			<tr class="alternate">
				<td>MYSQL</td>
				<td>v<?php echo $sqlversion; ?></td>
				<td><?php _e('MYSQL Maximum No. Connection', 'wp-serverinfo'); ?></td>
				<td><?php echo number_format($connection_max); ?></td>
			</tr>
			<tr>
				<td>GD</td>
				<td><?php echo $gd; ?></td>
				<td><?php _e('PHP Short Tag', 'wp-serverinfo'); ?></td>
				<td><?php echo $short_tag; ?></td>
			</tr>
			<tr class="alternate">
				<td><?php _e('Server Hostname', 'wp-serverinfo'); ?></td>
				<td><?php echo $_SERVER['SERVER_NAME']; ?></td>
				<td><?php _e('PHP Safe Mode', 'wp-serverinfo'); ?></td>
				<td><?php echo $safe_mode; ?></td>
			</tr>
			<tr>
				<td><?php _e('Server IP:Port','wp-serverinfo'); ?></td>
				<td><?php echo $_SERVER['SERVER_ADDR']; ?>:<?php echo $_SERVER['SERVER_PORT']; ?></td>
				<td><?php _e('PHP Magic Quotes GPC', 'wp-serverinfo'); ?></td>
				<td><?php echo $magic_quotes_gpc; ?></td>
			</tr>
			<tr class="alternate">
				<td><?php _e('Server Document Root','wp-serverinfo'); ?></td>
				<td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td>
				<td><?php _e('PHP Memory Limit', 'wp-serverinfo'); ?></td>
				<td><?php echo $memory_limit; ?></td>
			</tr>
			<tr>
				<td><?php _e('Server Admin', 'wp-serverinfo'); ?></td>
				<td><?php echo $_SERVER['SERVER_ADMIN']; ?></td>
				<td><?php _e('PHP Max Upload Size', 'wp-serverinfo'); ?></td>
				<td><?php echo $upload_max; ?></td>
			</tr>
			<tr class="alternate">
				<td><?php _e('Server Load', 'wp-serverinfo'); ?></td>
				<td><?php echo get_ServerLoad(); ?></td>
				<td><?php _e('PHP Max Post Size', 'wp-serverinfo'); ?></td>
				<td><?php echo $post_max; ?></td>
			</tr>
			<tr>
				<td><?php _e('Server Date/Time', 'wp-serverinfo'); ?></td>
				<td><?php echo date('l, jS F Y, H:i'); ?></td>
				<td><?php _e('PHP Max Script Execute Time', 'wp-serverinfo'); ?></td>
				<td><?php echo $max_execute; ?>s</td>
			</tr>
		</table>
	</div>
<?php
}


### Get PHP Information
function get_phpinfo() {
	ob_start();
	phpinfo();
	$phpinfo = ob_get_contents();
	ob_end_clean();
	// Strip Tags
	$phpinfo = strip_tags($phpinfo, '<table><tr><th><td>');
	// Strip Unwanted Contents
	$phpinfo = eregi('<table border="0" cellpadding="3" width="600">(.*)</table>', $phpinfo, $data);
	$phpinfo = $data[0];	
	// PHP Version Header
	$phpinfo = preg_replace("!<table border=\"0\" cellpadding=\"3\" width=\"600\">\n<tr class=\"h\"><td>\n(.*?)\n</td></tr>\n</table>!", "<h2>$1</h2>", $phpinfo);
	// Normal Header
	$phpinfo = preg_replace("!<\/table>\n(.*?)\n<table border=\"0\" cellpadding=\"3\" width=\"600\">!", "</table>\n<br />\n<h2>$1</h2>\n<table border=\"0\" cellpadding=\"3\" width=\"100%\">", $phpinfo);
	// Fixed For Credits
	$phpinfo = preg_replace("!</table>\n<table border=\"0\" cellpadding=\"3\" width=\"600\">\n<tr class=\"v\"><td>\n\n(.*?)</td></tr>\n</table>!", "<tr class=\"Out\" onmouseover=\"this.className='Over'\" onmouseout=\"this.className='Out'\"><td colspan=\"2\">$1</td></tr>\n</table>", $phpinfo);
	// Change Width To 100%
	$phpinfo = str_replace('width="600"', 'width="100%"', $phpinfo);
	// Get Rid Of TD Class
	$phpinfo = str_replace('<td class="e">', '<td>', $phpinfo);
	$phpinfo = str_replace('<td class="v">', '<td>', $phpinfo);
	// Remove PHP Credits, Will Add It In Later
	$phpinfo = str_replace('PHP Credits', '', $phpinfo);
	// Can't Find A Better Way Of Doing This
	$phpinfo = str_replace("Configuration\nPHP Core", '<br /><h2>PHP Core Configuration</h2>', $phpinfo);
	// Make Mouse Over Effect
	$phpinfo = str_replace('<tr>', '<tr class="Out" onmouseover="this.className=\'Over\'" onmouseout="this.className=\'Out\'">', $phpinfo);
	echo '<div class="wrap" id="PHPinfo">'."\n";
	echo $phpinfo;
	echo '</div>'."\n";
}


### Get MYSQL Information
function get_mysqlinfo() {
	global $wpdb;
	$sqlversion = $wpdb->get_var("SELECT VERSION() AS version");
	$mysqlinfo = $wpdb->get_results("SHOW VARIABLES");
	echo '<div class="wrap" id="MYSQLinfo">'."\n";
	echo "<h2>MYSQL $sqlversion</h2>\n";
	if($mysqlinfo) {
		echo '<table border="0" cellpadding="3" width="100%">'."\n";
		echo '<tr class="h"><th>'.__('Variable Name', 'wp-serverinfo').'</th><th>'.__('Value', 'wp-serverinfo').'</th></tr>'."\n";
		foreach($mysqlinfo as $info) {
			echo '<tr class="Out" onmouseover="this.className=\'Over\'" onmouseout="this.className=\'Out\'"><td>'.$info->Variable_name.'</td><td>'.htmlspecialchars($info->Value).'</td></tr>'."\n";
		}
		echo '</table>'."\n";
	}
	echo '</div>'."\n";
}


### WP-Server Sub Navigation
function serverinfo_subnavi() {
?>
	<div class="wrap" style="text-align: center">
		<a href="#DisplayGeneral" onclick="toggle_general(); return false;"><?php _e('Display General Overview', 'wp-serverinfo'); ?></a> - <a href="#DisplayPHP" onclick="toggle_php(); return false;"><?php _e('Display PHP Information', 'wp-serverinfo'); ?></a> - <a href="#DisplayMYSQL" onclick="toggle_mysql(); return false;"><?php _e('Display MYSQL Information', 'wp-serverinfo'); ?></a>
	</div>
<?php
}


### Function: Format Bytes Into GB/MB/KB/Bytes
if(!function_exists('format_size')) {
	function format_size($rawSize) {
		if($rawSize / 1099511627776 > 1) {
			return round($rawSize/1099511627776, 1) . ' TB';
		} elseif($rawSize / 1073741824 > 1) {
			return round($rawSize/1073741824, 1) . ' GB';
		} elseif($rawSize / 1048576 > 1) {
			return round($rawSize/1048576, 1) . ' MB';
		} elseif($rawSize / 1024 > 1) {
			return round($rawSize/1024, 1) . ' KB';
		} else {
			return round($rawSize, 1) . ' bytes';
		}
	}
}


### Function: Get The Server Load
if(!function_exists('get_serverload')) {
	function get_serverload() {
		if(PHP_OS != 'WINNT' && PHP_OS != 'WIN32') {
			if(file_exists('/proc/loadavg') ) {
				if ($fh = @fopen( '/proc/loadavg', 'r' )) {
					$data = @fread( $fh, 6 );
					@fclose( $fh );
					$load_avg = explode( " ", $data );
					$server_load = trim($load_avg[0]);
				}
			} else {
				$data = @system('uptime');
				preg_match('/(.*):{1}(.*)/', $data, $matches);
				$load_arr = explode(',', $matches[2]);
				$server_load = trim($load_arr[0]);
			}
		}
		if(!$server_load) {
			$server_load = __('N/A', 'wp-serverinfo');
		}
		return $server_load;
	}
}
?>