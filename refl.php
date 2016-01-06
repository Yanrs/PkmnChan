<?php
include 'functions.php';
Include 'config.php';
Include '_header.php';
printHeader('Referral Centre');

$uid = $_SESSION['userid'];
$user = mysql_fetch_array(mysql_query("SELECT * FROM `users` WHERE `id` = {$uid}"));
 ?>


<table align="center" class="pretty-table">
	<tr>
		<th>
Here you can refer your friends to Pokemon Helios RPG and earn Referral Points, 
the more referral points you get the more Pokemon, items or other prizes you can buy from the 
referral shop.
		</th>
	</tr>	
		
	<tr>
		<td>
			Your referral link:
		</td>
	</tr>	
	
	<tr>
		<td>
			http://pkmnhelios.net/register.php?ref=<?=$uid?>
		</td>
	</tr>
</table>
<br />
<br />
You have: <?=$user['Referals']?> referral points.
<br />
<br />
<?
$refPoints = $user['Referals'];

$query = mysql_query("SELECT * FROM `shop_ref` ORDER BY `price` ASC");

if (mysql_num_rows($query) == 0) {
    echo '<div class="error">The shop has no pokemon!</div>';
	die();
}

$salePokemon = array();
$categorys = array();
$defaultCat = '';

while ($row = mysql_fetch_assoc($query)) {
    if (empty($defaultCat)) { $defaultCat = strtolower($row['category']); }
    if (!in_array($row['category'], $categorys)) { $categorys[] = $row['category']; }
    $salePokemon[ strtolower($row['category']) ][$row['name']] = $row['price'];
}

if (isset($_GET['cat']) && in_array(strtolower($_GET['cat']), array_keys($salePokemon))) {
    $salePokemon = $salePokemon[strtolower($_GET['cat'])];
} else {
    $salePokemon = $salePokemon[$defaultCat];
}

if (isset($_POST['submit'])) {

	$pokeName = $_POST['submit'];
	if (in_array($pokeName, array_keys($salePokemon))) {
	
		$price = $salePokemon[$pokeName];
		if ($price > $refPoints) {
			echo '<div class="error">You do not have enough referrals!</div>';
		} else {
			$refPoints -= $price;
			mysql_query("UPDATE `users` SET `Referals` = '{$refPoints}' WHERE `id` = '{$uid}'");
			giveUserPokemon($uid, $pokeName, 5, levelToExp(5), 'Tackle', 'Scratch', 'Ember', 'Leer');
            
			echo '
				<div class="notice">
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
		<input type="radio" name="submit" value="'.$name.'" />
		'.$name.'<br />
		'.number_format($price).' Points<br />
	';
}
?>
	<form action="" method="post">
		<table class="pretty-table">
			<?=cellsToRows($cells, 5)?>
			<tr>
				<td colspan="5"><input type="submit" value="Exchange Points For Pokemon"></td>
			</tr>
		</table>
	</form>

<? include('_footer.php'); ?>