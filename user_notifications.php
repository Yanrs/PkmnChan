<?php


$uid = (int) $_SESSION['userid'];


// username, money, tokens etc
$query = mysql_query("SELECT * FROM `users` WHERE `id`='{$uid}' LIMIT 1");
$userRow = mysql_fetch_assoc($query);

$username            = cleanHtml($userRow['username']);
$money               = $userRow['money'];
$tokens              = $userRow['token'];
$totalMessages       = $userRow['total_messages'];
$totalUnreadMessages = $userRow['unread_messages'];
$totalSalePoke       = $userRow['total_sale_pokes'];
$newSales            = $userRow['newly_sold_pokes'];

// total messages
//$query = mysql_query("SELECT * FROM `messages` WHERE `recipient_uid`='{$uid}' AND `deleted_by_recipient`='0'");
//$totalMessages = mysql_num_rows($query);

// total unread messages
//$query = mysql_query("SELECT * FROM `messages` WHERE `recipient_uid`='{$uid}' AND `read`='0' AND `deleted_by_recipient`='0'");
//$totalUnreadMessages = mysql_num_rows($query);

// total pokemon for sale
// $query = mysql_query("SELECT * FROM `sale_pokemon` WHERE `uid`='{$uid}'");
// $totalSalePoke = mysql_num_rows($query);

// new sales
// $query = mysql_query("SELECT * FROM `sale_history` WHERE `uid`='{$uid}' AND `seen`='0'");
// $newSales = mysql_num_rows($query);

// total trade offers
$query = mysql_query("SELECT `id` FROM `trade_pokemon` WHERE `uid`='{$uid}'");
$tradeIds = array();
while ($tradeId = mysql_fetch_assoc($query)) { $tradeIds[] = $tradeId['id']; }
$tradeIdSql = '\''.implode('\', \'', $tradeIds) .'\'';

$query = mysql_query("SELECT * FROM `offer_pokemon` WHERE `tid` IN ({$tradeIdSql}) GROUP BY `oid`");
$totalOffers = mysql_num_rows($query);
?>

<div id="panel">
<table id="panel1">
	<tr>
		<td>
			<a href="/profile.php" style="color: #FFF; font-weight: bold;"><?=$username?></a><br>
			$<?=number_format($money)?><br>
			<?=number_format($tokens)?> tokens
		</td>
		
		<td>
			<a href="/messages.php?p=inbox" style="color: #FFF; font-weight: bold;">Messages</a><br>
			<a href="/messages.php?p=inbox">Total: <?=$totalMessages?></a><br>
			<a href="/messages.php?p=inbox">Unread: <?=$totalUnreadMessages?></a>
		</td>
		
		<td>	
			<a href="/sell_pokemon.php" style="color: #FFF; font-weight: bold;">Pokemon For Sale</a><br>
			<a href="/sell_pokemon.php?p=mine">Total: <?=$totalSalePoke?></a><br>
			<a href="/sell_pokemon.php?p=history">Newly Sold: <?=$newSales?></a>
		</td>
		
		<td>
			<a href="/trade.php" style="color: #FFF; font-weight: bold;">Trades</a><br>
			<a href="/trade.php?a=vao">Total Offers: <?=$totalOffers?></a>
		</td>
	<tr>
</table>
</div>