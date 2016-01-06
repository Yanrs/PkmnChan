<?php
require_once 'config.php';
require_once 'functions.php';

include '_header.php';
printHeader('Change Team');

$uid = (int) $_SESSION['userid'];
$pid = (int) $_GET['id'];
$teamIds = getUserTeamIds($uid);
$pokemon = getUserPokemon($pid);

if ($pokemon === false) {
	echo '<div class="error">Could not find the pokemon.</div>';
} elseif ($pokemon['uid'] != $uid) {
	echo '<div class="error">This pokemon does not belong to you.</div>';
} elseif (in_array($pokemon['id'], $teamIds)) {
	echo '<div class="error">This pokemon is already in your team</div>';
} else {	
	if (in_array($_POST['pos'], range(1, 6))) {
		$pos = (int) $_POST['pos'];
		
		for ($i=$pos; $i>0; $i--) {
			if ($teamIds['poke'.$i] == 0) {
				$pos = $i;
			}
		}
		
		mysql_query("UPDATE `users` SET `poke{$pos}`='{$pid}' WHERE `id`='{$uid}'");
		
		echo '<img src="images/pokemon/'.$pokemon['name'].'.png" /><br /><br />
			<div class="notice">
				'.$pokemon['name'].' has been placed in your team!
			</div>
		';
	} else {
		echo '
			<div style="text-align: center;">
				<img src="images/pokemon/'.$pokemon['name'].'.png" /><br />
				'.$pokemon['name'].'<br />
				Level: '.$pokemon['level'].'<br />
				Exp: '.$pokemon['exp'].'
			</div>
		';
		
		$cells = array();
		$pos = 1;
		foreach ($teamIds as $pokeid) {
			$poke = getUserPokemon($pokeid);
			if ($poke === false) {
    		    $cells[] = '
        			<img src="images/pokemon/EMPTY.png" /><br />
    				Empty Slot<br />
    				
    				<form method="post">
    					<input type="hidden" name="pos" value="'.$pos.'" />
    					<input type="submit" value="Put '.$pokemon['name'].' in team." />
    				</form>
    			';
			} else {
    			$cells[] = '
    				<img src="images/pokemon/'.$poke['name'].'.png" /><br />
    				'.$poke['name'].'<br />
    				Level: '.$poke['level'].'<br />
    				Exp: '.$poke['exp'].'<br /><br />
    				
    				<form method="post">
    					<input type="hidden" name="pos" value="'.$pos.'" />
    					<input type="submit" value="Swap '.$poke['name'].' for '.$pokemon['name'].'" />
    				</form>
    			';
			}
			
			$pos++;
		}
		
		echo '
			<table class="pretty-table">
				'.cellsToRows($cells, 3).'
			</table>
		';
	}
}

include '_footer.php';
?>