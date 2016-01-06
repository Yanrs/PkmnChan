<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}
include '_header.php';

printHeader('Lucky Hour');

function howlongtila($ts) {
   $ts=$ts - time();
       return floor($ts/60)." minutes";

};

$uid = $_SESSION['userid'];
$user = mysql_fetch_object(mysql_query("SELECT * FROM `users` WHERE `id` = '$uid'"));

$token = ($user->signup_date * 3);

$hitdown = getConfigValue('lucky_hour');

$hitrows1 = mysql_query("SELECT * FROM `lucky_hour`");
$hitrows = mysql_num_rows($hitrows1);

$pid = rand(1, 713);
$pokemon = mysql_fetch_object(mysql_query("SELECT * FROM `pokemon` WHERE `id` = '$pid'"));
$level = 5;
$exp = levelToExp($level);

if($hitrows == 0){
	$newpokemon = giveUserPokemon($uid, $pokemon->name, $level, $exp, $pokemon->move1, $pokemon->move2, $pokemon->move3, $pokemon->move4);
	$newgold = 10000;
}

$timeleft = howlongtila($hitdown);

$ts = $hitdown - time();
$secondz = $ts % 60;

function Message($text) {
	return '<p class="error">'.$text.'</p>';
}

if($_GET['lucky'] == 1) {
	if($_GET['token'] != $token){
		echo Message("Invalid token.");
		$error = 1;
	}

	if($user->lucky_hour == 1){
		echo Message("You've already had a hit this lucky hour.");
		$error = 1;
	}
	
	if($hitrows > 0){
		echo Message("Someone have already won in this lucky hour.");
		$error = 1;
	}
	
	if($error != 1){
		$result = mysql_query("INSERT INTO `lucky_hour` (winner, pokemon)"."VALUES ('$uid', '$pokemon->name')");
		$user->money = $user->money + $newgold;
		$givehit = mysql_query("UPDATE `users` SET `money` = '$user->money' WHERE `id`='$uid'");
		echo Message("
			You got a lucky!<br> 
			You've won $".number_format($newgold)."!<br> 
			AND <br> 
			<b>".$pokemon->name."</b><br>
			<b>Level: ".$level."</b><br>
			<img src='/images/pokemon/".$pokemon->name.".png'>
		");
	}
	
	$givehit = mysql_query("UPDATE `users` SET `lucky_hour` = '1' WHERE `id`='$uid'");
}

if($timeleft < 1 && $secondz < 1){
	$newtime = 3600 + time();
	$resethit = setConfigValue('lucky_hour', $newtime);
	$timeleft = howlongtila($newtime);
	$givehit = mysql_query("UPDATE `users` SET `lucky_hour` = '0'");
	$resethitzz = mysql_query("DELETE FROM `lucky_hour`");
	$secondz = 0;
}

?>

<br>

<center>
	<b>Prize: </b>$10,000 & Random Pokemon<br>

	The rules are simple, first people who hit button "Get Lucky!" in each lucky hour wins.<br>
	Lucky hour will reset itself every 60 minutes.<br>
	Time till next lucky hour: <?php echo $timeleft; ?> <?php echo $secondz; ?> seconds.
	<br><br>
	<button class="button" onclick=window.location='luckyhour.php?lucky=1&token=<?php echo $token; ?>'>Get Lucky!</button>
	<br><br>
	Last Lucky Hour Winner: <br>
	<?php
		$rofl = mysql_fetch_object(mysql_query("SELECT * FROM `lucky_hour` ORDER BY ABS(id) ASC LIMIT 1"));
		$pic = mysql_fetch_object(mysql_query("SELECT * FROM `users` WHERE `id`='" . $rofl->winner . "'"));
		
		if($rofl->id) {
			echo "<a href='/profile.php?id=".$pic->id."'>".$pic->username."</a><br>";
			echo "Won: $10,000 and $rofl->pokemon <br>";
			echo '<img src="/images/pokemon/'.$rofl->pokemon.'.png" />';
		} else {
			echo 'NO ONE!';
		}
	?>
</center>

<?php include '_footer.php'; ?>