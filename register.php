<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'recaptchalib.php';

$privatekey = "6LcoouYSAAAAAPqMC4MyP8wRieWRNvfGoJw7-LdJ";
$publickey = "6LcoouYSAAAAAGdxiM-0G2jv8BHKCOFqArqz0gwQ";

if (isLoggedIn()) { redirect('membersarea.php'); }

define('DEFAULT_STARTER_LEVEL', 15);
define('DEFAULT_STARTER_COLUMNS', 4);
define('DEFAULT_USER_MONEY', 20000);

$pokemonNames = array(
	'Bulbasaur' , 'Charmander' , 'Squirtle' , 'Chikorita' ,
	'Cyndaquil' , 'Totodile'   , 'Treecko'  , 'Torchic'   ,
	'Mudkip'    , 'Turtwig'    , 'Chimchar' , 'Piplup'    ,
	'Snivy'     , 'Tepig'      , 'Oshawott' , 'Eevee'
);

$username  = isset( $_POST['username']  ) ? $_POST['username']  : '' ;
$email     = isset( $_POST['email']     ) ? $_POST['email']     : '' ;
$password  = isset( $_POST['password']  ) ? $_POST['password']  : '' ;
$password2 = isset( $_POST['password2'] ) ? $_POST['password2'] : '' ;
$pokemon   = isset( $_POST['pokemon']   ) ? $_POST['pokemon']   : '' ;

$username = trim($username);
$errorMessage = '';

