<?php
require_once 'functions.php';
require_once 'config.php';
include '_header.php';

if (isLoggedIn()) { redirect('index.php'); }

if ($_POST['submit']) {
	$username = (string) $_POST['username'];
	$email = (string) $_POST['email'];
	
	$sqlUsername = cleanSql($username);
	$htmlUsername = cleanHtml($username);
	
	$sqlEmail = cleanSql($email);
	$htmlEmail = cleanHtml($email);
	
	
	if ($username && $email) {
		$passwordlenth = 25;
		$charset = 'abcdefghijklmnoprstovwxy1234567890';
		
		for ($x = 1; $x <= $passwordlenth; $x++) {
			$rand = rand() % strlen($charset);
			$temp = substr($charset, $rand, 1);
			$key .= $temp;
		}
		
		//$key_sha1 = sha1($key);
		
		$query = mysql_query("
			SELECT * 
			FROM `users`
			WHERE `username` = '{$sqlUsername}'
			AND `email` = '{$sqlEmail}'
		") or die(mysql_error());

		$row = mysql_num_rows($query);
		
		if ($row != 0) {
			$update = mysql_query("
				UPDATE `users`
				SET `reset_key` = '{$key}'
				WHERE `email` = '{$sqlEmail}'
			");
			
			//Send e-mail
			
			$to = $email;
			$subject = 'Reset Password';
			$headers = 'From: roby@pkmnhelios.net';
			$body	= '
				Hello '.$username.', 
				you recently requested a new password at Pokemon Helios RPG
				follow the link below to reset your password: 
				
				http://pkmnhelios.net/forgot_password/reset.php?key='.$key.'&username='.urlencode($username).'
			
				Your reset form password is: 
				    '.$key.'
					
				If you still have problems with password then go on:
					http://pkmnhelios.net/chatroom.php
			';		
			
			mail($to, $subject, $body, $headers);
			echo '<p class="successF">Your password has been sent to: '.$htmlEmail.'</p>';
		} else {
			echo'<p class="errorF">No user found!
			<br><a href="password_forgot.php">Back?</p>';
		}
	}
		else {
			echo '<p class="errorF">You must type both username and password!
			<br><a href="password_forgot.php">Back?</p>';
		}

}  else {
?>
<div class="content">
	<div class="wrap">
		<table>
			<tr>
				<td>
					<div class="poke three"></div>
					
					<div class="login forgot">
						<?=$msg?>
						
						<form method="POST" action="" autocomplete="off">
							<div class="title">Reset Password</div>
							
							<label>Username:</label>
							<input type="text" name="username" autofocus="on">
							
							<label>E-mail:</label>
							<input type="text" name="email">
							
							<input type="submit" name="submit" value="RESET" class="btn">
						</form>
					</div>
					<? include '_footer.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>
<? } ?>