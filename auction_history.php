<?php
include 'config.php';
include 'functions.php';

function secsToRoughTime($secs) {
        $seconds = array(
                'days'    => 86400,
                'hours'   =>  3600,
                'minutes' =>    60,
                'seconds' =>     1,
        );
        
        foreach ($seconds as $name => $seconds) {
        	$amount = intval($secs / $seconds);
		if ($amount > 0) {
			return $amount.' '.($amount == 1 ? rtrim($name, 's') : $name );
		}
        }
        return '-';
}

if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php';
printHeader('Auction History');

$uid = (int) $_SESSION['userid'];

$extraSqlArr = array(
	'1' => array(
		'sql' => 'ORDER BY `finish_time` DESC',
		'text' => 'Newest Sold'
	),
	'2' => array(
		'sql' => 'ORDER BY `winning_bid` DESC',
		'text' => 'Most Expensive'
	),
	'3' => array(
		'sql' => 'ORDER BY `level` DESC',
		'text' => 'Highest Level'
	),
	'4' => array(
		'sql' => "WHERE `owner_id`='{$uid}' ORDER BY `finish_time` DESC",
		'text' => 'Just My Auctions'
	),
);
$key = isset($_GET['s']) && array_key_exists($_GET['s'], $extraSqlArr) ? (int) $_GET['s'] : 1 ;
$extraSql = $extraSqlArr[ $key ]['sql'];

$links = array();
foreach ($extraSqlArr as $k => $a) {
	$links[] = $key == $k ? $a['text'] : '<a href="?s='.$k.'">'.$a['text'].'</a>' ;
}

$query = mysql_query("SELECT * FROM `auction_history` {$extraSql} LIMIT 100");

echo '
	<img src="images/auction.png" /><br /><br />
	<a href="auction.php">View Auctions</a><br /><br />
	'.implode(' &bull; ', $links).'<br /><br />
';

if (mysql_num_rows($query) == 0) {
	echo '<div class="notice">Could not find any auction history!</div>';
	include '_footer.php';
	die();
}

echo '
	<table class="pretty-table">
		<tr>
			<th>Pokemon</th>
			<th>Level</th>
			<th>Owner</th>
			<th>Sold</th>
			<th>Sold To</th>
		</tr>
';
while ($auctionRow = mysql_fetch_assoc($query)) {
	
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
				'.secsToRoughTime(time()-$auctionRow['finish_time']).' ago
			</td>
			<td>
	';
	if ($auctionRow['winner_id'] == 0) {
		echo 'No one!<br />';
	} else {
		echo '
			<a href="profile.php?id='.$auctionRow['winner_id'].'">'.cleanHtml($auctionRow['winner_username']).'</a><br />
			for $'.number_format($auctionRow['winning_bid']).' 
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