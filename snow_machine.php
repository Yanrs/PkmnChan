<?php
include 'config.php'; 
include 'functions.php'; 


if (!isLoggedIn()) {
    redirect('login.php');
}

include '_header.php';
printHeader('Snow Machine');

$uid = (int) $_SESSION['userid'];
$userMoney = getUserMoney($uid);
$message = '';

$attemptPrice   = getConfigValue('snow_machine_price');
$trappedPokemon = getConfigValue('snow_machine_pokemon');
$chanceOfWin    = getConfigValue('snow_machine_chance');
$trappedLevel   = getConfigValue('snow_machine_pokemon_level');
$spentMoney   	= getConfigValue('snow_machine_lost_money');

echo '<div style="text-align: center;">
Total spent money: '.number_format($spentMoney).'<br />';

if (isset($_POST['fix'])) {
    
    if ($userMoney < $attemptPrice) {
        $message = 'I am sorry but you do not have enough money.';
    } else {
        // take money
        $userMoney -= $attemptPrice;
        updateUserMoney($uid, $userMoney);
        
        if (rand(1, 100) <= $chanceOfWin) {
            // they won
            
            $message = '
                You have rescued a '.$trappedPokemon.'!<br />
                <img src="images/pokemon/'.$trappedPokemon.'.png" alt="'.$trappedPokemon.'" />
            ';
            
            // give them the pokemon
            $exp = levelToExp($trappedLevel);
            giveUserPokemon($uid, $trappedPokemon, $trappedLevel, $exp, 'Scratch', 'Scratch', 'Scratch', 'Scratch');
            
        } else {
            // they lost
            
            $message = '
                Unlucky, you have failed to rescue the pokemon!
            ';
			mysql_query("UPDATE `config` SET `value` = `value`+'{$attemptPrice}' WHERE `name` = 'snow_machine_lost_money'");
        }
    }
    
    echo '
        <div style="font-size: 15px;">
            '.$message.'<br /><br />
            <a href="snow_machine.php">Go back and try again?</a><br /><br />
        </div>
    ';
    
} else {

    echo '

        <img src="/images/pokemon/'.$trappedPokemon.'.png"/><br /><br />
        
        Hello, Welcome to the Snow Machine!<br />
        
        A <b style="color: #008FFF;">'.$trappedPokemon.'</b> has been trapped in the machine! <br />
        
        You can attempt to fix the machine and rescue '.$trappedPokemon.' for $' . number_format($attemptPrice) . '.<br />
        
        The chance of rescuing '.$trappedPokemon.' and fixing the machine is '.$chanceOfWin.'%.<br /><br />
        
        
    
        <form action="" method="post">
            <input style="padding: 3px 20px;" type="submit" name="fix" value="Try to fix the Snow Machine" id="button" />
        </form>
        <br /><br />
    ';
}

if (isAdmin()) {
    echo '
        [<a href="/staff/edit_snow_machine.php">Edit Snow Machine</a>]
        <br /><br />
    ';
}

echo '</div>';


include '_footer.php';
?>







