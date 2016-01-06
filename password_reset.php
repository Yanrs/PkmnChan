<?php
require_once 'functions.php';
require_once 'config.php';
include '_header.php';

if (isLoggedIn()) { redirect('index.php'); }

$username_1 = $_GET['username'];
$key = $_GET['key'];

if (isset($_POST['submit']) && !empty($_POST['key'])) {

	
	if (isset($_POST['username'], $_POST['key'], $_POST['password'], $_POST['re_password'])) {
	
		$sqlUsername = cleanHtml($_POST['username']);
		$sqlKey = cleanHtml($_POST['key']);
		$password = $_POST['password'];
		$rePassword = $_POST['re_password'];
		$newPassword = sha1($password);
		
		$query = mysql_query("
			SELECT *
			FROM `users`
			WHERE `username` = '{$sqlUsername}'
			AND `reset_key` = '{$sqlKey}'
		");

		$row = mysql_num_rows($query);
		
		if ($row != 0) {
			$update = mysql_query("
				UPDATE `users`
				SET `password` = '{$newPassword}'
				WHERE `username` = '{$sqlUsername}'
				AND `reset_key` = '{$sqlKey}'
			");			

			echo '<p class="successF">Successfully changed password!</p>';
		}
		else {
			echo '<p class="errorF">User not found!
			<br><a href="password_reset.php">Back?</a></p>';
		}
	}
	else {
		echo '<p class="errorF">You must fill all fields!
		<br><a href="password_reset.php">Back?</a></p>';
	}
}
else {
?>
<div class="content">
	<div class="wrap">
		<table>
			<tr>
				<td>
					<div class="poke three"></div>
					
					<div class="login forgot">
						<?=$msg?>
						
						<form method="POST" action="">
							<div class="title">Reset Password</div>
							
							<label>Username:</label>
							<input type="text" name="username" readonly="readonly" value="<?php echo cleanHtml($username_1); ?>">
							
							<label>Code from e-mail:</label>
							<input type="text" name="key" readonly="readonly" value="<?php echo cleanHtml($key); ?>">
							
							<label>New password:</label>
							<input type="password" name="password">
							
							<label>Re-type password:</label>
							<input type="password" name="re_password">
							
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