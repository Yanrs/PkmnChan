<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'pagination.class.php';

if (!isLoggedIn()) {
	redirect('index.php');
}

$sorts = array
(
    1 => ' ORDER BY `id` ASC',
    2 => ' ORDER BY `id` DESC',
    3 => ' ORDER BY `username` ASC',
    4 => ' ORDER BY `username` DESC',
    5 => ' ORDER BY `lastseen` ASC',
    6 => ' ORDER BY `lastseen` DESC'
);

$search    = isset($_GET['search']) ? $_GET['search'] : '' ;
$searchSql = '';

$sort      = $_GET['sort'];
$sortKey   = isset($sort) && in_array($sort, array_keys($sorts)) ? $sort : 1 ;
$orderSql  = $sorts[$sortKey];

if (!empty($search)) {
	$searchSqlSafe  = cleanSql($search);
	$searchHtmlSafe = cleanHtml($search);
	$searchSql      = " WHERE `username` LIKE '%{$searchSqlSafe}%' ";
}

$countQuery = mysql_query("SELECT `id` FROM `users` {$searchSql}");
$numRows    = mysql_num_rows($countQuery);
$pagination = new Pagination($numRows);

if (!empty($search)) {
	$pagination->addQueryStringVar('search', $_GET['search']);
}
include '_header.php';
printHeader('Members List');

$query = mysql_query("SELECT * FROM `users` {$searchSql} {$orderSql} LIMIT {$pagination->itemsPerPage} OFFSET {$pagination->startItem}");



	$qs = '';
	if (!empty($search)) {
		$qs .= '&amp;search=' . urlencode($search);
	}
	
	$usernameOrder  = $_GET['sort'] == 3 ? 5 : 3 ;
	$lastSeenOrder  = $_GET['sort'] == 6 ? 5 : 6 ;
	$idOrder        = $_GET['sort'] == 2 ? 1 : 2 ;

echo '
		<form method="get" action="" style="text-align: center; margin: 20px 0px;">
			Search For: <input type="text" name="search" value="'.$searchHtmlSafe.'" /> <input type="submit" value="Search" />
		</form>
';

if (mysql_num_rows($query) == 0) {
	echo '<div class="info">Could not find any users.</div>';
} else {
	echo '		
	<div class="franks">
		<table class="pretty-table">
			<tr>
				<th><a href="?'.$qs.'&amp;sort='.$idOrder.'">ID</a></th>
				<th><a href="?'.$qs.'&amp;sort='.$usernameOrder.'">Username</a></th>
				<th><a href="?'.$qs.'&amp;sort='.$lastSeenOrder.'">Last Seen</a></th>
				<th>Options</th>
			</tr>
	';
	while($row = mysql_fetch_assoc($query))
	{
		$row = cleanHtml($row);
		$banText = $row['banned'] == 1 ? '&nbsp;[<span style="color: #FF0000; font-size: 8pt;">BANNED</span>]' : '' ;
		$lastseen = $row['lastseen'] == 0 ? 'Never' : secondsToTimeSince(time()-$row['lastseen']) ;
		echo '
			<tr>
				<td>'.$row['id'].'</td>
				<td><a href="profile.php?id='.$row['id'].'">'.$row['username'].'</a>'.$banText.'</td>
				<td>'.$lastseen.'</td>
				<td><a href="battle_user.php?id='.$row['id'].'">Battle User</a></td>
			</tr>
		';
	}
	
	echo '</table></div>';
	
	$pagination->echoPagination();
}


include '_footer.php';
?>