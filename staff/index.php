<?php
require_once '../config.php';
require_once '../functions.php';
require_once 'admin_functions.php';

if (!isAdmin()) {
    die('Only admins can access this page.');
}

include '_header.php';
echoHeader('Staff Panel');

$filename = 'admin_messages_nkjdngksndfgermekmrz.txt';
if (isset($_POST['message'])) {
    $fh = fopen($filename, 'w');
    fwrite($fh, $_POST['message']);
    fclose($fh);
    echo '<div class="notice">Message has been saved.</div>';
}

echo '
	<div class="center-text">
		Admin List: '.getAdminProfileList().'<br /><br />

        <form method="post">
            <textarea name="message" cols="50" rows="10">'.file_get_contents($filename).'</textarea><br /><br />
            <input type="submit" value="Save" />
        </form>
	</div>
';

include '_footer.php';
?>