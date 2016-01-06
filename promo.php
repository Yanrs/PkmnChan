<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}
$uid = (int) $_SESSION['userid'];

include '_header.php';
printHeader('Promotional Pokemon');

$query      = mysql_query("SELECT `got_promo`, `money`, `token` FROM `users` WHERE `id`='{$uid}'");
$uRow       = mysql_fetch_assoc($query);
$gotPromo   = $uRow['got_promo'];
$userMoney  = $uRow['money'];
$userTokens = $uRow['token'];

$pokeName       = getConfigValue('promo_pokemon_name');
$pokeLevel      = getConfigValue('promo_pokemon_level');
$pokeCostMoney  = getConfigValue('promo_cost_money');
$pokeCostTokens = getConfigValue('promo_cost_tokens');
$message = '';

if (isset($_POST['claim']) && $gotPromo == 0) {
    
    if ($userMoney < $pokeCostMoney || $userTokens < $pokeCostTokens) {
        $message = '
        	<div class="error" style="color: #000;">
    			You can not afford this pokemon!
    		</div>
    	';
    } else {
    	$message = '
    		<div class="notice" style="color: #000;">
    			You have received a '.$pokeName.'!
    		</div>
    	';
        
        $userMoney = $userMoney - $pokeCostMoney;
        updateUserMoney($uid, $userMoney);
        
        $userTokens = $userTokens - $pokeCostTokens;
        updateUserToken($uid, $userTokens);
        
    	$exp = levelToExp($pokeLevel);
    	giveUserPokemon($uid, $pokeName, $pokeLevel, $exp, 'Scratch', 'Scratch', 'Scratch', 'Scratch');
    	
    	mysql_query("UPDATE `users` SET `got_promo`='1' WHERE `id`='{$uid}'");
    }
}

echo '
	<div style="text-align: center; margin: 30px 0px;">
		
        You have: $'.number_format($userMoney).' and '.$userTokens.' tokens!
        <br /><br /><br />
		<img src="images/pokemon/'.$pokeName.'.png" alt="'.$pokeName.'" /><br />
		The current promo pokemon is '.$pokeName.'!<br />
';

if ($pokeCostMoney != 0 || $pokeCostTokens != 0) {
	$cost = '';
	if ($pokeCostMoney > 0) {
		$cost = '$'.number_format($pokeCostMoney);
	}
	if ($pokeCostTokens > 0) {
		if ($cost != '') { $cost .= ' and '; }
		$cost .= number_format($pokeCostTokens).' tokens.';
	}
	
	
	echo 'To claim this pokemon you must pay: '.$cost.'<br />';
} else {
	echo 'This pokemon is absolutely free!<br />';
}

echo '<br /><br /><br />';

if (!empty($message) && isset($_POST['claim'])) {
    echo $message;
} else {
	if ($gotPromo == 0) {
		echo '
			<form action="" method="post">
				<input type="submit" id="button" name="claim" value="Claim This Promo!" />
			</form>
		';
	} else {
		echo 'You have already claimed this promo!';
	}
}

echo '</div>';

include '_footer.php';
?>