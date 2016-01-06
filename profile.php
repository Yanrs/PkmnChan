<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'gym_functions.php';
//$linkr = 2;
include 'bbcode.php';
if (!isLoggedIn()) {
	redirect('index.php');
}
include '_header.php';
printHeader('Profile');

$uid = (int) (isset($_GET['id']) ? $_GET['id'] : $_SESSION['userid'] );
$defaultAvatar = 'http://pkmnhelios.net/images/pokemon/Magikarp.png';

$query = mysql_query("SELECT * FROM `users` WHERE `id`='{$uid}'");

$query45 = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
$cktest = mysql_fetch_assoc($query45);
$ck = $cktest['username'];


$skaap1= mysql_query("SELECT * FROM `friends` WHERE (`uid`='". (int) $_SESSION['userid']."' AND `friendid`='". (int) $_GET['id']."')");
$skaap= mysql_num_rows($skaap1);

//START FRIEND REQUEST
if($_GET['friend'] == "req"){

if($skaap > 0){echo "You already are friends with this user.";$error=1;}


if($error != 1){
$sql = "INSERT INTO `friends` (`uid`,`friendid`) VALUES ('". (int) $_SESSION['userid']."', '" . (int) $_GET['id'] . "')";
mysql_query($sql);
echo "You have added this user as your friend.";
}

}
//END FRIEND REQUEST
if(mysql_num_rows($query) != 1) {
	echo 'This user does not exist';
} else {
	$userRow = mysql_fetch_array($query);
	
	$avatar = !filter_var($userRow['avatar'], FILTER_VALIDATE_URL) ? $defaultAvatar : cleanHtml($userRow['avatar']) ;

	$teamCells = array();
	for ($i=1; $i<=6; $i++) {
		$pid = $userRow[ 'poke' . $i ];
		
		
		if ($pid == 0) {
			$teamCells[] = '
				<img src="images/pokemon/EMPTY.png" alt="No Pokemon" /><br />
				<div class="info" style="display: none;">
					Empty slot
				</div>							
			';
		} else {
			$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$pid}'");
			$pokemon = mysql_fetch_assoc($query);
			
			if($pokemon['gender'] == "1"){$gender="Male";}
			if($pokemon['gender'] == "2"){$gender="Female";}
			if($pokemon['gender'] == "0"){$gender="Genderless";}
			if($pokemon['gender'] == ""){$gender="";}
			
			$adminOptions = '';
			if (isAdmin()) {
				$adminOptions = '
					[<a href="./staff/edit_pokemon.php?id='.$pokemon['id'].'">Edit Pokemon</a>]<br /><br />
				';
			}
			$types = array('Shiny ', 'Halloween ', 'Shiny Halloween ', 'Helios ', 'Possion ', 'Snow ', 'Enraged ', 'Golden ', 'Ancient ');
                        $pokemonName = str_replace($types, '', $pokemon['name']);
                        
                        $query = mysql_query("SELECT `type1`,`type2` FROM `pokedex` WHERE `name`='{$pokemonName}'");
                        $typeRow = mysql_fetch_assoc($query);
                        
                        $typeStr = '';
                        $typeStr .= !empty($typeRow['type1']) ? '<img src="images/dex/'.$typeRow['type1'].'.png"> ' : '' ;
                        $typeStr .= !empty($typeRow['type2']) ? '<img src="images/dex/'.$typeRow['type2'].'.png"> ' : '' ;
 			$typeStr .= !empty($typeStr) ? '<br /><br />' : '' ;
 
                        if ($pokemon['level'] >= '2250') { $heart = 'likes'; } else { $heart = 'like'; }
						
                        $teamCells[] = '
											<img src="/images/pokemon/' . $pokemon['name'] . '.png" alt="' . $pokemon['name'] . '"  />
											<div class="info" style="display: none;">
											<div class="like"><img src="/img/layout/members/'. $heart .'.png" /></div>
											<a href="pinfo.php?id='.$pokemon['id'].'">' . $pokemon['name'] . '</a><br />
											Level: ' . number_format($pokemon['level']) . ' <br />
											Exp: ' . number_format($pokemon['exp']) . '<br />
											Gender: <img src="images/gender/' . $pokemon['gender'] . '.png"/> '.$gender.'<br /><br />
											'.$typeStr.'
											'.$adminOptions.'
											</div>
			';
		}

	}
	

	
	
	
	$userBadges = array();
	$query = mysql_query("SELECT * FROM `user_badges` WHERE `uid`='{$uid}'");
	while ($row = mysql_fetch_assoc($query)) { $userBadges[] = $row['badge']; }
		
	$badgeCells = array();
	$allLeaguesArray = getAllLeaguesLeadersAndBadges();
	
	foreach ($allLeaguesArray as $leagueName => $leagueArray) {
		$bcell = '<p>'.$leagueName.'</p>';
		
		foreach ($leagueArray as $nameAndBadge) {
			$badge  = $nameAndBadge['badge'];
			$leader = $nameAndBadge['name'];
			
			if (in_array($badge, $userBadges)) {
				$bcell .= '<img src="images/badges/'.$badge.'.png" title="Won '.$leader.' and earned '.$badge.'"/>';
			} else {
				$bcell .= '&nbsp;';
			}
		}
		
		$badgeCells[] = $bcell;
	}
	
	

	
	
	

	$totalQuery = mysql_query("SELECT SUM(`exp`) AS `total_exp` FROM `user_pokemon` WHERE `uid`='{$uid}'");
	$totalExp = $totalQuery ? end( mysql_fetch_assoc($totalQuery)) : 0 ;
	
	$uniquesQuery = mysql_query("SELECT COUNT( DISTINCT(`name`) ) AS `uniques` FROM `user_pokemon` WHERE `uid`='{$uid}'");
	$numUniques   = $uniquesQuery ? end( mysql_fetch_assoc($uniquesQuery)) : 0 ;
	
	if ($userRow['clan'] == 0) {
		$clanName = 'Not in a clan!';
	} else {
		$clanQuery = mysql_query("SELECT `name` FROM `clans` WHERE `id`='{$userRow['clan']}'");
		$clanName = mysql_fetch_assoc($clanQuery);
		$clanName = cleanHtml($clanName['name']);
	}
	
	$signature = nl2br(cleanHtml($userRow['signature']));
	if ($signature != '') {
		//$signature = '<br /><div style="border-top: 1px solid #666666; border-bottom: 1px solid #666666; padding: 5px 0;">' . $signature . '</div>';
		$signature = '<br /><div>' . $signature . '</div><br />';
	}
	
	echo '<p style="text-align: center;">';
	if ($_SESSION['userid'] == $uid) {
		echo '<a href="edit_profile.php">Edit My Profile</a><br />';
	}
	
	if (isAdmin()) {
		echo '[<a href="staff/edit_user.php?id='.$userRow['id'].'">Edit This Account</a>]<br /><br />';
	}
	echo '</p>';
	
	if ($userRow['banned'] == 1) {
		echo '<div class="error">This user has been banned!</div>';
	}
	
	if ($userRow['premium'] == 2) {
		$premium = 'Yes';
		$pre_img = '<br /><img src="/images/userbars/premium.png" />';
	} else {
		$premium = 'No';
		$pre_img = '';
	}
