<?php
require_once 'config.php';
include '_header.php';
?>
<div class="header"><h2>Roll Dice</h2></div>
<?php


$rand1 = rand(1, 6);
$rand2 = rand(1, 6);
$rand3 = $rand1 + $rand2;


if(isset($_POST['slot']))
{

echo "<center><img src='/rpg/images/dice$rand1.png' /><img src='/rpg/images/dice$rand2.png' /></center>";

if($rand3 == 7)
{

$money = rand(300, 350);

echo "You rolled a total of 7 and won $money $";

$update = mysql_query("UPDATE users SET money = money + '$money' WHERE username = '$user'");
}

else
{
$updatepay = mysql_query("UPDATE users SET money = money - 50 WHERE username = '$user'");

echo "You didnt roll a 7 and just had to pay 50 $ for trying out.";
}
}

?>
<br />
<form action="" method="post">
<input type="submit" name="slot" value="Roll The Dice">
<br /><?php 

$view = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = $id"));

$moneycheck = $view["money"];

if($moneycheck <= 0)
{
$fixmgpoints = mysql_query("UPDATE users SET money = 0 WHERE id = $id");
echo "Good Luck<br />";
}
else
{
echo "You currently have ";
echo "$";
echo $view["money"]; 
echo " in your account.";
}
?>
<?php
include('_footer.php');
?>

