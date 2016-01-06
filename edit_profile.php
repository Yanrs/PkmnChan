<?php
require_once 'config.php';
require_once 'functions.php';
require 'banned.php'; 

if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php';
printHeader('Edit Profile');

echo '
    <div style="text-align: center; padding: 10px 0;">
        <a href="change_avatar.php">Click here to change your avatar.</a>
    </div>
';

$message = '';
$uid = (int) $_SESSION['userid'];

if (isset($_POST['cpassword'], $_POST['npassword'], $_POST['napassword'], $_POST['email'], $_POST['sprite'], $_POST['signature'])) {
	$password     = $_POST['cpassword'];
	$passwordNew  = $_POST['npassword'];
	$passwordNew2 = $_POST['napassword'];
	$email        = $_POST['email'];
	
	$sprite       = $_POST['sprite'];
	$signature    = $_POST['signature'];
	$errors = array();
	
	$query = mysql_query("SELECT `password` FROM `users` WHERE `id`='{$uid}'");
	$passwordRow = mysql_fetch_assoc($query);
	
	if ($passwordRow['password'] != sha1($password)) {
		$errors[] = 'You entered the wrong password.';
	}
	
	if (!empty($passwordNew) && $passwordNew != $passwordNew2) {
		$errors[] = 'The new passwords you entered did not match.';
	} else if (!empty($passwordNew) && strlen($passwordNew) < 6) {
		$errors[] = 'Your new password muct be at least 6 characters long.';
	}
	
	if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
		$errors[] = 'The email you entered is not valid.';
	}
	
	if(!in_array($sprite, range(1, 10))) {
		$sprite = 1;
	}
	
	if (count($errors) > 0) {
		$message = '<div class="error">'.implode('</div><div class="error">', $errors).'</div>';
	} else {
	
		if (!empty($passwordNew)) {
			$newPasswordSql = !empty($passwordNew) ? " `password`='".sha1($passwordNew)."', " : '' ;
		}
		
		$sqlEmail  = cleanSql($email);
		$sqlSig    = cleanSql($signature);
		$sprite    = (int) $sprite;
		
		
		$query = mysql_query("UPDATE `users` SET {$newPasswordSql} `email`='{$sqlEmail}', `map_sprite`='{$sprite}', `signature`='{$sqlSig}' WHERE `id`='{$uid}'");
		
		if ($query) {
			$message = '<div class="notice">Your profile has been edited.</div>';
		} else {
			$message = '<div class="error">Something went wrong.</div>';
		}
	}
}











$query = mysql_query("SELECT * FROM `users` WHERE `id`='{$uid}'");
$userRow = cleanHtml( mysql_fetch_assoc($query) );

$cells = array();
for ($i=1; $i<=14; $i++) {
	$attr = $userRow['map_sprite'] == $i ? ' checked="checked" ' : '' ;
	$cells[] = '
	<label>
		<img src="images/sprites/'.$i.'.png" /><br />
		<input type="radio" name="sprite" value="'.$i.'" '.$attr.' />
	</label>
	';
}

echo '
	'.$message.'
	<form action="" method="post">
		<table class="pretty-table">
			<tr>
				<th class="text-right">Current Password <span class="small">(needed)</span>: </th>
				<td><input type="password" name="cpassword" value="" size="30" /></td>
			</tr>
			
			<tr>
				<th class="text-right">New Password <span class="small">(optional)</span>: </th>
				<td><input type="password" name="npassword" value="" size="30" /></td>
			</tr>
			
			<tr>
				<th class="text-right">New Password Again: </th>
				<td><input type="password" name="napassword" value="" size="30" /></td>
			</tr>
			
			<tr>
				<th class="text-right">Email (needed): </th>
				<td><input type="text" name="email" value="'.$userRow['email'].'" size="30" /></td>
			</tr>
			
			<tr>
				<th class="text-right" valign="top">Map Sprite</th>
				<td>
					<table class="inline-block">
						'.cellsToRows($cells, 5).'
					</table>
				</td>
			</tr>
			
						<tr>
				<th colspan="2" class="text-center">Signature</th>
			</tr>
			
			<tr>
				<td colspan="2" class="text-center">
					<textarea name="signature" cols="50" rows="5">'.$userRow['signature'].'</textarea>
				</td> 
			</tr>
		
			
			<tr>
				<th colspan="2" class="text-center">
					<center><input id="button" type="submit" value="     Save     " /></center>
				</th>
			</tr>
		
		</table>
    </form>
';

include '_footer.php';
?>