?>
			<div class="profile">
				<div class="trainer-card">
					<div class="tc-img">
						<div class="t-info"><?=cleanHtml($userRow['username'])?> #<?=cleanHtml($userRow['id'])?></div>
					</div>
					
					<div class="avatar">
						<img src="<?=cleanHtml($userRow['avatar'])?>" alt="Avatar" style="max-width:120px; max-height:120px;">
					</div>
					
					<table>
						<?=cellsToRowsProfile($teamCells, 3)?>
					</table>
				</div>
				
				<div class="links">
					<a href="messages.php?p=new&uid=<?=urlencode($userRow['id'])?>">Send Message</a>
					<a href="battle_user.php?id=<?=urlencode($userRow['id'])?>">Battle this user</a>
					<a href="profile.php?id=<?=$uid?>&friend=req">Add As A Friend</a>
                    <a href="view_box.php?id=<?=urlencode($userRow['id'])?>">View Box</a>
					<a href="trade_sell.php?uid=<?=urlencode($userRow['id'])?>">View Trades</a>
					<a href="trade_sell.php?uid=<?=urlencode($userRow['id'])?>&sale">View Sales</a>
					<a href="card.php?id=<?=urlencode($userRow['id'])?>">Trainer Card</a>
					<a href="send_money.php?id=<?=urlencode($userRow['id'])?>">Send Money</a>
				</div>
				
				<div class="statics">
					<div class="header in">
						<table class="info">
							<tr>
								<td width="50%">Statistics</td>
								<td width="50%">Badges</td>
							</tr>
						</table>
					</div>
					
					<table class="fixed-stats">
						<tr>
							<td width="50%">
								<table class="stats">
									<tr>
										<td>Joined:</td>
										<td><?=date('Y/m/d',$userRow['signup_date'])?></td>
									</tr>
									<tr>
										<td>Total Exp:</td>
										<td><?=number_format($totalExp)?></td>
									</tr>
									<tr>
										<td>Num Uniques:</td>
										<td><?=number_format($numUniques)?></td>
									</tr>
									<tr>
										<td>Money:</td>
										<td><?=number_format($userRow['money'])?></td>
									</tr>
									<tr>
										<td>Battles Won:</td>
										<td><?=number_format($userRow['won'])?></td>
									</tr>
									<tr>
										<td>Battles Lost:</td>
										<td><?=number_format($userRow['lost'])?></td>
									</tr>
									<tr>
										<td>Clan:</td>
										<td><?=$clanName?></td>
									</tr>
									<tr>
										<td>Last Seen:</td>
										<td><?=secondsToTimeSince(time()-$userRow['lastseen'])?></td>
									</tr>
									<tr>
										<td>Trainer Level:</td>
										<td><?=expToLevel($userRow['trainer_exp'])?></td>
									</tr>
									<tr>
										<td>Premium:</td>
										<td><?=$premium?></td>
									</tr>
								</table>
							</td>
							
							<td width="50%">
								<table class="badges">
									<tr>
										<td>
											<?=cellsToRowsBadges($badgeCells, 8)?>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
					
				</div>
				
				<?php if($_SESSION['userid']) { ?>
				
				<div class="badges">
					<div class="header in"><p>Achievements <small style="font-weight: 300;font-size: 10px !important;position: relative;left: 5px;top: -5px;color: rgb(63, 192, 208);">[ALPHA]</small></p></div>
					
					<div class="show badges" style="text-align: center; width: 70%; margin: 10px auto;">
					<?php 
					if($userRow['won'] >= 1000) { echo '<img src="images/achievements/1k.png" title="Have won a 1000 battles">'; }
					if(expToLevel($userRow['trainer_exp']) >= 1000) { echo '<img src="images/achievements/1kb.png" title="Reached trainer level 1,000">'; }
					if(expToLevel($userRow['trainer_exp']) >= 5000) { echo '<img src="images/achievements/5k.png" title="Reached trainer level 5,000">'; }
					if(expToLevel($userRow['trainer_exp']) >= 10000) { echo '<img src="images/achievements/10k.png" title="Reached trainer level 10,000">'; }
					?>
					</div>
				</div>
				<?php } ?>
				
				<div class="about">
					<div class="header in"><p>Signature</p></div>
						
					<div class="signature">
						<?=bbcode($signature)?>
					</div>
				</div>
			</div>

<?php
}
include '_footer.php';
php?>