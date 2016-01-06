<?php
require_once 'config.php';
require_once 'functions.php';
include '_header.php';
logs($uid, " viewed mypokedex.php !");
echo '<table><tr><th><font size="4"><img src="http://pkmnplanet.net/rpg/images/mugshots/ck/Bugsy.png" style="float:right"/>Here you can see all the pokemon you managed 
to catch during your time in the Pokemon Planet RPG. Remember we dont have limits of how much pokemon can be caught so go ahead and catch many as you. Who knows maybe in
the future there will be ranks for who caught the most pokemons.';

$uid = (int) $_SESSION['userid'];
$query = mysql_query("
	SELECT
		`pokemon`.`name`,
		IF ((SELECT `id` FROM `user_pokemon` WHERE `name`=`pokemon`.`name` AND `uid`='{$uid}' LIMIT 1), 1, 0) as `caught_normal`,
		IF ((SELECT `id` FROM `user_pokemon` WHERE `name`=CONCAT('Shiny ', `pokemon`.`name`) AND `uid`='{$uid}' LIMIT 1), 1, 0) as `caught_shiny`,
		IF ((SELECT `id` FROM `user_pokemon` WHERE `name`=CONCAT('Snow ', `pokemon`.`name`) AND `uid`='{$uid}' LIMIT 1), 1, 0) as `caught_snow`
	FROM
		`pokemon`
	ORDER BY `pokemon`.`name` ASC
");


echo '
	<table border="0" style="text-align: center; margin: 30px auto; width: 400px;">
';
$lastLetter = '';
while ($pokemon = mysql_fetch_assoc($query)) {

	if ($pokemon['name'][0] != $lastLetter) {
		echo '
			<tr>
				<td colspan="3" style="font-weight: bold; font-size: 30px;">'.strtoupper($pokemon['name'][0]).'</td>
			</tr>
			<tr style="font-weight: bold; text-decoration: underline;">
				<td>Name</td>
				<td>Normal</td>
				<td>Shiny</td>
				<td>Snowy</td>
				
			</tr>
		';
		$lastLetter = $pokemon['name'][0];
	}

	$normalimage = ($pokemon['caught_normal'] == 1) ? 'pb.gif' : 'dpb.gif' ;
	$shinyimage  = ($pokemon['caught_shiny'] == 1)  ? 'pb.gif' : 'dpb.gif' ;
	$snowimage  = ($pokemon['caught_snow'] == 1)  ? 'pb.gif' : 'dpb.gif' ;
	
	echo '
		<tr>
			<td>'.$pokemon['name'].'</td>
			<td><img src="images/'.$normalimage.'" /></td>
			<td><img src="images/'.$shinyimage.'" /></td>
			<td><img src="images/'.$snowimage.'" /></td>
		</tr>
	';
}
echo '</table>';

echo '</div>';

include '_footer.php';
?>