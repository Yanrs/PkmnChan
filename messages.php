<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('login.php');
}

include '_header.php';
printHeader('Messages');
echo '
    <div style="text-align: center;">
        <a href="messages.php?p=inbox">Inbox</a> &bull; 
        <a href="messages.php?p=outbox">Outbox</a> &bull; 
        <a href="messages.php?p=new">New Message</a>
    </div>
';

$uid = (int) $_SESSION['userid'];


switch ($_GET['p']) {
	case 'outbox';
	case 'inbox':

		$column   = $_GET['p'] == 'outbox' ? 'sender_uid' : 'recipient_uid' ;
		$header   = $_GET['p'] == 'outbox' ? 'To'         : 'From'          ;
		$key      = $_GET['p'] == 'outbox' ? 'recipient'  : 'sender'        ;
		$extraSql = $_GET['p'] == 'outbox' ? " `deleted_by_sender`='0' " : " `deleted_by_recipient`='0' " ;
		
		$query = mysql_query("SELECT * FROM `messages` WHERE `{$column}`='{$uid}' AND {$extraSql} ORDER BY `id` DESC");
		
		if (mysql_num_rows($query) == 0) {
			echo '<div class="error">You have no messages in your '.cleanHtml($_GET['p']).'.</div>';
		} else {
			echo '
				<form method="post" action="messages.php?p=delete">
				<table class="pretty-table">
					<tr>
						<th style="width: 30px;">
							<input type="checkbox" onclick="setChecked(\'messages[]\', this.checked)" id="selectionHelper" style="display: none;" />
						</th>
						<th>Subject</th>
						<th style="width: 20%;">'.$header.'</th>
						<th style="width: 30%;">Time Sent</th>
					</tr>
			';
			while ($row = mysql_fetch_assoc($query)) {
				$row = cleanHtml($row);
				$class = $row['read'] == 0 ? 'unread-message' : '' ;
				echo '
					<tr class="'.$class.'">
						<td><input type="checkbox" name="messages[]" value="'.$row['id'].'" /></td>
						<td><a href="messages.php?p=read&amp;id='.$row['id'].'">'.$row['subject'].'</a></td>
						<td><a href="profile.php?id='.$row['sender_uid'].'">'.$row[$key].'</a></td>
						<td>'.date('F j, Y, g:i a', $row['timestamp']).'</td>
					</tr>
				';
			}
			echo '
				<tr>
					<td colspan="4" class="text-left">
						<input type="submit" value="Delete Messages" />
					</td>
				</tr>
			';
			echo '
				</table>
				</form>
				<script>
					function setChecked(name, sts) {
						var elems = document.querySelectorAll(\'input[name="\'+name+\'"]\');
						for (var i=0; i<elems.length; i++) {
							elems[i].checked = sts;
						}
					}
					document.getElementById(\'selectionHelper\').style.display = "inline";
				</script>
			';
		}
	break;
	
	case 'read':
		$id = (int) $_GET['id'];
		$query = mysql_query("SELECT * FROM `messages` WHERE `id`='{$id}' LIMIT 1");
	
		if (mysql_num_rows($query) != 1) {
			echo '<div class="error">Message not found.</div>';
		} else {
			$message = mysql_fetch_assoc($query);
			
			
			if ($message['recipient_uid'] != $uid && $message['sender_uid'] != $uid) {
				echo '<div class="error">This message does not belong to you.</div>';
			} else if (
				($message['recipient_uid'] == $uid && $message['deleted_by_recipient'] == 1) ||
				($message['sender_uid'] == $uid && $message['deleted_by_sender'] == 1)
			) {
				echo '<div class="error">You have deleted this message.</div>';
			} else {
				if ($message['read'] == 0) {
					mysql_query("UPDATE `messages` SET `read`='1' WHERE `id`='{$id}' LIMIT 1");
					mysql_query("UPDATE `users` SET `unread_messages`=`unread_messages`-1 WHERE `id`='{$uid}' LIMIT 1");
				}
				$message = cleanHtml($message);
				
				$startQuotes = substr_count($message['message'], '[quote]');
				$endQuotes   = substr_count($message['message'], '[/quote]');
				
				if ($startQuotes != 0 && $startQuotes == $endQuotes) {
					$message['message'] = str_replace(array('[quote]', '[/quote]'), array('<div class="quote">', '</div>'), $message['message']);
				}
				
				if ($message['sender_uid'] == $uid) {
					$replyLink = '<a href="messages.php?p=new&uid='.$message['recipient_uid'].'">Send Another Message</a> &bull; ';
				} else {
					$replyLink = '<a href="messages.php?p=new&reply='.$message['id'].'">Reply</a> &bull; ';
				}
				
				echo '
					<table class="pretty-table">
						<tr>
							<td style="width: 100px;" class="text-right">To: </td>
							<td class="text-left"><a href="profile.php?id='.$message['recipient_uid'].'">'.$message['recipient'].'</a></td>
						</tr>
						<tr>
							<td style="width: 100px;" class="text-right">From: </td>
							<td class="text-left"><a href="profile.php?id='.$message['sender_uid'].'">'.$message['sender'].'</a></td>
						</tr>
						<tr>
							<td style="width: 100px;" class="text-right">Subject: </td>
							<td class="text-left">'.$message['subject'].'</td>
						</tr>
						<tr>
							<td style="width: 100px;" class="text-right">Message: </td>
							<td class="text-left">'.nl2br($message['message']).'</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td class="text-left">
								'.date('F j, Y, g:i a', $message['timestamp']).'<br />
								'.$replyLink.'<a href="messages.php?p=delete&id='.$message['id'].'">Delete</a>
							</td>
						</tr>
					</table>
				';
			}
		}
	break;
	
	case 'new':
		
		$username = isset($_POST['username']) ? $_POST['username'] : '' ;
		$subject  = isset($_POST['subject'])  ? $_POST['subject']  : '' ;
		$message  = isset($_POST['message'])  ? $_POST['message']  : '' ;
		$outbox   = isset($_POST['saveOutbox']) ? 0 : 1 ;
		
		if (count($_POST) > 0) {
			$sqlUsername = cleanSql($username);
			$sqlSubject  = cleanSql($subject);
			$sqlMessage  = cleanSql($message);
			$errors = array();
			
			$query = mysql_query("SELECT `id` FROM `users` WHERE `username`='{$sqlUsername}' LIMIT 1");
			if (empty($username)) {
				$errors[] = 'Username field was empty.';
			} elseif (mysql_num_rows($query) == 0) {
				$errors[] = 'Could not find anyone with that username.';
			} else {
				$userid = mysql_fetch_assoc($query);
				$userid = (int) $userid['id'];
			}
			
			if (empty($subject)) {
				$errors[] = 'Subject field was empty.';
			}
			
			if (empty($message)) {
				$errors[] = 'Message field was empty.';
			}
			
			if ($_SESSION['token'] != $_POST['token']) {
				$errors[] = 'There was an error sending the message.';
				$username = '';
				$subject  = '';
				$message  = '';
			}
			
			if (count($errors) > 0) {
				echo '<div class="error">'.implode('</div><div class="error">', $errors).'</div>';
			} else {
				$sqlSender = cleanSql($_SESSION['username']);
				$time = time();
				
				$query = mysql_query("
					INSERT INTO `messages` (
						`sender_uid`, `recipient_uid`, `sender`, `recipient`,
						`timestamp`, `subject`, `message`, `read`,
						`deleted_by_sender`, `deleted_by_recipient`
					) VALUES (
						'{$uid}', '{$userid}', '{$sqlSender}', '{$sqlUsername}',
						'{$time}', '{$sqlSubject}', '{$sqlMessage}', '0',
						'{$outbox}', '0'
					)
				");
				mysql_query("UPDATE `users` SET `unread_messages`=`unread_messages`+1, `total_messages`=`total_messages`+1 WHERE `id`='{$userid}' LIMIT 1");
				
				if ($query) {
					echo '<div class="notice">Message Sent.</div>';
					$username = '';
					$subject  = '';
					$message  = '';
				} else {
					echo '<div class="error">There was an error sending the message.</div>';
				}
			}
		}
		
		if (isset($_GET['reply'])) {
			$id = (int) $_GET['reply'];
			$query = mysql_query("SELECT * FROM `messages` WHERE `id`='{$id}' AND `recipient_uid`='{$uid}' LIMIT 1");
			
			if (mysql_num_rows($query) == 1) {
				$message  = mysql_fetch_assoc($query);
				$username = $message['sender'];
				$subject  = strpos($message['subject'], 'Re: ') === 0 ? $message['subject'] : 'Re: '.$message['subject'] ;
				$message  = '[quote]'.$message['message'].'[/quote]';
			}
		}
		
		if (isset($_GET['uid'])) {
			$id = (int) $_GET['uid'];
			$query = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$id}' LIMIT 1");
			
			if (mysql_num_rows($query) == 1) {
				$userRow  = mysql_fetch_assoc($query);
				$username = $userRow['username'];
			}
		}
		
		$token = md5(rand());
		$_SESSION['token'] = $token;
		echo '
			<form action="messages.php?p=new" method="post" style="width: 600px; margin: 20px auto;">
				<input type="hidden" name="token" value="'.$token.'" />
				<table class="pretty-table">
					<tr>
						<td class="text-right">To:</td>
						<td class="text-left"><input type="text" name="username" size="30" value="'.cleanHtml($username).'" /></td>
					</tr>
					<tr>
						<td class="text-right">Subject:</td>
						<td class="text-left"><input type="text" name="subject" size="50" value="'.cleanHtml($subject).'" /></td>
					</tr>
					<tr>
						<td valign="top" class="text-right">Message:</td>
						<td class="text-left">
							<textarea name="message" cols="50" rows="5">'.cleanHtml($message).'</textarea>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="saveOutbox"  /> Save in outbox?</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" value="Send Message" /></td>
					</tr>
				</table>
			</form>
		';
	break;
	
	case 'delete':
		$messageIds = array();
		
		if (isset($_GET['id'])) {
			$messageIds = array($_GET['id']);
		}
		
		if (isset($_POST['messages'])) {
			$messageIds = $_POST['messages'];
		}
		
		if (count($messageIds) == 0) {
			echo '<div class="info">You did not select any messages to delete.</div>';
		} else {
			foreach ($messageIds as $k => $v) {
				$messageIds[$k] = (int) $v;
			}
			
			$messageIdsSql = implode(', ', $messageIds);
			$query = mysql_query("SELECT * FROM `messages` WHERE `id` IN ({$messageIdsSql}) AND (`recipient_uid`='{$uid}' OR `sender_uid`='{$uid}')");
			
			if (mysql_num_rows($query) != count($messageIds)) {
				echo '<div class="error">Some of those messages do not belong to you.</div>';
			} else {
				while ($message = mysql_fetch_assoc($query)) {
					if ($message['recipient_uid'] == $uid) {
						mysql_query("UPDATE `messages` SET `deleted_by_recipient`='1' WHERE `id`='{$message['id']}' LIMIT 1");
						
						$extraSql = ($message['read'] == 0) ? ', `unread_messages`=`unread_messages`-1 ' : '' ;
						mysql_query("UPDATE `users` SET `total_messages`=`total_messages`-1 {$extraSql} WHERE `id`='{$uid}' LIMIT 1");
					}
					
					if ($message['sender_uid'] == $uid) {
						mysql_query("UPDATE `messages` SET `deleted_by_sender`='1' WHERE `id`='{$message['id']}' LIMIT 1");
					}
				}
				mysql_query("DELETE FROM `messages` WHERE `deleted_by_sender`='1' AND `deleted_by_recipient`='1'");
				
				if (count($messageIds) == 1) {
					echo '<div class="notice">The message you selected has been deleted.</div>';
				} else {
					echo '<div class="notice">The messages you selected have been deleted.</div>';
				}
			}
		}
	break;
}


include '_footer.php';
?>