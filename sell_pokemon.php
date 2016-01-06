<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'pagination.class.php';

if (!isLoggedIn()) {
redirect('login.php');
}

include '_header.php';
printHeader('Sell Pokemon');

$uid = (int) $_SESSION['userid'];

$userMoney = getUserMoney($uid);

echo '
	<div style="text-align: center; margin: 10px 0;">
		<a href="?p=sell">Sell Pokemon</a> &bull; 
		<a href="?p=mine">View My Sales</a> &bull; 
		<a href="?p=all">View All Sales</a> &bull; 
		<a href="?p=history">My Sale History</a>
		
		<br /><br />
		
		You have $'.number_format($userMoney).'
	</div>
';

switch ($_GET['p']) {
	case 'sell':
		$query = mysql_query("SELECT * FROM `users` WHERE `id`='{$uid}' LIMIT 1");
		$myTeam = mysql_fetch_assoc($query);
		
		$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `uid`='{$uid}' AND `id` NOT IN ('{$myTeam['poke1']}', '{$myTeam['poke2']}', '{$myTeam['poke3']}', '{$myTeam['poke4']}', '{$myTeam['poke5']}', '{$myTeam['poke6']}')");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="info">You have no pokemon to put up for sale.</div>';
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
			echo '
				<tr>
					<td><a href="pinfo.php?id='.$pokemon['id'].'"><img src="images/pokemon/'.$pokemon['name'].'.png" /></a></td>
					<td>'.$pokemon['name'].'</td>
					<td>'.$pokemon['level'].'</td>
					<td>'.number_format($pokemon['exp']).'</td>
					<td>
						'.$pokemon['move1'].'<br />
						'.$pokemon['move2'].'<br />
						'.$pokemon['move3'].'<br />
						'.$pokemon['move4'].'<br />
					</td>
					<td>
						<a href="?p=sell2&id='.$pokemon['id'].'">Sell</a>
					</td>
				</tr>
			';
		}
		echo '</table>';
	break;
	
	case 'sell2':
		$pid = (int) $_GET['id'];
		$query = mysql_query("SELECT `poke1`,`poke2`,`poke3`,`poke4`,`poke5`,`poke6` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
		$myTeam = mysql_fetch_assoc($query);
		
		$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `uid`='{$uid}' AND `id`='{$pid}'");
		
		if (mysql_num_rows($query) == 0 || in_array($pid, $myTeam)) {
			echo '<div class="error">You can not sell this pokemon.</div>';
			break;
		}
		
		$pokemon = mysql_fetch_assoc($query);
		
		echo '
			<table class="pretty-table"><tr><td>You are about to sell '.$pokemon['name'].'</td></tr><tr>
				<td><img src="images/pokemon/'.$pokemon['name'].'.png" /><br />
				<strong>'.$pokemon['name'].'</strong><br />
				Level: '.$pokemon['level'].'<br />
				Exp: '.number_format($pokemon['exp']).'</td></tr><tr><td>
		';
			
		if (isset($_POST['price'])) {
			$price = (int) $_POST['price'];
			$price = $price < 1 ? 1000 : $price ;
			echo '<div class="notice">People can now buy this pokemon for $'.number_format($price).'.</div>';
			
			$username = cleanSql($_SESSION['username']);
			mysql_query("DELETE FROM `user_pokemon` WHERE `id`='{$pid}' LIMIT 1");
			mysql_query("INSERT INTO `sale_pokemon` (
				`name`, `level`, `exp`, `move1`, `move2`, `move3`, `move4`, `uid`, `username`, `price`
				) VALUES (
				'{$pokemon['name']}', '{$pokemon['level']}', '{$pokemon['exp']}', '{$pokemon['move1']}', '{$pokemon['move2']}', '{$pokemon['move3']}', '{$pokemon['move4']}', '{$uid}', '{$username}', '{$price}'
				)
			");
			mysql_query("UPDATE `users` SET `total_sale_pokes`=`total_sale_pokes`+1 WHERE `id`='{$uid}' LIMIT 1");
		} else {
			echo '
				<form action="?p=sell2&id='.$pid.'" method="post">
					Price: <input type="text" name="price" value="1000" />
					<input type="submit" value="Put up for sale!" />
				</form>
			';
		}
		echo '</td></tr></table>';
		
	break;
	
	case 'mine':
		$query = mysql_query("SELECT * FROM `sale_pokemon` WHERE `uid`='{$uid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="info">You have no pokemon up for sale.</div>';
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
					<th>Price</th>
					<th>Options</th>
				</tr>
		';
		while ($pokemon = mysql_fetch_assoc($query)) {
			echo '
				<tr>
					<td><img src="images/pokemon/'.$pokemon['name'].'.png" /></td>
					<td>'.$pokemon['name'].'</td>
					<td>'.$pokemon['level'].'</td>
					<td>'.number_format($pokemon['exp']).'</td>
					<td>
						'.$pokemon['move1'].'<br />
						'.$pokemon['move2'].'<br />
						'.$pokemon['move3'].'<br />
						'.$pokemon['move4'].'<br />
					</td>
					<td>$'.number_format($pokemon['price']).'</td>
					<td>
						<a href="?p=remove&id='.$pokemon['id'].'">Remove</a>
					</td>
				</tr>
			';
		}
		echo '</table>';
	break;
	
	case 'remove':
		$pid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT * FROM `sale_pokemon` WHERE `uid`='{$uid}' AND `id`='{$pid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">This is not your pokemon to remove.</div>';
			break;
		}
		
		$pokemon = mysql_fetch_assoc($query);
		
		echo '
			<div style="text-align: center;">
				<img src="images/pokemon/'.$pokemon['name'].'.png" /><br />
				'.$pokemon['name'].'<br />
				Level: '.$pokemon['level'].'<br />
				Exp: '.number_format($pokemon['exp']).'<br /><br /><br />
				'.$pokemon['name'].' is no longer up for sale.
			</div>
		';
		
		mysql_query("DELETE FROM `sale_pokemon` WHERE `id`='{$pid}' LIMIT 1");
		mysql_query("UPDATE `users` SET `total_sale_pokes`=`total_sale_pokes`-1 WHERE `id`='{$uid}' LIMIT 1");
        giveUserPokemon($uid, $pokemon['name'], $pokemon['level'], $pokemon['exp'], $pokemon['move1'], $pokemon['move2'], $pokemon['move3'], $pokemon['move4']);

	break;
	
	case 'all':

		$sorts = array
		(
		    1 => ' ORDER BY `name` ASC',
		    2 => ' ORDER BY `name` DESC',
		    3 => ' ORDER BY `exp` ASC',
		    4 => ' ORDER BY `exp` DESC',
		    5 => ' ORDER BY `price` ASC',
		    6 => ' ORDER BY `price` DESC',
		    7 => ' ORDER BY `id` ASC',
		    8 => ' ORDER BY `id` DESC'
		);

		$search    = isset($_GET['search']) ? $_GET['search'] : '' ;
		$searchSql = '';

		$sort      = $_GET['sort'];
		$sortKey   = isset($sort) && in_array($sort, array_keys($sorts)) ? $sort : 5 ;
		$orderSql  = $sorts[$sortKey];

		if (!empty($search)) {
			$searchSqlSafe  = cleanSql($search);
			$searchHtmlSafe = cleanHtml($search);
			$searchSql      = " AND `name` LIKE '%{$searchSqlSafe}%' ";
		}

		$countQuery = mysql_query("SELECT * FROM `sale_pokemon` WHERE `uid` != '{$uid}' {$searchSql}");
		$numRows    = mysql_num_rows($countQuery);
		$pagination = new Pagination($numRows);

		if (!empty($search)) {
			$pagination->addQueryStringVar('search', $_GET['search']);
		}
		$pagination->addQueryStringVar('p', 'all');

		$query = mysql_query("SELECT * FROM `sale_pokemon` WHERE `uid` != '{$uid}' {$searchSql} {$orderSql} LIMIT {$pagination->itemsPerPage} OFFSET {$pagination->startItem}");



		$qs = '';
		$qs .= 'p=all';
		if (!empty($search)) {
			$qs .= '&amp;search=' . urlencode($search);
		}

		$nameOrder  = $_GET['sort'] == 1 ? 2 : 1 ;
		$expOrder   = $_GET['sort'] == 3 ? 4 : 3 ;
		$priceOrder = $_GET['sort'] == 6 ? 5 : 6 ;
		$idOrder    = $_GET['sort'] == 7 ? 8 : 7 ;

		echo '
			<form method="get" action="" style="text-align: center; margin: 20px 0px;">
				<input type="hidden" value="all" name="p" />
				Search For: <input type="text" name="search" value="'.$searchHtmlSafe.'" />
				<input type="submit" value="Search" />
			</form>
		';

		if (mysql_num_rows($query) == 0) {
			echo '<div class="info">Could not find any users.</div>';
		} else {
			if (mysql_num_rows($query) == 0) {
				echo '<div class="info">There are no pokemon up for sale.</div>';
				break;
			}
		
			echo '
				<table class="pretty-table">
					<tr>
						<th><a href="?'.$qs.'&amp;sort='.$idOrder.'">ID</a></th>
						<th><a href="?'.$qs.'&amp;sort='.$nameOrder.'">Name</a></th>
						<th>Level</th>
						<th><a href="?'.$qs.'&amp;sort='.$expOrder.'">Exp</a></th>
						<th>Moves</th>
						<th><a href="?'.$qs.'&amp;sort='.$priceOrder.'">Price</a></th>
						<th>Options</th>
					</tr>
			';
			
			while ($pokemon = mysql_fetch_assoc($query)) {
				echo '
					<tr>
						<td>'.number_format($pokemon['id']).'</td>
						<td><img src="images/pokemon/'.$pokemon['name'].'.png" /><br />
						'.$pokemon['name'].'</td>
						<td>'.number_format($pokemon['level']).'</td>
						<td>'.number_format($pokemon['exp']).'</td>
						<td>
							'.$pokemon['move1'].'<br />
							'.$pokemon['move2'].'<br />
							'.$pokemon['move3'].'<br />
							'.$pokemon['move4'].'<br />
						</td>
						<td>$'.number_format($pokemon['price']).'</td>
						<td>
							<a href="?p=buy&id='.$pokemon['id'].'">Buy Pokemon</a><br />
							from <a href="profile.php?id='.$pokemon['uid'].'">'.cleanHtml($pokemon['username']).'</a>
						</td>
					</tr>
				';
			}
			echo '</table>';
	
			$pagination->echoPagination();
		}
	break;
	
	case 'buy':
		$pid = (int) $_GET['id'];
		
		$query = mysql_query("SELECT * FROM `sale_pokemon` WHERE `uid`!='{$uid}' AND `id`='{$pid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">Could not find the pokemon!</div>';
			break;
		}
		
		$pokemon = mysql_fetch_assoc($query);
		
		echo '
			<table class="pretty-table">
				<tr><td>
Are you sure you want to buy '.$pokemon['name'].'?</td></tr><tr><td><center><img src="images/pokemon/'.$pokemon['name'].'.png" /><br />
				'.$pokemon['name'].'<br />
				Level: '.$pokemon['level'].'<br />
				Exp: '.number_format($pokemon['exp']).'</td></tr><tr><td>
		';
		 
		if (isset($_POST['sure'])) {
			$query = mysql_query("SELECT `money` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
			$userMoney = mysql_fetch_assoc($query);
			$userMoney = $userMoney['money'];
			$query2 = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
			$userMoney2 = mysql_fetch_assoc($query2);
			$userMoney2 = $userMoney2['username'];

			if ($userMoney < $pokemon['price']) {
				echo '<div class="error">You can not afford this pokemon!</div>';
			} else {
				mysql_query("DELETE FROM `sale_pokemon` WHERE `id`='{$pid}' LIMIT 1");
				mysql_query("UPDATE `users` SET `money`=`money`-{$pokemon['price']} WHERE `id`='{$uid}'");
				mysql_query("UPDATE `users` SET `money`=`money`+{$pokemon['price']} , `newly_sold_pokes`=`newly_sold_pokes`+1 , `total_sale_pokes`=`total_sale_pokes`-1 WHERE `id`='{$pokemon['uid']}'");
                                send_event($pokemon['uid'], "$userMoney2 has bought your pokemon!");
				/*mysql_query("INSERT INTO `user_pokemon` (
					`name`, `level`, `exp`, `move1`, `move2`, `move3`, `move4`, `uid`
					) VALUES (
					'{$pokemon['name']}', '{$pokemon['lekemon['move2']}', '{$pokemon['move3']}', '{$pokemon['move4']}', '{$uid}'
					)
				");*/
giveUserPokemon($uid, $pokemon['name'], $pokemon['level'], $pokemon['exp'], $pokemon['move1'], $pokemon['move2'], $pokemon['move3'], $pokemon['move4']);
				
				/*$query = mysql_query("SELECT `id` FROM `user_pokemon` WHERE `uid`='{$uid}'");
				$numPokes = mysql_num_rows($query);
				if ($numPokes <= 6) {
					if ($numPokes < 1) { $numPokes = 1; }
					$pokeId = mysql_insert_id();
					mysql_query("UPDATE `users` SET `poke{$numPokes}`='$pokeId' WHERE `id`='{$uid}'");
				}*/
				
				$username = mysql_real_escape_string($_SESSION['username']);
				mysql_query("INSERT INTO `sale_history` (
					`name`, `level`, `exp`, `move1`, `move2`, `move3`, `move4`, `uid`, `username`, `soldto`, `sid`, `price`
					) VALUES (
					'{$pokemon['name']}', '{$pokemon['level']}', '{$pokemon['exp']}', '{$pokemon['move1']}', '{$pokemon['move2']}', '{$pokemon['move3']}', '{$pokemon['move4']}', '{$pokemon['uid']}', '{$pokemon['username']}', '{$username}', '{$uid}', '{$pokemon['price']}'
					)
				");
				
				
				
				echo '<div class="notice">You have bought '.$pokemon['name'].' for $'.number_format($pokemon['price']).'.</div>';
			}
		} else {
			echo '
				<form action="?p=buy&id='.$pokemon['id'].'" method="post">
					<input type="submit" name="sure" value="Buy '.$pokemon['name'].' for $'.number_format($pokemon['price']).'" />
				</form>
			';
		}
		echo '</td></tr></table>';
		
	break;
	
	case 'history':
		$query = mysql_query("SELECT * FROM `sale_history` WHERE `uid`='{$uid}' AND `udeleted`='0' OR `sid`='{$uid}' AND `sdeleted`='0'");
		
		mysql_query("UPDATE `sale_history` SET `seen`='1' WHERE `uid`='{$uid}' AND `udeleted`='0' OR `sid`='{$uid}' AND `sdeleted`='0'");
		
		mysql_query("UPDATE `users` SET `newly_sold_pokes`='0' WHERE `id`='{$uid}'");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="info">You have no sale history.</div>';
			break;
		}
		
		echo '
			<p style="text-align: center;"><a href="?p=clear_history">Clear History</a></p>
			<table class="pretty-table">
				<tr>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Level</th>
					<th>Exp</th>
					<th>Moves</th>
					<th>Details</th>
				</tr>
		';
		while ($pokemon = mysql_fetch_assoc($query)) {
			echo '
				<tr>
					<td><a href="pinfo.php?id='.$pokemon['id'].'"><img src="images/pokemon/'.$pokemon['name'].'.png" /></a></td>
					<td>'.$pokemon['name'].'</td>
					<td>'.$pokemon['level'].'</td>
					<td>'.number_format($pokemon['exp']).'</td>
					<td>
						'.$pokemon['move1'].'<br />
						'.$pokemon['move2'].'<br />
						'.$pokemon['move3'].'<br />
						'.$pokemon['move4'].'<br />
					</td>
					<td>
			';
			if ($uid == $pokemon['sid']) {
				echo '
					Brought from <br />
					<strong><a href="profile.php?id='.$pokemon['sid'].'">'.cleanHtml($pokemon['username']).'</a></strong><br />
					for <br />
					<strong>$'.number_format($pokemon['price']).'</strong>.
				';
			} else {
				echo '
					Sold to <br />
					<strong><a href="profile.php?id='.$pokemon['sid'].'">'.cleanHtml($pokemon['soldto']).'</a></strong><br />
					for <br />
					<strong>$'.number_format($pokemon['price']).'</strong>.
				';
			}
			echo '
					</td>
				</tr>
			';
		}
		echo '</table>';
	break;
	
	case 'clear_history':
		mysql_query("UPDATE `users` SET `newly_sold_pokes`='0' WHERE `id`='{$uid}'");
		mysql_query("UPDATE `sale_history` SET `udeleted`='1' WHERE `uid`='{$uid}'") or die(mysql_error());
		mysql_query("UPDATE `sale_history` SET `sdeleted`='1' WHERE `sid`='{$uid}'") or die(mysql_error());
		mysql_query("DELETE FROM `sale_history` WHERE `sdeleted`='1' AND `udeleted`='1'") or die(mysql_error());
		
		echo '<div class="notice">Your history has been cleared.</div>';
	break;
}

include '_footer.php';
?>