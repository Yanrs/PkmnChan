<?php
include '../config.php';
include '../functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}

include '../_header.php';
printHeader('Manage Members');

$result1 = mysql_query("SELECT * FROM `users` WHERE `username` = '".$_SESSION['username']."'");
$user = mysql_fetch_array($result1);
$uclan = $user['clan']; 
$clan1 = mysql_query("SELECT * FROM `clans` WHERE `id`='$uclan'");
$clan = mysql_fetch_array($clan1);
$cowner = $clan['owner'];

if ($clan['owner'] != $user['username']){
	echo "<div class='error'>You do not have authorization to be here.</div>";
	
	die();
}

$clan1 = mysql_query("SELECT * FROM `clans` WHERE `id`='$user->clan'");

$clan_class = mysql_fetch_object($clan1);

$_GET['kick'] = mysql_real_escape_string($_GET['kick']);

if($_GET['kick'] != ""){

if ($clan_class->leader != $user->username){
	echo "<div class='error'>You do not have permission to kick.</div>";
	include '../_footer.php';
	die();
}

$from1 = mysql_query("SELECT * FROM `users` WHERE `clan`='$uclan' AND `id`='".$_GET['kick']."'");
$from_user = mysql_fetch_object($from1);

if($from_user->clan != $uclan){
echo "<div class='error'>Invalid User.</div>";
$errorz = 1;
}

if($from_user->id == $uid){
echo "<div class='error'>You can't remove yourself.</div>";
$errorz = 1;
}

if (!preg_match('~^[a-z0-9 ]+$~i', $_GET['kick'])){
echo "<div class='error'>Please don't enter special characters.</div>";
$errorz = 1;
}

if($errorz != 1){

$result = mysql_query("UPDATE `users` SET `clan`='' WHERE `id`='".$from_user->id."'");
$result2 = mysql_query("UPDATE clans SET members = members - 1 WHERE id = '$uclan'");
echo "<div class='success'>You've just kicked <a href='/profile.php?id=".$from_user->id."'>".$from_user->username."</a> out of your clan.</div>";
}
}
?>

<?
/* Set current, prev and next page */
$page = (!isset($_GET['page']))? 1 : $_GET['page'];
$prev = ($page - 1);
$next = ($page + 1);

/* Max results per page */
$max_results = 3000;

/* Calculate the offset */
$from = (($page * $max_results) - $max_results);

/* Query the db for total results. You need to edit the sql to fit your needs */
$result = mysql_query("SELECT * FROM `users` WHERE `clan` = '$uclan'");
//echo $result;
$total_results = mysql_num_rows($result);
$total_pages = ceil($total_results / $max_results);

$pagination = '';


/* Create a PREV link if there is one */
if($page > 1)
{
$pagination .= '<a href="/shop.php?page='.$prev.'">Previous</a> ';
}

/* Loop through the total pages */
for($i = 1; $i <= $total_pages; $i++)
{
if(($page) == $i)
{
$pagination .= $i;
}
else
{
$pagination .= '<a href="/shop.php?page='.$i.' ">'.$i.'</a>';
}
}
/* Print NEXT link if there is one */
if($page < $total_pages)
{
$pagination .= '<a href="/shop.php?page='.$next.'">Next</a>';
}

$uid = (int) $_SESSION['userid'];
$query = mysql_query("SELECT * FROM `users` WHERE `clan` = '$uclan' LIMIT $from, $max_results");
$color="1";
echo '<table class="pretty-table" class="t" border=4 width="55%" cellpadding="0" cellspacing="0" align="center" border="1" bordercolor="#444444">
<tr>
<th>Trainer #</th>
<th>Username</th>
<th>Options</th>
 
</tr>';

while($res1=mysql_fetch_array($query)){


echo "<tr bgcolor='#EEEEEE'>
<td>".$res1['id']."</td><td><a href='/profile.php?id=".$res1['id']."'>".$res1['username']."</a></td>";

echo "<td><a href=/clans/moderate.php?kick=".$res1['id'].">Kick</a></td></tr>";
}
echo '</table>';
?>
<? include '../_footer.php';?>