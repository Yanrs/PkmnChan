<?php
require_once '../config.php';
require_once '../functions.php';
require_once 'admin_functions.php';

if (!isAdmin()) {
    die('Only admins can access this page.');
}

include '_header.php';
echoHeader('Find Users With The Same IP.');

$ip     = $_GET['ip'];
$ipHtml = cleanHtml($ip);
$ipSql  = cleanSql($ip);

echo '
    <form method="get" class="center-text bottom-padded">
        IP: <input type="text" name="ip" value="'.$ipHtml.'" />
        <input type="submit" value="Search For IP" />
    </form>
';

if (isset($_GET['ip'])) {
	$query = mysql_query("SELECT * FROM `users` WHERE `ip`='{$ipSql}'");
	
	if (mysql_num_rows($query) == 0) {
	    echo '
	    	<div class="error">
	    		Could not find any users with the ip address of <strong>'.$ipHtml.'</strong>!
	   	</div>
	   ';
	} else {
		echo '
			<table class="center pretty-table" style="width: 50%;">
				<tr>
					<th colspan="3">Users with the same IP</th>
				</tr>
		';
		while ($userInfo = mysql_fetch_assoc($query)) {
			$userInfo = cleanHtml($userInfo);
			echo '
				<tr>
					<td>
						<a href="../profile.php?id='.$userInfo['id'].'">
							'.$userInfo['username'].'
						</a>
					</td>
					<td class="center-text" style="width: 80px;">
						[<a href="edit_user.php?id='.$userInfo['id'].'">Edit&nbsp;User</a>]
					</td>
					<td class="center-text" style="width: 130px;">
						<a href="user_ip.php?ip='.$userInfo['ip'].'">
							'.$userInfo['ip'].'
						</a>
					</td>
				</tr>
			';
		}
		echo '
			</table>
		';
	}
	
	// network
	$ipParts = explode('.', $ip);
	unset($ipParts[ count($ipParts)-1 ]);
	$newIp = implode('.', $ipParts);
	$newIpSql = cleanSql($newIp);
	$newIpHtml = cleanHtml($newIp.'.*');
	
	$query = mysql_query("SELECT * FROM `users` WHERE `ip` LIKE '{$newIpSql}%' AND `ip`!='{$ipSql}' ORDER BY `ip` ASC LIMIT 50");
	$numRows = mysql_num_rows($query);
	
	if ($numRows == 0) {
	    echo '
	    	<div class="error">
	    		Could not find any users with the an ip address matching <strong>'.$newIpHtml.'</strong>!
	   	</div>
	   ';
	} else {
		echo '
			<br />
			<table class="center pretty-table" style="width: 50%;">
				<tr>
					<th colspan="3">Users with nearly the same IP</th>
				</tr>
				<tr>
					<td colspan="3" class="small center-text">Could be on the same network.</td>
				</tr>
		';
		while ($userInfo = mysql_fetch_assoc($query)) {
			$userInfo = cleanHtml($userInfo);
			echo '
				<tr>
					<td>
						<a href="profile.php?id='.$userInfo['id'].'">
							'.$userInfo['username'].'
						</a>
					</td>
					<td class="center-text" style="width: 80px;">
						[<a href="edit_user.php?id='.$userInfo['id'].'">Edit&nbsp;User</a>]
					</td>
					<td class="center-text" style="width: 130px;">
						<a href="user_ip.php?ip='.$userInfo['ip'].'">
							'.$userInfo['ip'].'
						</a>
					</td>
				</tr>
			';
		}
		echo '
			</table>
		';
	}
}

include '_footer.php';

?>