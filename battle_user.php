<?php
require_once 'config.php';
require_once 'functions.php';
require 'banned.php'; 

if (!isLoggedIn()) {
redirect('login.php');
}

$uid = (int) $_GET['id'];

if ($uid == $_SESSION['userid']) {
	include '_header.php';
	printHeader('ERROR');
	echo '<div class="error">You can not battle yourself!</div>';
	include '_footer.php';
	die();
}
/*
$bannedQuery = mysql_query("SELECT `banned` FROM `users` WHERE `banned` = '1'");
$banned = mysql_fetch_row($bannedQuery);

if ($banned->banned == 1) {
	include '_header.php';
	printHeader('ERROR');
	echo '<div class="error">You can not battle with banned user!</div>';
	include '_footer.php';
	die();
}*/

$userTeam = getUserTeamIds($uid);

if ($userTeam == false) {
	die();
}

$query = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$uid}'");
$row = mysql_fetch_assoc($query);
$username = $row['username'];

$x = 0;
for ($i=1; $i<=6; $i++) {
	$pid = $userTeam['poke'.$i];

	if ($pid==0) { continue; }
	
	$pokeRow = getUserPokemon($pid);

	$_SESSION['battle']['opponent'][$x]          = $pokeRow;
	$_SESSION['battle']['opponent'][$x]['maxhp'] = maxHp($pokeRow['name'], $pokeRow['level']);
	$_SESSION['battle']['opponent'][$x]['hp']    = maxHp($pokeRow['name'], $pokeRow['level']);
	$x++;
}
$_SESSION['battle']['captcha'] = time();
$_SESSION['battle']['onum'] = 0;
$_SESSION['battle']['rebattlelink'] = '<a href="battle_user.php?id='.$uid.'">Rebattle '.cleanHtml($username).'</a>';
$_SESSION['battle']['uid'] = $uid;

redirect('battle.php');
?>