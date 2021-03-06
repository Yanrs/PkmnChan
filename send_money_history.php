<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
redirect('login.php');
}

$uid = (int) $_SESSION['userid'];

include '_header.php';

echo '
<center>
<a href="send_money.php">Send Money</a> &bull; 
<a href="send_money_history.php?clear">Clear History</a><br /><br />
</center>
';

mysql_query("UPDATE `send_money_history` SET `seen_by_recipient`='1' WHERE `recipient_uid`='{$uid}'");

if (isset($_GET['clear'])) {
mysql_query("UPDATE `send_money_history` SET `deleted_by_recipient`='1' WHERE `recipient_uid`='{$uid}'");
mysql_query("UPDATE `send_money_history` SET `deleted_by_sender`='1' WHERE `sender_uid`='{$uid}'");
mysql_query("DELETE FROM `send_money_history` WHERE `deleted_by_sender`='1' AND `deleted_by_recipient`='1'");
}

$query = mysql_query("SELECT * FROM `send_money_history` WHERE (`sender_uid`='{$uid}' AND `deleted_by_sender`='0') OR (`recipient_uid`='{$uid}' AND `deleted_by_recipient`='0') ORDER BY `timestamp` DESC");
 
if (mysql_num_rows($query) == 0) {
echo '<div class="info">You have no history!</div>';
} else {
while ($row = mysql_fetch_assoc($query)) {
if ($row['sender_uid'] == $uid) {
echo '<table><tr><td>You sent $' . number_format($row['amount']) . ' to ' . cleanHtml($row['recipient']) . '.<br /></td></tr></table>';
} else {
echo '<table><tr><td>You got $' . number_format($row['amount']) . ' from ' . cleanHtml($row['sender']) . '.<br /></td></tr></table>';
}
}
}
echo '
</div>
</div>
';

include '_footer.php';
?>