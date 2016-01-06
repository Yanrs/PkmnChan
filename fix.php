<?php
require 'config.php';
require 'functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}

switch ($_GET['d']) {
	case 'g':
		$minLevel = 5;
		$maxLevel = 10;
		$numPokes = 1;
	break;
	
	case 'e':
		$minLevel = 5;
		$maxLevel = 10;
		$numPokes = 3;
	break;

	case 'h':
		$minLevel = 15;
		$maxLevel = 20;
		$numPokes = 6;
	break;

        case 'n':
		$minLevel = 25;
		$maxLevel = 35;
		$numPokes = 6;
	break;
	
	case 'i':
		$minLevel = 40;
		$maxLevel = 50;
		$numPokes = 6;
	break;
       
        case 'm':
		$minLevel = 60;
		$maxLevel = 70;
		$numPokes = 6;
	break;

       case 'a':
		$minLevel = 80;
		$maxLevel = 90;
		$numPokes = 6;
	break;
	
	case 'v':
		$minLevel = 95;
		$maxLevel = 100;
		$numPokes = 6;
	break;

	
	default:
	case 'p':
		$_GET['d'] = 'p';
		$minLevel = 30;
		$maxLevel = 50;
		$numPokes = 3;
	break;
}

$query = mysql_query("SELECT `id` FROM `pokemon` ORDER BY `id` ASC LIMIT 1");
$lastId = mysql_fetch_assoc($query);
$lastId = $lastId['id'];

$cells = array();

for ($i=0;$i<$numPokes;$i++ ){
	$randId      = mt_rand(1, $lastId);
	$randomLevel = mt_rand($minLevel, $maxLevel);
	$type        = mt_rand(1, 5) == 3 ? 'Shiny ' : '' ;
	
	$query   = mysql_query("SELECT * FROM `pokemon` WHERE `id`>={$randId} AND `name`!='' LIMIT 1");
	$pokeRow = mysql_fetch_assoc($query);
	
	$pokeRow['name']  = $type.$pokeRow['name'];
	$pokeRow['level'] = $randomLevel;
	$pokeRow['maxhp'] = maxHp($pokeRow['name'], $randomLevel);
	$pokeRow['hp']    = maxHp($pokeRow['name'], $randomLevel);
	
	$_SESSION['battle']['opponent'][$i] = $pokeRow;
	
	$cells[] = '
		<img src="images/pokemon/'.$pokeRow['name'].'.png" /><br />
		'.$pokeRow['name'].'<br />
		Level: '.$pokeRow['level'].'<br />
		HP: '.$pokeRow['hp'].'/'.$pokeRow['maxhp'].'
	';
}
$_SESSION['battle']['rebattlelink'] = '<a href="fix.php?d='.$_GET['d'].'&rebattle">Rebattle This Training Account</a>';
$_SESSION['battle']['onum'] = 0;

if (isset($_GET['rebattle'])) {
	redirect('battle.php');
}


include '_header.php';
printHeader('Training Center');


echo '
	<div style="text-align: center;">
		<a href="?d=g">Level 5-10</a> &bull; <a href="?d=e">Level 5-10 x3</a> &bull;
		<a href="?d=h">Level 15-20 x6</a> &bull; <a href="?d=n">Level 25-35 x6</a><br />
		<a href="?d=i">Level 40-50 x6</a> &bull; <a href="?d=m">Level 60-70 x6</a> &bull;
		<a href="?d=a">Level 80-90 x6</a> &bull;  <a href="?d=v">Level 95-100 x6</a> 
	</div>

	
	<table class="pretty-table" style="margin-top: 10px;">
		'.cellsToRows($cells, 6).'
		<tr>
			<td colspan="6">
				<form action="battle.php" method="post">
					<input type="submit" value="Battle!">
				</form>
			</td>
		</tr>
	</table>
';

include '_footer.php';

?>