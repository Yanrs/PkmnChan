<?php
include 'config.php';
include 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

$uid = (int) $_SESSION['userid'];
logs($uid, " accessed friends page!");

include '_header.php';
printHeader('Friends List');


$query = mysql_query("SELECT * FROM `friends` WHERE `uid`='{$uid}'");

if (mysql_num_rows($query) == 0) {
	echo '<div class="error">You have no friends.</div>';
	include '_footer.php';
	die();
}

echo '
	<table style="width: 98%; margin-bottom: 10px;" class="pretty-table">
		<tr>
			<th>Username</th>
			<th>Money</th>
			<th>Total Exp</th>
			<th colspan="2">&nbsp</th>
		</tr>
';

$i=0;
while($fRow = mysql_fetch_assoc($query)) {
	$query2 = mysql_query("
		SELECT
			`users`.*,
			SUM(`user_pokemon`.`exp`) AS `total_exp`
		FROM
			`users`,
			`user_pokemon`
		WHERE
			`users`.`id` = '{$fRow['friendid']}' AND
			`users`.`id` = `user_pokemon`.`uid`
	");
	$userInfo = mysql_fetch_array($query2);
	$userInfo = cleanHtml($userInfo);
	
	echo '
		<tr>
			<td><a href="profile.php?id='.$userInfo['id'].'">'.$userInfo['username'].'</a></td>
			<td>$'.number_format($userInfo['money']).'</td>
			<td>'.number_format($userInfo['total_exp']).'</td>
			<td><a href="#" onclick="if (document.getElementById(\'team'.$i.'\').style.display == \'none\') { this.textContent = \'Hide&nbsp;Team\'; document.getElementById(\'team'.$i.'\').style.display = \'table-row\'; } else { this.textContent = \'Show&nbsp;Team\'; document.getElementById(\'team'.$i.'\').style.display = \'none\'; } return false;">Show&nbsp;Team</a></td>
			<td><a href="messages.php?p=new&uid='.$userInfo['id'].'">Send&nbsp;Message</a></td>
		</tr>
	';
	
	$cells = array();
	for ($x=1; $x<=6; $x++) {
		$pid = $userInfo[ 'poke' . $x ];
		
		
		if ($pid == 0) {
			$cells[] = '
				<img src="images/pokemon/EMPTY.png" alt="No Pokemon" /><br />
				Empty Slot<br /><br />
			';
		} else {
			$query3 = mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$pid}'");
			$pokeInfo = mysql_fetch_assoc($query3);
			
			$cells[] = '
				<img src="images/pokemon/'.$pokeInfo['name'].'.png" alt="'.$pokeInfo['name'].'" /><br />
				'.$pokeInfo['name'].'<br />
				Level: '.number_format($pokeInfo['level']).'<br />
				Exp: '.number_format($pokeInfo['exp']).'<br />
			';
		}
	}
	echo '
		<tr style="display: none;" id="team'.$i.'">
			<td colspan="5">
				<h1>'.$userInfo['username'].'s team!</h1>
				<table>'.cellsToRows($cells, 3).'</table>
			</td>
		</tr>
	';
	$i++;
}

echo '
	</table>
';

include '_footer.php';
?>