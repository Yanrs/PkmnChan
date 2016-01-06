<?php
require_once 'config.php';
require_once 'functions.php';
require_once 'pagination.class.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$gid   = (int) (isset($_GET['id']) ? $_GET['id'] : $_SESSION['userid']);
$query = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$gid}' LIMIT 1");

if(mysql_num_rows($query) == 0) {
	redirect('view_box.php');
}

$boxUsername = mysql_fetch_assoc($query);
$boxUsername = $boxUsername['username'];

include '_header.php';

$headerText = isset($_GET['id']) ? $boxUsername.'s Pokemon' : 'Your Pokemon' ;
printHeader($headerText);

$sorts = array
(
    1 => ' ORDER BY `name` ASC',
    2 => ' ORDER BY `name` DESC',
    3 => ' ORDER BY `exp` ASC',
    4 => ' ORDER BY `exp` DESC'
);

$search    = isset($_GET['search']) ? $_GET['search'] : '' ;

$sort      = $_GET['sort'];
$sortKey   = isset($sort) && in_array($sort, array_keys($sorts)) ? $sort : 1 ;
$orderSql  = $sorts[$sortKey];

$searchSql = '';

if (!empty($search)) {
	$searchSqlSafe  = cleanSql($search);
	$searchHtmlSafe = cleanHtml($search);
	$searchSql      = " AND `name` LIKE '%{$searchSqlSafe}%' ";
}

$countQuery = mysql_query("SELECT `id` FROM `user_pokemon` WHERE `uid`='{$gid}' {$searchSql}");
$numRows    = mysql_num_rows($countQuery);

$pagination = new Pagination($numRows);

if (!empty($search)) {
	$pagination->addQueryStringVar('search', $search);
}

if (isset($_GET['id'])) {
	$pagination->addQueryStringVar('id', (int) $_GET['id']);
}

if ($sortKey != 1) {
	$pagination->addQueryStringVar('sort', $sortKey);
}

$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `uid`='{$gid}' {$searchSql} {$orderSql} LIMIT {$pagination->itemsPerPage} OFFSET {$pagination->startItem}");

	

echo '	
	<form method="get" style="text-align: center; margin: 20px 0px;">
';
if (isset($_GET['id'])) {
	$uuid = (int) $_GET['id'];
	echo '<input type="hidden" name="id" value="'.$uuid.'" />';
}
echo '
		Search For: <input type="text" name="search" value="'.$searchHtmlSafe.'" />
		<input type="submit" value="Search" />
	</form>
';

if (mysql_num_rows($query) == 0) {
	echo '<div class="info">Could not find any pokemon.</div>';
} else {
	$qs = '';
	
	if (!empty($search)) {
		$qs .= '&amp;search=' . urlencode($search);
	}
	
	if (!empty($_GET['page'])) {
		$qs .= '&amp;page=' . (int) $_GET['page'];
	}
	
	if (isset($_GET['id'])) {
		$qs .= '&amp;id=' . (int) $_GET['id'];
	}
	
	$nameOrder = $_GET['sort'] == 1 ? 2 : 1 ;
	$expOrder  = $_GET['sort'] == 4 ? 3 : 4 ;
	
	echo '		
		<table class="pretty-table">
			<tr>
				<th width=25%><a href="view_box.php?sort='.$nameOrder.'">Pokemon</a></th>
				<th  width=25%>Level</th>
				<th  width=25%><a href="view_box.php?sort='.$expOrder.'">Exp</a></th>
				<th  width=25%>Moves</th>
				
	';
	
	echo isset($_GET['id']) ? '' : '<th>Options</th>' ;
	
	echo '
			</tr>
	';

	$teamIds = getUserTeamIds($uid);
	$genders = array('1' => 'Male', '2' => 'Female', '3' => 'Genderless');
	while ($pokemon = mysql_fetch_assoc($query)) {

		if (!isset($_GET['id'])) {
			if (in_array($pokemon['id'], $teamIds)) {
				$tradeHtml   = '<strike>Put&nbsp;Up&nbsp;For&nbsp;Trade</strike><br />';
				$sellHtml    = '<strike>Put&nbsp;Up&nbsp;For&nbsp;Sale</strike><br />';
				$teamHtml    = '<strike>Put&nbsp;In&nbsp;My&nbsp;Team</strike><br />';
				$releaseHtml = '<strike>Release&nbsp;Pokemon</strike><br />';
				$auctionHtml = '<strike>Auction&nbsp;Pokemon</strike><br />';
			} else {
				$tradeHtml   = '<a href="trade.php?a=puft_process&amp;id='.$pokemon['id'].'">Trade&nbsp;Pokemon</a><br />';
				$sellHtml    = '<a href="sell_pokemon.php?p=sell2&amp;id='.$pokemon['id'].'">Sell&nbsp;Pokemon</a><br />';
				$teamHtml    = '<a href="change_team.php?id='.$pokemon['id'].'">Put&nbsp;In&nbsp;My&nbsp;Team</a><br />';
				$releaseHtml = '<a href="release.php?id='.$pokemon['id'].'">Release&nbsp;Pokemon</a><br />';
				$auctionHtml = '<a href="auction_start.php?id='.$pokemon['id'].'">Auction&nbsp;Pokemon</a><br />';
			}
		}
		
		$genderLine = '<img src="images/gender/'.$pokemon['gender'].'.png" alt="'.$genders[$pokemon['gender']].'" title="'.$genders[$pokemon['gender']].'">';
		
		echo '
			<tr>
				<td>
					<a href="pinfo.php?id='.$pokemon['id'].'"><img src="images/pokemon/' . $pokemon['name'] . '.png" alt="' . $pokemon['name'] . '" /></a><br />
					' . $pokemon['name'] . '&nbsp;'.$genderLine.'
				</td>
				<td>' . number_format($pokemon['level']) . '</td>
				<td>' . number_format($pokemon['exp']) . '</td>
				<td>	
					' . $pokemon['move1'] . '<br />
					' . $pokemon['move2'] . '<br />
					' . $pokemon['move3'] . '<br />
					' . $pokemon['move4'] . '<br />
				</td>
		';
		
		if (!isset($_GET['id'])) {
			echo '
				<td>
					<a href="evolve.php?id='.$pokemon['id'].'">Evolve</a><br />
					<a href="change_attacks.php?id='.$pokemon['id'].'">Change&nbsp;Attacks</a><br /><br />
					'.$teamHtml.'
					'.$tradeHtml.'
					'.$sellHtml.'
					'.$releaseHtml.'
					'.$auctionHtml.'
				</td>
			';
		}
		
		echo '
			</tr>
		';
	}
	echo '</table>';

	$pagination->echoPagination();
}


include '_footer.php';
?>