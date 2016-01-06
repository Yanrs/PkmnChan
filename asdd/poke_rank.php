<?php
include '../config.php'; 
include '../functions.php'; 
include '../_header.php';
printHeader('Top Individual Pokemon Ranking');

$name = cleanSql( trim($_GET['name']) );

$query = mysql_query("SELECT `user_pokemon`.`id` AS `pid`, `user_pokemon`.`name`, `user_pokemon`.`level`, `user_pokemon`.`gender`, `users`.`username`, `users`.`id` AS `uid` FROM `user_pokemon`, `users` WHERE `user_pokemon`.`name`='{$name}' AND `user_pokemon`.`uid`=`users`.`id` ORDER BY `level` DESC LIMIT 100");

if (mysql_num_rows($query) == 0) {
	echo '<div class="error">Could not find any pokemon.</div>';
	include '../_footer.php';
	die();
}

$filename = '../images/pokemon/'.$name.'.png';
if (is_file($filename)) {
	echo '<img src="'.$filename.'" title="'.$name.'" alt="'.$name.'" />';
}

echo '
	<table class="pretty-table">
		<tr>
			<th>Owner</th>
			<th>Name</th>
			<th>Level</th>
			<th>Gender</th>
		</tr>
';

$genderArray = array('1'=>'Male', '2'=>'Female', '0'=>'Genderless');
while ($pokeArray = mysql_fetch_assoc($query)) {
	echo '
		<tr>
			<td><a href="../profile.php?id=' . $pokeArray['uid'] . '">' . cleanHtml($pokeArray['username']) . '</td>
			<td><a href="../pinfo.php?id=' . $pokeArray['pid'] . '">' . $pokeArray['name'] . '</td>
			<td>' . number_format($pokeArray['level']) . '</td>
			<td>' . $genderArray[ $pokeArray['gender'] ] . '</td>
		</tr>
	';
}
echo '
	</table>
';
include '../_footer.php';
?>