<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

$uid = (int) $_SESSION['userid'];
$userMoney = getUserMoney($uid);

$query = mysql_query("SELECT * FROM `shop_pokemon` ORDER BY `price` ASC");

if (mysql_num_rows($query) == 0) {
    include '_header.php';
    echo '
        <div class="error">The shop has no pokemon!</div>
    ';
    include '_footer.php';
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

include '_header.php';
printHeader('Pokemon Shop');

if (isset($_POST['buyPoke'])) {

	$pokeName = $_POST['buyPoke'];
	if (in_array($pokeName, array_keys($salePokemon))) {
	
		$price = $salePokemon[$pokeName];
		if ($price > $userMoney) {
			echo '<div class="error">You do not have enough money!</div>';
		} else {
			$userMoney -= $price;
			updateUserMoney($uid, $userMoney);
			giveUserPokemon($uid, $pokeName, 5, levelToExp(5), 'Scratch', 'Scratch', 'Scratch', 'Scratch');
            
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
		<input type="radio" name="buyPoke" value="'.$name.'" />
		'.$name.'<br />
		$'.number_format($price).'<br />
	';
}

$linksArray = array();
foreach ($categorys as $category) {
    $category = ucfirst( strtolower($category) );
    $linksArray[] = '<a href="?cat='.$category.'">'.$category.'</a>';
}
echo '
	<div style="text-align: center; margin: 10px auto; ">
		You have $'.number_format($userMoney).'<br /><br />
		'.implode(' &bull; ', $linksArray).'
	</div>
	<form action="" method="post">
		<table class="pretty-table">
			'.cellsToRows($cells, 5).'
			<tr>
				<td colspan="5"><input type="submit" value="Buy Pokemon"></td>
			</tr>
		</table>
	</form>
';

echo '</div>';
include '_footer.php';
?>


