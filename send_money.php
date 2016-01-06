<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

$uid       = (int) $_SESSION['userid'];
$userMoney = getUserMoney($uid);
$message   = isset($_SESSION['message']) ? $_SESSION['message'] : '' ;
$username  = '';
$amount    = '';

if (isset($_POST['amount']) && isset($_POST['username'])) {
	$amount        = (int) $_POST['amount'];
	$uni_username  = trim($_POST['username']);
	$sqlUsername   = cleanSql($uni_username);
	$sqlMyUsername = cleanSql($_SESSION['username']);
	
	$query = mysql_query("SELECT `id` FROM `users` WHERE `username`='{$sqlUsername}' LIMIT 1");

	if (mysql_num_rows($query) == 0) {
		$message = '<div class="error">There is not a user with that username.</div>';
	} else if ($amount > $userMoney) {
		$message = '<div class="error">You do not have that much money!</div>';
	} else if ($amount <= 0) {
		$message = '<div class="error">The lowest amount of money you can send is $1.</div>';
	} else if ($uni_username === $_SESSION['username']) {
		$message = '<div class="error">Why would you want to send money to your self?</div>';
	} else if (isset($_SESSION['send_money_token']) && $_SESSION['send_money_token'] != $_POST['token']) {
		$message = '<div class="error">Your tokens do not match!</div>';
	} else {
		$recUid     = mysql_fetch_assoc($query);
		$recUid     = $recUid['id'];
		$recMoney   = getUserMoney($recUid) + $amount;
		$userMoney -= $amount;
		$time       = time();

		updateUserMoney($recUid, $recMoney);
		updateUserMoney($uid, $userMoney);
		
		mysql_query("
			INSERT INTO `send_money_history` (
				`sender_uid`, `recipient_uid`, `sender`, `recipient`, `amount`, `timestamp`
			) VALUES (
				'{$uid}', '{$recUid}', '{$sqlMyUsername}', '{$sqlUsername}', '{$amount}', '{$time}'
			)
		");

		$_SESSION['message'] = '<div class="notice">You have sent $' . $amount . ' to ' . cleanHtml($uni_username) . '.</div>';
		if (isset($_GET['id'])) {
			$id = (int) $_GET['id'];
			redirect('send_money.php?id='.$id);
		} else {
			redirect('send_money.php');
		}
	}


}

include '_header.php';
printHeader('Send Money');

if (isset($_GET['id'])) {
	$id = (int) $_GET['id'];
	$query = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$id}'");
	
	if (mysql_num_rows($query) == 1) {
		$row = mysql_fetch_assoc($query);
		$uni_username = $row['username'];
	}
}



$token = md5( rand(10000, 99999) );
$_SESSION['send_money_token'] = $token;

echo '
	<center>
		<a href="send_money.php">Send Money</a> &bull; 
		<a href="send_money_history.php">View History</a><br /><br />
		
		' . $message . '
<table>
<tr>
		<td colspan="2">You have $' . number_format($userMoney) . '</td></tr>
		<form action="" method="post">

			<tr><th>Send To: </th><td><input type="text" name="username" value="' . cleanHtml($uni_username) . '" /><br /></td></tr>
			<tr><th>Amount:</th><td> <input type="text" name="amount" value="' . cleanHtml($amount) . '" /><br /></td></tr>
<tr><td>&nbsp;</td><td><input type="hidden" name="token" value="' . $token . '" /><input type="submit" value="Send Money" id="button"/></td></tr></table>
		</form>

</center>
';

if (isset($_SESSION['message'])) {
	unset($_SESSION['message']);
}

include '_footer.php';
?>