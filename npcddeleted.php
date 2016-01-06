<?php
die();
require 'config.php';
require 'functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}

	$usersQuery = mysql_query("SELECT `poke1` FROM `users` WHERE id='{$uid}'");
	$usersRow = mysql_fetch_object($usersQuery);
							
	$starterID = $usersRow->poke1;
								
	$pokeQuery	= mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$starterID}'");
	$pokeRow = mysql_fetch_object($pokeQuery);
	
	$pokelevel = $pokeRow->level;

switch ($_GET['battle']) {
	case 'x3':
		$level = $pokelevel * 3;
		$numPokes = 1;
		$pokeid = 150;
		$type = '';
	break;
	
	case 'x5':
		$level = $pokelevel * 5;
		$numPokes = 1;
		$pokeid = 248;
		$type = '';
	break;

	
	default:
	case 'x2':
		$_GET['battle'] = 'x2';
		$level = $pokelevel * 2;
		$numPokes = 1;
		$pokeid = 127;
		$type = 'Snow ';
	break;
}


$cells = array();

for ($i=0;$i<$numPokes;$i++ ){

	$query   = mysql_query("SELECT * FROM `pokemon` WHERE `id`>={$pokeid} AND `name`!='' LIMIT 1");
	$pokeRow = mysql_fetch_assoc($query);
	
	$pokeRow['name']  = $type.$pokeRow['name'];
	$pokeRow['level'] = $level;
	$pokeRow['maxhp'] = maxHp($pokeRow['name'], $level);
	$pokeRow['hp']    = maxHp($pokeRow['name'], $level);
	
	$_SESSION['battle']['opponent'][$i] = $pokeRow;
	
	$cells[] = '
		<img src="images/pokemon/'.$pokeRow['name'].'.png" /><br />
		'.$pokeRow['name'].'<br />
		Level: '.$pokeRow['level'].'<br />
		HP: '.$pokeRow['hp'].'/'.$pokeRow['maxhp'].'
	';
}
$_SESSION['battle']['rebattlelink'] = '<a href="npc.php?battle='.$_GET['x2'].'&rebattle">Rebattle with this NPC</a>';
$_SESSION['battle']['onum'] = 0;

if (isset($_GET['rebattle'])) {
	redirect('battle.php');
}


include '_header.php';
printHeader('NPC - Training Center');


echo '
	<div style="text-align: center;">
		<a href="?battle=x2">2x  your levels</a> &bull; <a href="?battle=x3">3x  your levels</a> &bull; <a href="?battle=x5">5x your levels</a>
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