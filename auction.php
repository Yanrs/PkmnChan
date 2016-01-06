<?php
include 'config.php';
include 'functions.php';

function secsToTimeAmountArray($secs) {
	if ($secs <= 0) { return array('-'); }
	
        $seconds = array(
                'days'    => 86400,
                'hours'   =>  3600,
                'minutes' =>    60,
                'seconds' =>     1,
        );
        $timeAmounts = array();
        
        foreach ($seconds as $name => $seconds) {
        	$amount = intval($secs / $seconds);
        	$secs -= $amount * $seconds;
		if ($amount > 0) {
			$timeAmounts[] = $amount.' '.($amount == 1 ? rtrim($name, 's') : $name );
		}
        }
        return $timeAmounts;
}

if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php';
printHeader('Auctions');

$uid = (int) $_SESSION['userid'];
$sqlUsername = cleanSql($_SESSION['username']);

$query = mysql_query("SELECT * FROM `auction_pokemon` LIMIT 1");

if (mysql_num_rows($query) == 0) {
	echo '<div class="notice">There are no auctions!</div>';
	include '_footer.php';
	die();
}

if (isset($_POST['pid']) && isset($_POST['bid'])) {
	$errors = array();
	$pid = (int) $_POST['pid'];
	$bid = (int) $_POST['bid'];
	$query = mysql_query("SELECT * FROM `auction_pokemon` WHERE `id`='{$pid}'");
	
	if (mysql_num_rows($query) == 0) {
		$errors[] = 'Auction does not exist.';
	} else {
		$auctionRow = mysql_fetch_assoc($query);
		$time = time();
		
		if ($auctionRow['finish_time'] < $time) {
			$errors[] = 'This auction has finished.';
		}
		
		if ($bid <= $auctionRow['current_bid']) {
			$errors[] = 'Your bid is too low.';
		}
		
		if (getUserMoney($uid)-$bid < 0) {
			$errors[] = 'You do not have that much money!';
		}
		
		if ($uid == $auctionRow['owner_id']) {
			$errors[] = 'You can not bid on your own auction!';
		}
	}
	
	if (count($errors) != 0) {
		echo '<div class="error">'.implode('</div><div class="error">', $errors).'</div>';
	} else {
		if ($auctionRow['bidder_id'] != 0) {
			$lastBid = $auctionRow['current_bid'];
			$bidId = $auctionRow['bidder_id'];
			
			mysql_query("UPDATE `users` SET `money`=`money`+{$lastBid} WHERE `id`='{$bidId}' LIMIT 1");
		}
		
		mysql_query("UPDATE `auction_pokemon` SET `bidder_id`='{$uid}', `bidder_username`='{$sqlUsername}', `current_bid`='{$bid}', `num_bids`=`num_bids`+1 WHERE `id`='{$pid}' LIMIT 1");
		mysql_query("UPDATE `users` SET `money`=`money`-{$bid} WHERE `id`='{$uid}' LIMIT 1");
		
		echo '<div class="notice">You placed a bid of $'.number_format($bid).' on '.$auctionRow['name'].'.</div>';
	}
	
}

$extraSqlArr = array(
	'1' => array(
		'sql' => 'ORDER BY `id` DESC',
		'text' => 'Newest'
	),
	'2' => array(
		'sql' => 'ORDER BY `num_bids` DESC',
		'text' => 'Most Popular'
	),
	'3' => array(
		'sql' => 'ORDER BY `exp` DESC',
		'text' => 'Highest Level'
	),
	'4' => array(
		'sql' => "WHERE `owner_id`='{$uid}' ORDER BY `id` DESC",
		'text' => 'My Auctions'
	),
	'5' => array(
		'sql' => 'ORDER BY `finish_time` ASC',
		'text' => 'Closest to Finishing'
	)
);
$key = isset($_GET['s']) && array_key_exists($_GET['s'], $extraSqlArr) ? (int) $_GET['s'] : 1 ;
$extraSql = $extraSqlArr[ $key ]['sql'];

$links = array();
foreach ($extraSqlArr as $k => $a) {
	$links[] = $key == $k ? $a['text'] : '<a href="?s='.$k.'">'.$a['text'].'</a>' ;
}

$query = mysql_query("SELECT * FROM `auction_pokemon` {$extraSql}");

echo '
	<img src="images/auction.png" /><br /><br />
	<a href="auction_history.php">View Auction History</a><br /><br />
	'.implode(' &bull; ', $links).'<br /><br />
	<table class="pretty-table">
		<tr>
			<th>Pokemon</th>
			<th>Level</th>
			<th>Owner</th>
			<th>Time Left</th>
			<th>Current Bid</th>
		</tr>
';
while ($auctionRow = mysql_fetch_assoc($query)) {
	$secondsLeft = $auctionRow['finish_time']-time();
	$amountArray = secsToTimeAmountArray($secondsLeft);
	echo '
		<tr>
			<td>
				<img src="images/pokemon/'.$auctionRow['name'].'.png" /><br />
				'.$auctionRow['name'].'
			</td>
			<td>
				<span title="Exp: '.number_format($auctionRow['exp']).'">
					&nbsp;'.number_format($auctionRow['level']).'&nbsp;
				</span>
			</td>
			<td>
				<a href="profile.php?id='.$auctionRow['owner_id'].'">'.cleanHtml($auctionRow['owner_username']).'</a>
			</td>
			<td>
				<span title="'. implode(', ', $amountArray) .'">
					'.$amountArray[0].'
				</span>
			</td>
			<td>
	';
	if ($auctionRow['bidder_id'] == 0) {
		echo 'No Bids!<br />';
	} else {
		echo '$'.number_format($auctionRow['current_bid']).' 
		by <a href="profile.php?id='.$auctionRow['bidder_id'].'">'.cleanHtml($auctionRow['bidder_username']).'</a><br />';
	}
	
	$time = time();
	if ($auctionRow['finish_time'] < $time) {
		echo '<br />Auction has finished!<br />';
	} else {
		
		echo '
			<br /><br />
			<form action="" method="post">
				<input type="hidden" name="pid" value="'.$auctionRow['id'].'" />
				Your Bid: <input type="text" name="bid" value="'.($auctionRow['current_bid']+1).'" size="5" /> <input type="submit" value="Bid" />
			</form>
		';
	}
	echo '
			</td>
		</tr>
	';
}
echo '
	</table>
';


include '_footer.php';
?>