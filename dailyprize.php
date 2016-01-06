<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}
include '_header.php';

printHeader('Daily Prize');

$message =  '';

$uid = $_SESSION['userid'];
$user = mysql_fetch_object(mysql_query("SELECT * FROM `users` WHERE `id` = '".$uid."'"));

$multi1 = mysql_query("SELECT * FROM `users` WHERE `ip2`='" . $user->ip2 . "' AND `dailyprize`='0'");
$multi  = mysql_num_rows($multi1);

$time = time();
$timeLeft = $user->last_dailyprize + 86400;
$next_dailyprize  = secondsToTimeSince($timeLeft - $time);

if ($timeLeft <= $time) {
	mysql_query("UPDATE `users` SET `dailyprize` = '0' WHERE `id`='$user->id'");
}

if(isset($_POST['submit'])) {

	if ($user->dailyprize >= 1 || $multi > 2) {
		if($multi < 3) {
			$message = "<p class='error'>You've had your daily prize today.<br>
					Till next daily prize you have left $next_dailyprize <br>
					Come back tomorrow.</p>";
		}

		if($multi > 2) {
			$message = "<p class='error'>Too many people with your IP address have already took the daily prize today.</p>";
		}
	} else {
		
		mysql_query("UPDATE `users` SET `dailyprize` = '0' WHERE `id`='$user->id'");

		$rand = rand(1,2);
		$rand2 = rand(1,100);

		if($rand2 == 50){
			$amountone = rand(1,3);
			$new_tokens = $user->token + $amountone;
			echo "<p class='success'>Your daily prize is: <b>".number_format($amountone)." Token(s)</b><br>You are one lucky player.</p>";
			$result = mysql_query("UPDATE `users` SET `token` = '".$new_tokens."' WHERE `id`='$user->id'");
		}

		if($rand == 1){
			$amountone = rand(1024,124000);
			$new_trainer_exp = $user->trainer_exp + $amountone;
			$message = "<p class='success'>Your daily prize is: <b>".number_format($amountone)." Trainer Experience Points</b>!</p>";
			$result = mysql_query("UPDATE `users` SET `trainer_exp` = '".$new_trainer_exp."' WHERE `id`='$user->id'");
		}

		if($rand == 2){
			$amountone = rand(1000,20000);
			$new_money = $user->money + $amountone;
			$message = "<p class='success'>Your daily prize is: <b>$".number_format($amountone)." Game Money</b>!</p>";
			$result = mysql_query("UPDATE `users` SET `money` = '".$new_money."' WHERE `id`='$user->id'");
		}		
		

		
		mysql_query("UPDATE `users` SET `dailyprize` = '1', `last_dailyprize`='$time' WHERE `id`='$user->id'");
	}
}
?>

<center>
<?=$message?>
<br>
<br>
<img src="images/pokemon/oak.png"><br><br>
Hello trainers, here you can claim a prize for just being active!.<br>
You can claim once every 24 hours! and you can win przies such as money, tokens and experience<br>
<form method="post" action="dailyprize.php">
	<input type="submit" name="submit" value="Get a Daily Prize" class="button">
</form>

</center>


<?php
	include '_footer.php';
?>