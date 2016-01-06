<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'pagination.class.php';


if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php';
printHeader('Trade');
echo '
	<div style="text-align: center; margin: 10px 0px;">
		<a href="?a=puft">Put Pokemon Up For Trade</a> &bull;
		<a href="?a=vuft">View All Pokemon Up For Trade</a> &bull;
		<a href="?a=vao">View Offers On My Pokemon</a> &bull;
		<a href="?a=va">View Offers I Have Made</a>
	</div>
';

$uid = (int) $_SESSION['userid'];

switch ($_GET['a']) {
	case 'puft':
		require_once 'trade_puft.php';
	break;
	
	case 'puft_process':
		if (isset($_GET['id'])) {
			$_POST['pokemon'] = array($_GET['id']);
		}
		
		if (!isset($_POST['pokemon']) || count($_POST['pokemon']) == 0) {
			echo '<div class="error">You did not select any pokemon to put up for trade.</div>';
			break;
		}
		
		foreach ($_POST['pokemon'] as $key => $value) {
			$_POST['pokemon'][$key] = (int) $value;	
		}
		
		$query = mysql_query("SELECT `poke1`,`poke2`,`poke3`,`poke4`,`poke5`,`poke6` FROM `users` WHERE `id`='{$uid}'");
		$myTeam = mysql_fetch_assoc($query);
		
		$nonInTeamSql = " `id` NOT IN ('".implode("', '", $myTeam)."') ";
		$puftSql = " `id` IN ('".implode("', '", $_POST['pokemon'])."') ";
		$query = mysql_query("SELECT * FROM `user_pokemon` WHERE {$nonInTeamSql} AND {$puftSql} AND `uid`='{$uid}' ORDER BY `name`");
		
		if ( mysql_num_rows($query) != count($_POST['pokemon']) ) {
			echo '<div class="error">There was an error.</div>';
			break;
		}
		
		echo '<div class="notice">The following pokemon have been put up for trade!</div>';
		
		echo '<div style="text-align: center;">';
		while ($p = mysql_fetch_assoc($query)) {
			echo '
				<img src="images/pokemon/'.$p['name'].'.png" /><br />
				'.$p['name'].'<br />
				Level: '.$p['level'].'<br />
			';
			$p['name'] = mysql_real_escape_string($p['name']);
			mysql_query("INSERT INTO `trade_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `user_pokemon` WHERE `id`='{$p['id']}'");
		}
		echo '</div>';
	break;
	
	case 'vuft':		
		require_once 'trade_vuft.php';
	break;

	case 'mao':
		$tid = (int) $_GET['id'];
		$query = mysql_query("SELECT * FROM `trade_pokemon` WHERE `id`='{$tid}' LIMIT 1");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">This pokemon is not up for trade.</div>';
			break;
		}
		
		$tpoke = mysql_fetch_assoc($query);
		echo '
			<div style="text-align: center;">
				<img src="images/pokemon/'.$tpoke['name'].'.png" /><br />
				'.$tpoke['name'].'<br />
				Level: '.number_format($tpoke['level']).'
			</div>
		';
		
		$query = mysql_query("SELECT `poke1`,`poke2`,`poke3`,`poke4`,`poke5`,`poke6` FROM `users` WHERE `id`='{$uid}'");
		$myTeam = mysql_fetch_assoc($query);
		
		$nonInTeamSql = " `id` NOT IN ('".implode("', '", $myTeam)."') ";
		$query = mysql_query("SELECT * FROM `user_pokemon` WHERE {$nonInTeamSql} AND `uid`='{$uid}' ORDER BY `name`");
		
		echo '
			<form action="?a=mao_process&id='.$tid.'" method="post">
			<table class="pretty-table">
				<tr>
					<th>&nbsp;</th>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Level</th>
					<th>Exp</th>
					<th>Moves</th>
				</tr>
		';
		while ($pokemon = mysql_fetch_assoc($query)) {
			echo '
				<tr>
					<td><input type="checkbox" name="pokemon[]" value="'.$pokemon['id'].'" /></td>
					<td><img src="images/pokemon/'.$pokemon['name'].'.png" /></td>
					<td>'.$pokemon['name'].'</td>
					<td>'.number_format($pokemon['level']).'</td>
					<td>'.number_format($pokemon['exp']).'</td>
					<td>
						'.$pokemon['move1'].'<br />
						'.$pokemon['move2'].'<br />
						'.$pokemon['move3'].'<br />
						'.$pokemon['move4'].'
					</td>
				</tr>
			';
		}
		echo '
				<tr>
					<td colspan="6"><input type="submit" value="Put these pokemon up for trade." /></td>
				</tr>
			</table>
			</form>
		';
	break;
	
	case 'mao_process':
		//print_r($_POST);
		
		$tid = (int) $_GET['id'];
		$query = mysql_query("SELECT * FROM `trade_pokemon` WHERE `id`='{$tid}' LIMIT 1");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">Could not find that trade.</div>';
			break;
		}
		
		if (!isset($_POST['pokemon']) || count($_POST['pokemon']) == 0) {
			echo '<div class="error">You did not select any pokemon to offer.</div>';
			break;
		}
		
		foreach ($_POST['pokemon'] as $key => $value) {
			$_POST['pokemon'][$key] = (int) $value;	
		}
		
		$query = mysql_query("SELECT `poke1`,`poke2`,`poke3`,`poke4`,`poke5`,`poke6` FROM `users` WHERE `id`='{$uid}'");
		$myTeam = mysql_fetch_assoc($query);
		
		$nonInTeamSql = " `id` NOT IN ('".implode("', '", $myTeam)."') ";
		$offerSql = " `id` IN ('".implode("', '", $_POST['pokemon'])."') ";
		$query = mysql_query("SELECT * FROM `user_pokemon` WHERE {$nonInTeamSql} AND {$offerSql} AND `uid`='{$uid}' ORDER BY `name`");
		
		if ( mysql_num_rows($query) != count($_POST['pokemon']) ) {
			echo '<div class="error">There was an error.</div>';
			break;
		}
		
		$query2 = mysql_query("SELECT `oid` FROM `offer_pokemon` ORDER BY `oid` DESC LIMIT 1");
		$oid = mysql_fetch_assoc($query2);
		$oid = $oid['oid']+1;
		
		echo '<div class="notice">The following pokemon have been offered!</div>';
		
		echo '<div style="text-align: center;">';
		while ($p = mysql_fetch_assoc($query)) {
			echo '
				<img src="images/pokemon/'.$p['name'].'.png" /><br />
				'.$p['name'].'<br />
				Level: '.$p['level'].'<br />
			';
			
			$p['name'] = mysql_real_escape_string($p['name']);
			
			mysql_query("INSERT INTO `offer_pokemon` (`tid`, `oid`, `uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$tid}', '{$oid}', '{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `user_pokemon` WHERE `id`='{$p['id']}'");
		}
		echo '</div>';
	break;
	
	case 'vao':
		$query = mysql_query("SELECT * FROM `trade_pokemon` WHERE `uid`='{$uid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">You have no pokemon up for trade.</div>';
			break;
		}
		
		echo '
			<table class="pretty-table">
				<tr>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Level</th>
					<th>Exp</th>
					<th>Moves</th>
					<th>Options</th>
				</tr>
		';
		while ($pokemon = mysql_fetch_assoc($query)) {
			$query2 = mysql_query("SELECT * FROM `offer_pokemon` WHERE `tid`='{$pokemon['id']}' GROUP BY `oid`");
			$numOffers = mysql_num_rows($query2);
			echo '
				<tr>
					<td><img src="images/pokemon/'.$pokemon['name'].'.png" /></td>
					<td>'.$pokemon['name'].'</td>
					<td>'.number_format($pokemon['level']).'</td>
					<td>'.number_format($pokemon['exp']).'</td>
					<td>
						'.$pokemon['move1'].'<br />
						'.$pokemon['move2'].'<br />
						'.$pokemon['move3'].'<br />
						'.$pokemon['move4'].'
					</td>
					<td>
						<a href="?a=vo&id='.$pokemon['id'].'">View&nbsp;Offers&nbsp;('.$numOffers.')</a><br /><br />
						<a href="?a=remove&id='.$pokemon['id'].'">Remove</a><br />
					</td>
				</tr>
			';
		}
		echo '
			</table>
		';
	break;
	
	case 'vo':
		$tid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT * FROM `trade_pokemon` WHERE `uid`='{$uid}' AND `id`='{$tid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">This trade does not exist.</div>';
			break;
		}
		
		$tpoke = mysql_fetch_assoc($query);
		echo '
			<div style="text-align: center;">
				<img src="images/pokemon/'.$tpoke['name'].'.png" /><br />
				'.$tpoke['name'].'<br />
				Level: '.number_format($tpoke['level']).'
			</div>
		';
		
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `tid`='{$tid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="info">You have no offers on this pokemon.</div>';
			break;
		}
		
		$offers = array();
		while ($p = mysql_fetch_assoc($query)) {
			$query2 = mysql_query("SELECT * FROM `users` WHERE `id`='{$p['uid']}'");
			$username = mysql_fetch_assoc($query2);
			$username = $username['username'];
			$p['username'] = $username;
			$offers[$p['oid']][] = $p;
		}
		//echo '<pre>';
		//print_r($offers);
		//echo '</pre>';
		
		
		
		foreach ($offers as $oid => $pokemons) {			
			echo '
				<table class="pretty-table">
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Level</th>
						<th>Exp</th>
						<th>Moves</th>
						<th>Offer From</th>
					</tr>
			';
			foreach ($pokemons as $pokemon) {
				echo '
					<tr>
						<td><img src="images/pokemon/'.$pokemon['name'].'.png" /></td>
						<td>'.$pokemon['name'].'</td>
						<td>'.number_format($pokemon['level']).'</td>
						<td>'.number_format($pokemon['exp']).'</td>
						<td>
							'.$pokemon['move1'].'<br />
							'.$pokemon['move2'].'<br />
							'.$pokemon['move3'].'<br />
							'.$pokemon['move4'].'
						</td>
						<td>'.htmlspecialchars($pokemon['username']).'</td>
				';

				echo '
					</tr>
				'; 
			}
			echo '
					<tr>
						<td colspan="6">
							<a href="?a=accept&id='.$oid.'">Accept Offer</a> &bull; 
							<a href="?a=decline&id='.$oid.'">Decline Offer</a>
						</td>
					</tr>
				</table>
			';
		}
		
	break;
	
	case 'decline':
		$oid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT `tid` FROM `offer_pokemon` WHERE `oid`='{$oid}' LIMIT 1");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="notice">That offer does not exist!</div>';
			break;
		}
		$row = mysql_fetch_assoc($query);
		
		$query = mysql_query("SELECT `uid` FROM `trade_pokemon` WHERE `id`='{$row['tid']}' LIMIT 1");
		$row = mysql_fetch_assoc($query);
		
		if ($row['uid'] != $uid) {
			echo '<div class="notice">This offer does not belong to you!</div>';
			break;
		}
		
		echo '<div class="notice">The offer was declined!</div>';
		
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `oid`='{$oid}'");
		while ($p = mysql_fetch_assoc($query)) {
			$p['name'] = mysql_real_escape_string($p['name']);
			mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `offer_pokemon` WHERE `id`='{$p['id']}'");
		}
	break;
	
	case 'accept':
		$oid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT `tid` FROM `offer_pokemon` WHERE `oid`='{$oid}' LIMIT 1");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="notice">That offer does not exist!</div>';
			break;
		}
		$row = mysql_fetch_assoc($query);
		$tid = $row['tid'];
		
		$query = mysql_query("SELECT `uid` FROM `trade_pokemon` WHERE `id`='{$tid}' LIMIT 1");
		$row = mysql_fetch_assoc($query);
		
		if ($row['uid'] != $uid) {
			echo '<div class="notice">This offer does not belong to you!</div>';
			break;
		}
		
		echo '<div class="notice">The offer was accepted!</div>';
		
		
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `oid`='{$oid}'");
		while ($p = mysql_fetch_assoc($query)) {
			$tuid = $p['uid'];
			$p['name'] = mysql_real_escape_string($p['name']);
			mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$uid}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `offer_pokemon` WHERE `id`='{$p['id']}'");
		}
		
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `tid`='{$tid}'");
		while ($p = mysql_fetch_assoc($query)) {
			$p['name'] = mysql_real_escape_string($p['name']);
			mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `offer_pokemon` WHERE `id`='{$p['id']}'");
		}
		
		
		$query = mysql_query("SELECT * FROM `trade_pokemon` WHERE `id`='{$tid}'");
		$p = mysql_fetch_assoc($query);
		$p['name'] = mysql_real_escape_string($p['name']);
		mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$tuid}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
		mysql_query("DELETE FROM `trade_pokemon` WHERE `id`='{$p['id']}'");
	break;
	
	case 'remove':
		$tid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT `uid` FROM `trade_pokemon` WHERE `id`='{$tid}' LIMIT 1");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="notice">You can not remove this trade!</div>';
			break;
		}
		$row = mysql_fetch_assoc($query);
	
		if ($row['uid'] != $uid) {
			echo '<div class="notice">This offer does not belong to you!</div>';
			break;
		}
		
		echo '<div class="notice">This trade has been removed!</div>';
		
		
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `tid`='{$tid}'");
		while ($p = mysql_fetch_assoc($query)) {
			//$tuid = $p['uid'];
			$p['name'] = mysql_real_escape_string($p['name']);
			mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `offer_pokemon` WHERE `id`='{$p['id']}'");
		}
		
		$query = mysql_query("SELECT * FROM `trade_pokemon` WHERE `id`='{$tid}'");
		$p = mysql_fetch_assoc($query);
		$p['name'] = mysql_real_escape_string($p['name']);
		mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
		mysql_query("DELETE FROM `trade_pokemon` WHERE `id`='{$p['id']}'");
	break;
	
	case 'va':	
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `uid`='{$uid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">You have made no offers.</div>';
			break;
		}
		
		$offers = array();
		while ($p = mysql_fetch_assoc($query)) {
			$query2 = mysql_query("SELECT * FROM `trade_pokemon` WHERE `id`='{$p['tid']}'");
			$tradeRow = mysql_fetch_assoc($query2);

			$query2 = mysql_query("SELECT * FROM `users` WHERE `id`='{$tradeRow['uid']}'");
			$userRow = mysql_fetch_assoc($query2);
			
			$tradeRow['username'] = $userRow['username'];
			$p['r'] = $tradeRow;
			$offers[$p['oid']][] = $p;
		}
		//echo '<pre>';
		//print_r($offers);
		//echo '</pre>';
		
		
		
		foreach ($offers as $oid => $pokemons) {			
			echo '
				<table class="pretty-table">
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Level</th>
						<th>Exp</th>
						<th>Moves</th>
					</tr>
			';
			foreach ($pokemons as $pokemon) {
				echo '
					<tr>
						<td><img src="images/pokemon/'.$pokemon['name'].'.png" /></td>
						<td>'.$pokemon['name'].'</td>
						<td>'.number_format($pokemon['level']).'</td>
						<td>'.number_format($pokemon['exp']).'</td>
						<td>
							'.$pokemon['move1'].'<br />
							'.$pokemon['move2'].'<br />
							'.$pokemon['move3'].'<br />
							'.$pokemon['move4'].'
						</td>
				';

				echo '
					</tr>
				'; 
			}
			echo '
					<tr>
						<td colspan="5">
							You offered these pokemon for:<br />
							<img src="images/pokemon/'.$pokemon['r']['name'].'.png" /><br />
							Owner: '.htmlspecialchars($pokemon['r']['username']).'<br />
							Level: '.number_format($pokemon['r']['level']).'<br />
							Exp: '.number_format($pokemon['r']['exp']).'<br /><br />
							<a href="?a=reo&id='.$oid.'">Remove Offer</a>
						</td>
					</tr>
				</table>
			';
		}
		
	break;
	
	case 'reo':
		$oid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT `uid` FROM `offer_pokemon` WHERE `oid`='{$oid}' LIMIT 1");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">You can not remove this offer!</div>';
			break;
		}
		$row = mysql_fetch_assoc($query);
	
		if ($row['uid'] != $uid) {
			echo '<div class="error">This offer does not belong to you!</div>';
			break;
		}
		
		echo '<div class="notice">This offer has been removed!</div>';
		
		
		$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `oid`='{$oid}'");
		while ($p = mysql_fetch_assoc($query)) {
			//$tuid = $p['uid'];
			$p['name'] = mysql_real_escape_string($p['name']);
			mysql_query("INSERT INTO `user_pokemon` (`uid`, `name`, `exp`, `level`, `move1`, `move2`, `move3`, `move4`)
				VALUES ('{$p['uid']}', '{$p['name']}', '{$p['exp']}', '{$p['level']}', '{$p['move1']}', '{$p['move2']}', '{$p['move3']}', '{$p['move4']}')");
				
			mysql_query("DELETE FROM `offer_pokemon` WHERE `id`='{$p['id']}'");
		}
	break;
}






echo '</div>';
include '_footer.php';
?>