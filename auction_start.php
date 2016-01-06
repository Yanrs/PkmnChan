<?php
include 'config.php';
include 'functions.php';

if (!isLoggedIn()) { redirect('login.php'); }

if (!isset($_GET['id'])) { redirect('membersarea.php'); }
$pid = (int) $_GET['id'];
$uid = (int) $_SESSION['userid'];
$sqlUsername = cleanSql($_SESSION['username']);

include '_header.php';
printHeader('Start An Auction');

$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$pid}' AND `uid`='{$uid}'");
if (mysql_num_rows($query) == 0) { 
	echo '<div class="error">Not your pokemon!</div>';
	include '_footer.php';
	die();
}
$pokeRow = mysql_fetch_assoc($query);

if (in_array($pokeRow['id'], getUserTeamIds($uid))) { 
	echo '<div class="error">This pokemon is in your team,</div>';
	include '_footer.php';
	die();
}

if (isset($_POST['duration']) && in_array($_POST['duration'], range(0, 4))) {
	$costs = array(
		'0' => 200,  // 10 mins
		'1' => 1000,  // 1 hour
		'2' => 5000,  // 6 hours
		'3' => 10000, // 1 day
		'4' => 15000  // 7 days
	);
	$cost = $costs[ $_POST['duration'] ];
	if (getUserMoney($uid) < $cost) {
		echo '<div class="error">Not enough money.</div>';
	} else {
		$times = array(
			'0' => 60*10,  // 10 mins
			'1' => 60*60, // 1 hour
			'2' => 60*60*6, // 6 hours
			'3' => 60*60*24, // 1 day
			'4' => 60*60*24*7 // 7 days
		);
		$finishTime = time() + $times[ $_POST['duration'] ];
		
		$query = mysql_query("
			INSERT INTO `auction_pokemon`
			(
				`owner_id`,
				`owner_username`,
				`bidder_id`,
				`bidder_username`,
				`current_bid`,
				`name`,
				`exp`,
				`level`,
				`move1`,
				`move2`,
				`move3`,
				`move4`,
				`num_bids`,
				`gender`,
				`finish_time`
			) VALUES (
				'{$uid}',
				'{$sqlUsername}',
				'0',
				'',
				'1',
				'{$pokeRow['name']}',
				'{$pokeRow['exp']}',
				'{$pokeRow['level']}',
				'{$pokeRow['move1']}',
				'{$pokeRow['move2']}',
				'{$pokeRow['move3']}',
				'{$pokeRow['move4']}',
				'0',
				'{$pokeRow['gender']}',
				'{$finishTime}'
			)
		");
		if ($query) {
			mysql_query("DELETE FROM `user_pokemon` WHERE `id`='{$pid}' LIMIT 1");
			updateUserMoney($uid, getUserMoney($uid)-$cost);
		}
		echo '<div class="notice">Your pokemon is now up for auction!</div>';
		include '_footer.php';
		die();
	}
}



echo '
	<img src="images/pokemon/'.$pokeRow['name'].'.png" /><br />
	'.$pokeRow['name'].'<br />
	Level: '.$pokeRow['level'].'<br />
	Exp: '.$pokeRow['exp'].'<br />
	<br /><hr /><br />
	<form action="" method="post">
		<h3>How long do you want the auction to last?</h3><br />
		<input type="radio" name="duration" value="0" /> 10 Minutes (Cost $200)<br />
		<input type="radio" name="duration" value="1" /> 1 Hour (Cost $1,000)<br />
		<input type="radio" name="duration" value="2" /> 6 Hour (Cost $5,000)<br />
		<input type="radio" name="duration" value="3" checked="checked" /> 1 Day (Cost $10,000)<br />
		<input type="radio" name="duration" value="4" /> 7 Days (Cost $15,000)<br /><br />
		<input type="submit" name="submit" value="Put Up For Auction" />
	</form>
	<br /><hr /><br />
	<h3>Things you should know.</h3><br />
	Auctions can not be cancelled.<br /><br />
	You can not bid on your own auction.<br /><br />
	<!--You can only have 10 pokemon up for auction at any one time.<br /><br />-->
	All bidding starts at $1.<br /><br />
	If no one bids the pokemon is released.<br />
	
';

include '_footer.php';
?>