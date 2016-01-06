<?php
@session_save_path("tmp");
@session_start();

error_reporting(0);

//We log to the DataBase
$connection = @mysql_connect('127.0.0.1', 'root', '');

if (!$connection) {
	include '_header.php';
	echo '<div class="error">Error connecting to the database!</div>';
	//if (isset($_SESSION['admin']) && $_SESSION['admin']==1) { echo mysql_error(); }
	include '_footer.php';
	die();
}

$dbSelected = mysql_select_db('pkmnhelios');

if (!$dbSelected) {
	include '_header.php';
	echo '<div class="error">Error connecting to the database!</div>';
	//if (isset($_SESSION['admin']) && $_SESSION['admin']==1) { echo mysql_error(); }
	include '_footer.php';
	die();
}

// asdd stuffz
$filename = end(explode('/', $_SERVER["SCRIPT_NAME"]));

if ($filename != 'battle.php' && $filename != 'battle2.php' && $filename != 'map_users.php') {
	unset($_SESSION['battle']);
}

if (isset($_SESSION['lastseen']) && (time() - $_SESSION['lastseen'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time 
    session_destroy();   // destroy session data in storage
} else {
	$_SESSION['lastseen'] = time();
}

if (isset($_SESSION['userid'])) {
	$uid = (int) $_SESSION['userid'];
	
	// Check if they are banned
	$banquery = mysql_query("SELECT `banned` FROM `users` WHERE `id`='{$uid}'");
	$banrow = mysql_fetch_assoc($banquery);
	if ($banrow['banned'] == 1 && $filename != 'logout.php') {
		header('Location: logout.php');
		die();
	}
	
	$time = $_SESSION['lastseen'];
	//$id = (int) $_SESSION['userid'];	
	mysql_query("UPDATE `users` SET `lastseen`='{$time}' WHERE `id`='{$uid}' LIMIT 1");
	
	//$selectTime = mysql_fecth_array(mysql_query("SELECT `lastseen` FROM `users` WHERE `id` = '{$uid}'"));
	//$_SESSION['lastseen'] = 
}

// Basic check for SQL injection
if (
	stripos($_SERVER['QUERY_STRING'], 'UNION') !== false ||
	stripos($_SERVER['QUERY_STRING'], 'SELECT') !== false ||
	stripos($_SERVER['QUERY_STRING'], 'SCRIPT') !== false
) {
	if (
		isset($_SERVER['HTTP_X_FORWARDED_FOR']) &&
		$_SERVER['HTTP_X_FORWARDED_FOR'] != ''
	) {
	    $ip = cleanSql($_SERVER['HTTP_X_FORWARDED_FOR']);
	} else {
	    $ip = cleanSql($_SERVER['REMOTE_ADDR']);
	}
	$fh = @fopen('sqli_attempts.txt', 'a') or die();
	fwrite($fh, $ip . ' ' . $_SERVER['SCRIPT_NAME'] . '?' . urldecode($_SERVER['QUERY_STRING']) . PHP_EOL);
	fclose($fh);
}

if (!function_exists('stripslashes_deep')) {
	function stripslashes_deep($value) {
	    $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
	    return $value;
	}
}

// Stop Magic Quotes
if (get_magic_quotes_gpc()) {
	$_POST = stripslashes_deep($_POST);
	$_GET  = stripslashes_deep($_GET);
}


// If some important updates
if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
	/*	include 'updates.html';
	die();*/
}

/*
if ($_SERVER['REMOTE_ADDR'] != '212.93.114.91') {
		include 'updates.html';
	die();
}*/

$allowed_lang = array('en', 'es', 'ph', 'lv');

if(isset($_GET['lang']) === true && in_array($_GET['lang'], $allowed_lang) === true) {
	$_SESSION['lang'] = $_GET['lang'];
} else if(isset($_SESSION['lang']) === false){
	$_SESSION['lang'] = 'en';
}

include 'lang/' . $_SESSION['lang'] . '.php';

define('GOT_CONFIG', true);

?>