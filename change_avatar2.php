<?php
include("functions.php");
include("config.php");
        
if (!isLoggedin()) {
    redirect('login.php');
}

$uid = (int) $_SESSION['userid'];
        
include "_header.php";
printHeader("Change Avatar");

if(isset($_POST['pic'])) {
	$avatar = cleanSql('http://pkmnhelios.net/'.$_POST['pic']);
	mysql_query("UPDATE `users` SET `avatar`='{$avatar}' WHERE `id`='{$uid}'");
	echo '<div class="notice">Your avatar was updated succesfully</div>';
}
$query = mysql_query("SELECT `avatar` FROM `users`WHERE `id`='{$uid}'");
$avatar = mysql_fetch_assoc($query);
$avatar = $avatar['avatar'];

echo '
	<form action="" method="post">
		<div style="height: 250px; width: 98%; width: calc(100% - 20px); overflow-y: scroll; overflow-x: none; margin: 0 auto;">
';
$images = glob('images/trainers/*.png');
foreach ($images as $image) {
	$attr = 'http://pkmnhelios.net/'.$image == $avatar ? ' checked="checked" ' : '' ;
	echo '
		<div style="height: 120px; width: 100px; float: left;">
			<img src="'.$image.'" /><br />
			<input type="radio" name="pic" value="'.$image.'" '.$attr.' />
		</div>
	';
}
echo '
		</div>
		<br />
		<input type="submit" value="Change Avatar" />
	</form>
';


include "_footer.php";
?>