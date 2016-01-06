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

if ($totalUnreadMessages > 0) {
	$totalUnreadMessages = '<font style="color: #e74c3c;">'.$totalUnreadMessages.'</font>';
}
if ($totalOffers > 0) {
	$totalOffers = '<font style="color: #e74c3c;">'.$totalOffers.'</font>';
}
if ($newSales > 0) {
	$newSales = '<font style="color: #e74c3c;">'.$newSales.'</font>';
}
?>

<li class="usr-inf-title"><a href="#">Notifications</a></li>
<li>
	<div class="info-box">
		<a href="/messages.php?p=inbox">Messages Total: <?=$totalMessages?></a>
		<a href="/messages.php?p=inbox">Messages New: <?=$totalUnreadMessages?></a>
		<a href="/sell_pokemon.php?p=all">Sales Total: <?=$totalSalePoke?></a>  	
		<a href="/sell_pokemon.php?p=history">Newly Sold: <?=$newSales?></a>
		<a href="/trade.php?a=vao">Trade Offers: <?=$totalOffers?></a>
	</div>
</li>

</ul>

<?php
if($_SESSION['userid']) {	
?>

	<ul class="usr-inf left">
		<li class="usr-inf-title"><a href="#">Account</a></li>	
		<li>			
			<div class="user-info">
				<p>ID: <?=$userRow['id']?></p>
				<p><?=$username?></p>
				<p>$<?=number_format($money)?></p>
				<p><?=number_format($tokens)?> Tokens</p>
				<p>LEVEL: <?=expToLevel($userRow['trainer_exp'])?></p>
			</div>
		</li>
		
		<li class="usr-inf-title"><a href="#">Short cuts</a></li>
		<li>
			<div class="info-box">
				<a href="/auction.php?lref=1">Auctions</a>
				<a href="/view_box.php?lref=3">My PokeBox</a>
				<a href="/trade.php?lref=4">Trade Center</a>
				<a href="/sell_pokemon.php?lref=5">Global Buy/Sell</a>
				<a href="/fix.php?lref=6">Battle Training</a>
				<a href="/chatroom.php?lref=7">ChatRoom</a>
				<a href="http://forums.pkmnhelios.net/">Forums</a>
				<a href="/online.php?lref=9">Online Members</a>
				<a href="/users.php?lref=10">Member List</a>
			</div>		
		</li>
	</ul>
<?php } ?>