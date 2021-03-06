<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php';
printHeader('Release Pokemon');

$uid = (int) $_SESSION['userid'];
$pid = (int) $_GET['id'];
$releaseReward = getConfigValue('release_reward');

// check that the pokemon exists and they own it
$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$pid}' AND `uid`='{$uid}'");

if (mysql_num_rows($query) == 0) {
	echo '<div class="error">This pokemon either doesn\'t exist or doesn\'t belong to you.</div>';
	include '_footer.php';
	die();
}
$pokeInfo = mysql_fetch_assoc($query);
//---------------------------------

// check that it is not in their team
$teamIds = getUserTeamIds($uid);

if (in_array($pid, $teamIds)) {
	echo '<div class="error">You can not release a pokemon that is in your team.</div>';
	include '_footer.php';
	die();
}
// --------------------------------

if (isset($_GET['sure'])) {
	if (!isset($_SESSION['releaseToken'][$pid])) {
		echo '<div class="error">Token not set.</div>';
	} else if ($_SESSION['releaseToken'][$pid] != $_GET['token']) {
		echo '<div class="error">Tokens do not match.</div>';
	} else {
		echo '
			<div style="text-align: center;">
				<div class="notice">You have released '.$pokeInfo['name'].'!</div>
				<img src="images/pokemon/'.$pokeInfo['name'].'.png" alt="'.$pokeInfo['name'].'" /><br />
				<a href="view_box.php">Back to your box.</a><br /><br />
			</div>
		';
		
		mysql_query("DELETE FROM `user_pokemon` WHERE `uid`='{$uid}' AND `id`='{$pid}'");
		mysql_query("UPDATE `users` SET `released`=`released`+1 WHERE `id`='{$uid}'");
		updateUserMoney($uid, getUserMoney($uid) + $releaseReward);
		
		unset($_SESSION['releaseToken'][$pid]);
	}
	
	
} else {
	$token = md5( rand(1000, 5000) );
	$_SESSION['releaseToken'][$pid] = $token;
	echo '
		<p>
			Are you sure you want to release '.$pokeInfo['name'].'?<br />
			<img src="images/pokemon/'.$pokeInfo['name'].'.png" alt="'.$pokeInfo['name'].'" /><br />
			<a href="release.php?id='.$pid.'&token='.$token.'&sure">Yes</a> &bull; 
			<a href="view_box.php">No</a><br /><br />
		</p>
	';
	
	if ($releaseReward != 0) {
		echo '
			<div style="font-size: 10pt; text-align: center; color: #FFFFFF;">
				If you release a pokemon you get a $'.number_format($releaseReward).' reward!
			</div><br /><br />
		';
	}
}

include '_footer.php';
?>