if(count($_POST) > 0) {

	$sqlUsername = cleanSql( trim($username) );
	$sqlPassword = sha1($password);
	$sqlEmail    = cleanSql($email);
	$sqlPokemon  = cleanSql($pokemon);
	$time        = time();
	$errors      = array();
	

	
	if( $username == '' ) {
		$errors[] = $lang['register_empty_username'];
	}
	
	if( strlen($username) < 5){
		$errors[] =  $lang['register_contain_username'];
	}
	
	if( strlen($username) > 40){
		$errors[] = $lang['register_contain_username_max'];
	}

	if( !in_array($pokemon, $pokemonNames) ) {
		$errors[] =  $lang['register_not_starter'];
	}
	
	if($password != $password2) {
		$errors[] =  $lang['register_match_pwd'];
	}
	
	if(strlen($password) <= 6) {
		$errors[] =  $lang['register_contain_pwd'];
	}
	
	if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		$errors[] =  $lang['register_valid_email'];
	}
	
	if (isset($_POST["recaptcha_challenge_field"]) && isset($_POST["recaptcha_response_field"])) {
	    $resp = recaptcha_check_answer(
	        $privatekey,
	        $_SERVER["REMOTE_ADDR"],
	        $_POST["recaptcha_challenge_field"],
	        $_POST["recaptcha_response_field"]
	     );
			
	    if(!$resp->is_valid) {
			$errors[] = $lang['register_captcha_wrong'];
	    }
	} else {
		$errors[] = $lang['register_captcha_missing'];
	}
	
	$query = mysql_query("SELECT `id` FROM `users` WHERE `username`='{$sqlUsername}' LIMIT 3");
	if(mysql_num_rows($query) == 3) {
		$errors[] =  $lang['register_taken_username'];
	}
	
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
	    $ip = cleanSql($_SERVER['HTTP_X_FORWARDED_FOR']);
	} else {
	    $ip = cleanSql($_SERVER['REMOTE_ADDR']);
	}
	
	if (isset($_GET['ref'])) {
		$refId = (int) $_GET['ref']; 
		$query = mysql_query("SELECT * FROM `users` WHERE `id`='{$refId}'");
		$refRow = mysql_fetch_assoc($query);
		if ($refRow['ip'] == $ip) {
			$errors[] = $lang['register_match_ip'];
		}
	}
	
	$oneDayAgo = time() - (60*60*24);
	$query = mysql_query("SELECT `id` FROM `users` WHERE `ip`='{$ip}' AND `signup_date`>='{$oneDayAgo}' LIMIT 1");
	if(mysql_num_rows($query) == 1) {
		$errors[] = $lang['register_already_ip'];
	}
	

   /* if(mysql_num_rows(mysql_query("SELECT id FROM users WHERE ip = '{$ip}'")) != 0) {  
		include '_header.php'; 
		echo '<div class="ip-error">Sorry, but there\'s already an account registered under this IP. <br /> 
				If you wish to reclaim this account or delete it, please contact one of our administrators.</div>';
		die();  
	} */
	
	
	if (count($errors) > 0) {
		$errorMessage = '<p style="top: 0; margin: 2px 0;" class="error">'.implode('</p><p style="top: 0; margin: 2px 0;" class="error">', $errors).'</p>';
	} else {
		
		// make them a user account
		$money = DEFAULT_USER_MONEY;
		$refId = isset($_GET['ref']) ? (int) $_GET['ref'] : 0 ;
		mysql_query("
			INSERT INTO `users` (
				`username`, `password`, `email`,`signup_date`, `money`, `ip`, `register_ip`, `ref_id`
			) VALUES (
				'{$sqlUsername}', '{$sqlPassword}', '{$sqlEmail}', '{$time}', '{$money}', '{$ip}', '{$ip}', '$refId')
		");
		$uid = mysql_insert_id();
		
		
		$pokeQuery  = mysql_query("SELECT * FROM `pokemon` WHERE `name`='{$pokemon}'");
		$pokemonRow = mysql_fetch_assoc($pokeQuery);
		$level = DEFAULT_STARTER_LEVEL;
		$exp   = levelToExp($level);
		
		// give them a pokemon
		$query = mysql_query("
			INSERT INTO `user_pokemon` (
				`uid`, `name`, `level`, `exp`, `move1`, `move2`, `move3`, `move4`
			) VALUES (
				'{$uid}', '{$pokemon}', '{$level}', '{$exp}', '{$pokemonRow['move1']}', '{$pokemonRow['move2']}', '{$pokemonRow['move3']}', '{$pokemonRow['move4']}'
			)
		");
		$pid = mysql_insert_id();
		
		// put the pokemon in the first slot
		mysql_query("UPDATE `users` SET `poke1`='{$pid}' WHERE `id`='{$uid}'");

		//give them some items
		mysql_query("
			INSERT INTO `user_items` (
				`uid`, `poke_ball`, `great_ball`, `ultra_ball`, `master_ball`, 
				`potion`, `super_potion`, `hyper_potion`, `burn_heal`, `full_heal`, 
				`parlyz_heal`, `antidote`, `awakening`, `ice_heal`, `dawn_stone`, 
				`dusk_stone`, `fire_stone`, `leaf_stone`, `moon_stone`, `oval_stone`,
				`shiny_stone`, `sun_stone`, `thunder_stone`, `water_stone`
			) VALUES (
				'{$uid}', '20', '15', '10', '5', '20', '10', '5', 
				'5', '5', '5', '5', '5', '5', '5', '5', '5',
				'5', '5', '5', '5', '5', '5', '5'
			);
		");
		
		if (isset($_GET['ref'])) {
			$refId = (int) $_GET['ref']; 
			mysql_query("UPDATE `users` SET `Referals`=`Referals`+1 WHERE `id`='{$refId}'");
		}
	
		$_SESSION['register'] = '<div class="success">'.$lang['register_success'].'</div>';
		
		redirect('login.php');
	}
}

$starterCells = array();
foreach ($pokemonNames as $name) {
	$starterCells[] = '
		<label>
		<img src="images/pokemon/'.$name.'.png" alt="'.$name.'" /><br /><br />
		<input type="radio" name="pokemon" value="'.$name.'" />
		</label>
	';
}

include '_header.php';
?>

<div class="content">
	<div class="wrap">
		<table>
			<tr>
				<td>
					<div class="poke three"></div>
					
					<div class="register">
					
					<?=$errorMessage?>
					
						<div class="title" align="center"><?=$lang['register_title']?></div>
						
						<form action="" method="post" autocomplete="off">
							<table style=" text-align: left;">
								<tr class="block">
									<th><?=$lang['register_username']?></th>
									<td><input type="text" name="username" value="<?=htmlentities($username, ENT_QUOTES, 'UTF-8')?>"  autofocus="on" /></td>
								</tr>
								
								<tr class="block">
									<th><?=$lang['register_pwd']?></th>
									<td><input type="password" name="password" /></td>
								</tr>
								
								<tr class="block">
									<th><?=$lang['register_pwd_again']?></th>
									<td><input type="password" name="password2" /></td>
								</tr>
								
								<tr class="block">
									<th><?=$lang['register_email']?></th>
									<td><input type="text" name="email" value="<?=htmlentities($email, ENT_QUOTES, 'UTF-8')?>" /></td>
								</tr>
								
								<tr class="block">
									<th colspan="2" style="vertical-align: top;"><?=$lang['register_starter']?></th>				
								</tr>
								
								<tr class="block">
									<td colspan="2">
										<table class="table" style="text-align: center">
											<?=cellsToRows($starterCells, DEFAULT_STARTER_COLUMNS)?>
										</table>
									</td>
								</tr>
								
								<tr class="block">
									<th colspan="2"><?=$lang['register_captcha']?></th>
								</tr>
								
								<tr>
									<td colspan="2"><?php echo recaptcha_get_html($publickey); ?></td>
								</tr>	
								
								<tr class="block">
									<td colspan="2"><center><input type="submit" value="<?=$lang['register_signup']?>" name="submit" class="button"></center></td>
								</tr>
							</table>
						</form>
					</div>
					<? include '_footer.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>