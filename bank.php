<?php
include 'config.php';
include 'functions.php';

if (!isLoggedIn()) {
die();
}

include '_header.php';
printHeader('Bank');

logs($uid, " accessed bank page!");
?>
<?php
// other stuff
$user1 = mysql_query("SELECT * FROM `users` WHERE `id`='" . $_SESSION['userid'] . "'");
$user = mysql_fetch_object($user1);

//QUICK DRAW
if($_GET['d'] == 1){
$_POST['deposit'] = 1;
$_POST['damount'] = $user->money;
}elseif($_GET['w'] == 1){
$_POST['withdraw'] = 1;
$_POST['wamount'] = $user->bank;
}

if($_POST['deposit'] != ""){
$_POST['withdraw'] = "";

if(strtolower(substr($_POST['damount'], -1)) == "k"){
$_POST['damount'] = $_POST['damount'] * 1000;
}
if(strtolower(substr($_POST['damount'], -1)) == "m"){
$_POST['damount'] = $_POST['damount'] * 1000000;
}


$_POST['damount'] = mysql_real_escape_string($_POST['damount']);

$dontlike = array(',', '$', '+', '-');
$yoyo   = array('', '', '', '');
$_POST['damount'] = str_replace($dontlike, $yoyo, $_POST['damount']);

    if ($user->money < 1){
        echo "Sorry, you don't have any money to deposit.";
  include "_footer.php";
  die();
    }

    if ($_POST['damount'] > $user->money) {
        echo "You do not have that much money.";
    }

if (!preg_match('~^[a-z0-9 ]+$~i', $_POST['damount'])){
  echo "Invalid characters detected.";
  include "_footer.php";
  die();
}

    if ($_POST['damount'] <= $user->money && $_POST['damount'] > 0) {
        echo "Money deposited.";
        $user->bank = $_POST['damount'] + $user->bank;
        $user->money = $user->money - $_POST['damount'];
        $result = mysql_query("UPDATE `users` SET `bank` = '".$user->bank."', `money` = '".$user->money."' WHERE `id`='".$_SESSION['userid']."'");

}


    }

?>
<?
if($_POST['withdraw'] != ""){


$_POST['wamount'] = mysql_real_escape_string($_POST['wamount']);

$dontlike = array(',', '$', '+', '-');
$yoyo   = array('', '', '', '');
$_POST['wamount'] = str_replace($dontlike, $yoyo, $_POST['wamount']);


if(strtolower(substr($_POST['wamount'], -1)) == "k"){
$_POST['wamount'] = $_POST['wamount'] * 1000;
}
if(strtolower(substr($_POST['wamount'], -1)) == "m"){
$_POST['wamount'] = $_POST['wamount'] * 1000000;
}

    if ($_POST['wamount'] > $user->bank) {
        echo "You do not have that much money in the bank";
    }
if (!preg_match('~^[a-z0-9 ]+$~i', $_POST['wamount'])){
  echo "Invalid characters detected.";
  include "_footer.php";
  die();
}

    if ($_POST['wamount'] <= $user->bank && $_POST['wamount'] > 0) {
        echo "Money withdrawn.";
        $user->bank = $user->bank - $_POST['wamount'];
        $user->money = $user->money + $_POST['wamount'];
        $result = mysql_query("UPDATE `users` SET `bank` = '".$user->bank."', `money` = '".$user->money."' WHERE `id`='".$_SESSION['userid']."'");
logs($uid, " withdrew $".$_POST['wamount']." !");
    }
}

?>


<center>
<table class="pretty-table"><tr>
<th>
Withdraw Money
</th>
</tr><tr>
<td>
<br><br>

<form method='post'>
<input type='text' name='wamount' value='$<?=number_format($user->bank)?>' size='15' maxlength='20'> &nbsp;
<br>
<input type='submit' name='withdraw' value='Withdraw' id='button'>
</form><br><br>
</td></tr></table>

<br />

<table class="pretty-table"><tr><th>Deposit Money</th></tr>
<tr><td>
<br><br>
<form method='post'>
<input type='text' name='damount' value='$<?=number_format($user->money)?>' size='15' maxlength='20'> &nbsp;
<br>
<input type='submit' name='deposit' value='Deposit' id='button'>
</form><br><br>
</td></tr></table>
</center>
<?include '_footer.php';?>