<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

$uid = (int) $_SESSION['userid'];
$userTokens = getUserToken($uid);

$query = mysql_query("SELECT * FROM `token_shop_pokemon` ORDER BY `price` ASC");

if (mysql_num_rows($query) == 0) {
    include '_header.php';
    echo '
        <div class="error">The token shop has no pokemon!</div>
    ';
    include '_footer.php';
}

$salePokemon = array();

while ($row = mysql_fetch_assoc($query)) {
    $salePokemon[ $row['name'] ] = $row['price'];
}

include '_header.php';
printHeader('Token Shop');

if (isset($_POST['buyPoke'])) {

	$pokeName = $_POST['buyPoke'];
	if (in_array($pokeName, array_keys($salePokemon))) {
	
		$price = $salePokemon[$pokeName];
		if ($price > $userTokens) {
			echo '<div class="error">You do not have enough tokens!</div>';
		} else {
			$userTokens -= $price;
			updateUserToken($uid, $userTokens);
			giveUserPokemon($uid, $pokeName, 50, levelToExp(50), 'Scratch', 'Scratch', 'Scratch', 'Scratch');
            
			echo '
				<div class="notice" style="color: #000000;">
					<img src="images/pokemon/'.$pokeName.'.png" /><br />
					You bought a '.$pokeName.'.
				</div>
			';
		}
	} else {
		echo '<div class="error">This pokemon is not for sale!</div>';
	}
}

$cells = array();
foreach ($salePokemon as $name => $price) {
	$cells[] = '
		<img src="images/pokemon/'.$name.'.png" /><br />
		<input type="radio" name="buyPoke" value="'.$name.'" />
		'.$name.'<br />
		'.number_format($price).' tokens<br />
	';
}

$items = array(
	'rare_candy' => array(
		'name' => 'Rare Candy',
		'price' => 1
	)
);
$query = mysql_query("SELECT * FROM `user_items` WHERE `uid`='{$uid}'");
$itemAmounts = mysql_fetch_assoc($query);

if (isset($_POST['buy_items'])) {
	
	$totalCost = 0;
	$updateSqlArray = array();
	$newItemAmounts = $itemAmounts;
	$totalItems = 0;
	
	foreach ($_POST as $item => $amount) {
		$amount = (int) $amount;
		$amount = $amount < 1 ? 0 : $amount;
		
		if (array_key_exists($item, $items) && $amount > 0) { 
			$totalCost += $amount * $items[$item]['price'];
			$updateSqlArray[] = "`$item`=`$item`+$amount";
			$newItemAmounts[$item] += $amount;
			$totalItems += $amount;
		}
	}
	
	if ($totalItems == 0) {
		echo '<div class="error">You did not select any items.</div>';
	} elseif ($totalCost > $userTokens) {
		echo '<div class="error">You do not have enough tokens.</div>';
	} else {
		echo'<div class="success">Transaction Successful!</div>';
		
		$updateSql = implode(', ', $updateSqlArray);
		mysql_query("UPDATE `user_items` SET {$updateSql} WHERE `uid`='{$uid}'");
		mysql_query("UPDATE `users` SET `token`=`token`-$totalCost WHERE `id`='{$uid}'");
		$userTokens -= $totalCost;
		$itemAmounts = $newItemAmounts;
	}
}

echo ' 

	<div style="text-align: center; margin: 30px auto; ">
		You have '.number_format($userTokens).' tokens.<br />
        <a href="donate.php">Need more tokens? Click here!</a>
	</div>
	<form action="" method="post">
		<table class="pretty-table">
			'.cellsToRows($cells, 5).'
			<tr>
				<td colspan="5"><input type="submit" value="Buy Pokemon"></td>
			</tr>
		</table>
	</form>
	<br />
	<form action="" method="post">
		<table class="pretty-table">
			<tr>
				<th>&nbsp;</th>
				<th>Name</th>
				<th>You Have</thd>
				<th>Amount to buy</th>
			</tr>
';
foreach ($items as $cname => $item) {
	echo '
		<tr>
			<td><img src="images/items/'.$item['name'].'.png" title="'.$item['info'].'" align="middle"/></td>
			<td>'.$item['name'].'</td>
			<td>'.number_format($itemAmounts[$cname]).'</td>
			<td style="text-align: left; padding-left: 10px;">
				<select name="'.$cname.'">
					<option value="0">0</option>
					<option value="1">1</option>
					<option value="5">5</option>
					<option value="10">10</option>
					<option value="25">25</option>
					<option value="50">50</option>
					<option value="100">100</option>
                                        <option value="500">500</option>
                                        <option value="1000">1000</option>
				</select>&nbsp;('.number_format($item['price']).'&nbsp;token&nbsp;each)
			</td>
		</tr>
	';
}
echo '
			<tr>
				<td colspan="5"><input type="submit" name="buy_items" value="Buy Items"></td>
			</tr>
		</table>
	</form>
';
// WTF is this?
// include 'tshop.php';
echo '</div>';

include '_footer.php';
?>