<?php

require_once 'config.php';
require_once 'functions.php';

error_reporting(-1);

if (!isLoggedIn()) {
	redirect('index.php');
}
include '_header.php';

printHeader('50/50 Money game');

$uid = (int) $_SESSION['userid'];
$user1 = mysql_query("SELECT * FROM `users` WHERE `id`='".$_SESSION['userid']."'");
$user = mysql_fetch_object($user1);

$_POST['multiple'] = abs((int) $_POST['multiple']);
$_POST['bet_id'] = mysql_real_escape_string($_POST['bet_id']);
$_POST['amount'] = abs((int) $_POST['amount']);
$_POST['amount'] = mysql_real_escape_string($_POST['amount']);
$minimum = $_POST['amount'];

if($_POST['multiple'] != "" && $_POST['multiple'] < 1){echo Message("Invalid amount of multiple bets.");$error = 1;}
if($_POST['multiple'] != "" && $_POST['multiple'] > 5){echo Message("Invalid amount of multiple bets.");$error = 1;}

if ($_POST['takebet'] != ""){
$_POST['bet_id'] = abs((int) $_POST['bet_id']);

$result = mysql_query("SELECT * FROM `5050` WHERE `id`='".$_POST['bet_id']."'");
$worked = mysql_fetch_array($result);
$amount = $worked['money'];

if ($worked['id'] < 1){echo  "Someone took that bet before you.";$error = 1;}

if($worked['money'] > 0){
if ($worked['uid'] == $user->id) { echo "You cannot take your own bet";$error = 1;}
if ($amount > $user->money) { echo "You don not have enough money to match their bet.";$error = 1;}

	if($error != 1){
		mysql_query("DELETE FROM `5050` WHERE `id`='".$worked['id']."'");
		$winner = rand(1,2);
		$win = $worked['money'];

		if($winner == 1){
			$user->money = $user->money - $amount;
			echo 'You have lost this bet';
			mysql_query("UPDATE `users` SET `money` = `money` - $amount WHERE `id`='$user->id'");
			
			$amount = $amount * 2;
			mysql_query("UPDATE `users` SET `money` = `money` + $amount WHERE `id`='".$worked['user']."'");
		} else {
			$user->money = $user->money + $amount;
			echo 'You have won this bet';
			mysql_query("UPDATE `users` SET `money` = `money` + $amount WHERE `id`='$user->id'");
		}
	}

}
}


if ($_POST['makebet']){

$dalimit1 = mysql_query("SELECT id FROM `5050` WHERE `uid` = '$user->id'");
$dalimit = mysql_num_rows($dalimit1);

if($dalimit > 9){echo "You can only have a maximum of 10 bets at a time." ;$error = 1;}

if($_POST['type'] == 1){
$_POST['amount'] = abs((int) $_POST['amount']);
$_POST['multiple'] = abs((int) $_POST['multiple']);

$checka = $_POST['amount'];

if($_POST['multiple'] > 1){$checka = $_POST['amount'] * $_POST['multiple'];}
if($checka > $user->money){echo  "You don not have that much money.";$error = 1;}
if($minimum < 1000){echo "Each bid must be at least $1000.";$error = 1;}
if (!preg_match('~^[a-z0-9 ]+$~i', $_POST['amount'])){echo  "Invalid characters detected.";$error = 1;}
if (!preg_match('~^[a-z0-9 ]+$~i', $_POST['multiple'])){echo  "Invalid characters detected.";$error = 1;}

if($error != 1){
$i = 0;
$mbid = $_POST['multiple'];

while($i<$mbid){ 
$i++;
$result= mysql_query("INSERT INTO `5050` (uid, money)"."VALUES ('$user->id', '".$_POST['amount']."')");
}

mysql_query("UPDATE `users` SET `money` = `money` - $checka WHERE `id`='$user->id'");
//".$_POST['multiple']." bets of
if($checka > 1){
echo  "You have added $".number_format($_POST['amount'])."";	
}else{
echo  "You have added $".number_format($_POST['amount'])."";	
}
}

}
}

if($_POST['remove'] != "" && $ivedisabledremovefornow == 1){
$_POST['bet_id'] = abs((int) $_POST['bet_id']);
$_POST['bet_id'] = mysql_real_escape_string($_POST['bet_id']);

$result = mysql_query("SELECT * FROM `5050` WHERE `id`='".$_POST['bet_id']."' AND `uid` = '$user->id'");
$worked = mysql_fetch_object($result);

if($worked->user != $user->id){
echo  "Bet already taken/invalid.";
$error = 1;
}

if($error != 1){
$newgold = $user->money + $worked->money;
$result = mysql_query("UPDATE `users` SET `money` = '".$newgold."' WHERE `id`='$user->id'");
$result = mysql_query("DELETE FROM `5050` WHERE `id`='".$worked->id."'");
echo "You have removed your bet.";
}
}
?>

<div class="help">
	<p>Instructions</p>
	Min bid starts from: $1000<br>
	Let win the best.
</div>

<table class="pretty-table">
<form method='post' onsubmit="return addbet();">
	<tr>
		<th>Amount of money:</th>
		<td colspan="2">
			<input type='text' class='textarea' name='amount' size='50' maxlength='15' placeholder="Enter number of money you want">
		</td>
	</tr>

	<tr style="display:none;">
		<th>Type:</th>
		<td>
			<select name="type" onchange="checko(this.value)"> 
				<option value="1">Money</option>
			<select>
		</td>
		<td></td>
	</tr>

	<tr style="display: none;">
		<th>Multiple Bets:</th>
		<td>
			<select name="multiple"> 
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				</select>
		</td>
		<td></td>
	</tr>

	<tr id="theform">
		<th colspan="3">
			<input type='submit' class='button' name='makebet' value='Make Bet'>

		</th>
	</tr>
	
</form>

	<tr>
		<th>From</th>
		<th>Amount</th>
		<th>Bet?</th>
	</tr>
<?php
	$result = mysql_query("SELECT * FROM `5050` WHERE `money` != 0  ORDER BY `money` DESC");
	while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$other1 = mysql_query("SELECT id, username from `users` WHERE `id`='".$line['uid']."' LIMIT 0,1");
		$other = mysql_fetch_object($other1);

		$value = "takebet";
		$value2 = "Take Bet";

		if($line['user'] == $user->id){
			$value = "takebet";
			$value2 = "Take Bet";
		}

		$moneyon = number_format($line['money']);
		echo "<form method='post'>";
		echo "<tr>
				<td align=center>
					<a href='profile.php?id=".$other->id."'>".$other->username."</a>
				</td>
				
				<td align=center>
					$".$moneyon." dollars
				</td>
				
				<td align=center>
					<input type='hidden' name='bet_id' value='".$line['id']."'> 
					<input type='submit' class='button' name='".$value."' value='".$value2."'>
				</td>
			</tr>
		</form>";
}
?>
</table>



<style>
#content .adsbygoogle {
	display: none !important;
}

.help p {
	font-size: 18px;
}
.help {
	width: 50%;
	padding: 10px;
	background: #2B41A9;
	border-radius: 2px;
	margin: 10px;
	text-align: left;
}

</style>