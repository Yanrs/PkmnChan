<?
include 'config.php';
include '_header.php';
include 'functions.php';

?>
<?

$test = off;
$uid = (int) $_SESSION['userid'];
$userCredits = getUserCredits($uid);

switch ($_GET['type']) {
	case 'shiny':
		$type = 'Shiny ';
	break;
	
	default:
		$type = '';
	break;
}

$defaultPrice = 100;
$salePokemon = array(
	'Arceus (Bug)'      => $defaultPrice,
	'Arceus (Dark)'     => $defaultPrice,
	'Arceus (Dragon)'   => $defaultPrice,
	'Arceus (Electric)' => $defaultPrice,
	'Arceus (Fighting)' => $defaultPrice,
	'Arceus (Fire)'     => $defaultPrice,
	'Arceus (Flying)'   => $defaultPrice,
	'Arceus (Ghost)'    => $defaultPrice,
	'Arceus (Grass)'    => $defaultPrice,
	'Arceus (Ground)'   => $defaultPrice,
	'Arceus (Ice)'      => $defaultPrice,
	'Arceus (Poison)'   => $defaultPrice,
	'Arceus (Psychic)'  => $defaultPrice,
	'Arceus (Rock)'     => $defaultPrice,
	'Arceus (Steel)'    => $defaultPrice,
	'Arceus (Unknown)'  => $defaultPrice,
	'Arceus (Water)'    => $defaultPrice,
	'Deoxys (Attack)'   => $defaultPrice,
	'Deoxys (Defence)'  => $defaultPrice,
	'Deoxys (Speed)'    => $defaultPrice,
	'Rotom (Cut)'       => $defaultPrice,
	'Rotom (Frost)'     => $defaultPrice,
	'Rotom (Heat)'      => $defaultPrice,
	'Rotom (Spin)'      => $defaultPrice,
	'Rotom (Wash)'      => $defaultPrice
);



if (isset($_POST['buyPoke'])) {

	$pokeName = $_POST['buyPoke'];
	if (in_array($pokeName, array_keys($salePokemon))) {
	
		$price = $salePokemon[$pokeName];
		if ($price > $userCredits) {
			echo '<div class="errorMsg">You do now have enough money!</div>';
		} else {
			$t = $userCredits -= $price;
			giveUserPokemonByName($uid, $pokeName, 100, $type);
			updateUserCredits($uid, $t);
			
			echo '
				<div class="actionMsg">
					<img src="images/pokemon/'.$type.$pokeName.'.png" /><br />
					You bought a '.$type.$pokeName.'.
				</div>
			';
		}
	} else {
		echo '<div class="errorMsg">This pokemon is not for sale!</div>';
	}
}

$cells = array();
foreach ($salePokemon as $name => $price) {
	$cells[] = '
		<img src="images/pokemon/'.$type.$name.'.png" /><br />
		<input type="radio" name="buyPoke" value="'.$name.'" />
		'.$type.$name.'<br />
		'.number_format($price).'&nbspCredits<br />
	';
}


echo '
	<div style="text-align: center; margin: 30px auto; ">
		You have '.number_format($userCredits).' Credits<br /><br />
		
		<a href="pokeshop.php">Normal</a> &bull;
		<a href="pokeshop.php?type=shiny">Shiny</a>
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
?>
