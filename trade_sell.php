<?php
include 'config.php';
include 'functions.php';

if (!isLoggedIn()) { redirect('index.php'); }
if (!isset($_GET['uid'])) { redirect('membersarea.php'); }

include '_header.php';
$word = isset($_GET['sale']) ? 'sale' : 'trade' ;
printHeader('Pokemon For '.ucwords($word));

$uid = (int) $_GET['uid'];

$query = mysql_query("SELECT * FROM `users` WHERE `id`='{$uid}'");
if (mysql_num_rows($query) == 0) {
	echo '<div class="error">User does not exist.</div>';
	include '_footer.php';
	die();
}
$userRow = mysql_fetch_assoc($query);

$tablename = isset($_GET['sale']) ? 'sale_pokemon' : 'trade_pokemon' ;
$query = mysql_query("SELECT * FROM `{$tablename}` WHERE `uid`='{$uid}'");
$numPokes = mysql_num_rows($query);

if ($numPokes == 0) {
	
	echo '<div class="error">User does not have any pokemon for '.$word.'.</div>';
	include '_footer.php';
	die();
}

$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 100 ;
$limit = $limit <= 0 ? 100 : $limit ;
$limit = $limit > $numPokes ? $numPokes : $limit ;

$query = mysql_query("SELECT * FROM `{$tablename}` WHERE `uid`='{$uid}' ORDER BY `id` DESC LIMIT {$limit}");

echo '
	<div style="overflow: auto;">
		Showing '.$limit.'/'.$numPokes.' pokemon up for '.$word.' by <a href="profile.php?id='.$userRow['id'].'">'.cleanHtml($userRow['username']).'</a>.<br /><br />
';
if ($limit < $numPokes) {
	$text = isset($_GET['sale']) ? 'Show all '.$numPokes.' pokemon up for sale.' : 'Show all '.$numPokes.' trades.' ;
	echo '
		<form action="" method="get">
			<input type="hidden" name="uid" value="'.$uid.'" /> 
			<input type="hidden" name="limit" value="'.$numPokes.'" /> 
			<input type="submit" value="'.$text.'" /> 
		</form>
	';
}


while ($poke = mysql_fetch_assoc($query)) {
	$price = isset($_GET['sale']) ? 'Price: $'.number_format($poke['price']).'<br />' : '';
	$link = isset($_GET['sale']) ? 'sell_pokemon.php?p=buy&id='.$poke['id'] : 'trade.php?a=mao&id='.$poke['id'] ;
	echo '
		<div style="height: 200px; width: 150px; float: left;">
			<img src="images/pokemon/'.$poke['name'].'.png"><br />
			<a href="'.$link.'">'.$poke['name'].'</a><br />
			Level: '.number_format($poke['level']).'<br />
			Exp: '.number_format($poke['exp']).'<br />
			'.$price.'
		</div>
	';		
}
echo '
	</div>
';
include '_footer.php';
?>