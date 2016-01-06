<?php 
include 'config.php';
include 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php'; 
printHeader('Online Members');

$uid = (int) $_SESSION['userid'];
$time = time();
$otime = $time - (60*60);

/// just an test -- >
if (isset($_GET['get'])) {
	echo date('h:i:s A',$_SESSION['lastseen']);
	die();
}
/// < -- end of test

$query = mysql_query("SELECT * FROM `users` WHERE `lastseen`>='{$otime}' ORDER BY `lastseen` DESC");
$rows = mysql_num_rows($query);

if (getConfigValue('most_online') < $rows ) {
	setConfigValue('most_online', $rows);
}

echo "<center>
		<font color=white>
			Total users online: ".$rows."<br />
			Most users ever online: ".getConfigValue('most_online')."<br /><br />
		</font>
	</center>"; 
echo '		
                        <table class="pretty-table">
                          
                                <tr>                                 
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Last Seen</th>
                                    <th>Options</th>
                                </tr>

                           
';
while ($row = mysql_fetch_assoc($query)) {
	$lastseenMins = floor(($time-$row['lastseen'])/60);
	$lastseenSecs = ($time-$row['lastseen'])%60;
	$lastseenStr = $lastseenMins > 0 ? $lastseenMins.'mins ' : '' ;
	$lastseenStr .= $lastseenSecs.'secs ago';

	echo '
		<tr>
			<td class="pad">'.$row['id'].'</td>
			<td class="pad"><a href="profile.php?id='.$row['id'].'"><b>'.htmlspecialchars($row['username']).' '.$row['rank'].'</a></td></b></font>
			<td class="pad">'.$lastseenStr.'</td>
			<td class="pad">
				<a href="battle_user.php?id='.$row['id'].'">Battle User</a> - <a href="view_box.php?id='.$row['id'].'">View Box</a>
			</td>
		</tr>
	';
}
echo '</table>';
?>



	<?php include ('_footer.php'); ?>
	
	<div>
	</section>
	</div>