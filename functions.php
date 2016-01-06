<?php

function printHeader($str) {
    echo '<div class="header"><p>'.$str.'</p></div>';
    echo '<div class="ads-two" style="padding: 10px 0; text-align: center;">
	    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	    <!-- Pokemon Thunder RPG 2 -->
	    <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-5362441234156231" data-ad-slot="1696165306"></ins>
	    <script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>
    </div>
	<div style="display:none;" class="ads-two-mobile">
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- mobile -->
	<ins class="adsbygoogle"
		 style="display:inline-block;width:320px;height:100px"
		 data-ad-client="ca-pub-5362441234156231"
		 data-ad-slot="2504048502"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
	</div>
	';
}

function getConfigValue($name) {
	$query = mysql_query("SELECT `value` FROM `config` WHERE `name`='{$name}'");
	
	if ($query) {
		$row   = mysql_fetch_assoc($query);
		return $row['value'];
	}
	
	return false;
}

function setConfigValue($name, $value) {
	$query = mysql_query("UPDATE `config` SET `value`='{$value}' WHERE `name`='{$name}'");
	return $query ? true : false ;
}

function cleanSql($input) {
	if (is_array($input)) {
		foreach ($input as $k => $v) {
			$output[$k] = cleanSql($v);
		}
	} else {
		$output = (string) $input;
		$output = mysql_real_escape_string($output);
	}

	return $output;
}

function giveUserPokemonByName($uid, $pokeName, $level = 5, $prefix = '') {

	$pokeName = cleanSql($pokeName);
	$query = mysql_query("SELECT * FROM `pokemon` WHERE `name`='{$pokeName}' LIMIT 1");
	
	if (mysql_num_rows($query) == 0) {
		return false;
		/*$poke = array(
			'name' => $pokeName,
			'move1' => 'Bite',
			'move2' => 'Bite',
			'move3' => 'Bite',
			'move4' => 'Bite',
		);*/
	} else {
		$poke = mysql_fetch_assoc($query);
	}
	
	$exp  = levelToExp($level);
	
	$pokeId = giveUserPokemon($uid, $prefix . $poke['name'], $level, $exp, $poke['move1'], $poke['move2'], $poke['move3'], $poke['move4']);
	
	return $pokeId;
}

function giveUserPokemon($uid, $name, $level, $exp, $move1, $move2, $move3, $move4) {

	$uid   = (int) $uid;
	$level = (int) $level;
	$exp   = (int) $exp;
	$name  = cleanSql($name);
	$move1 = cleanSql($move1);
	$move2 = cleanSql($move2);
	$move3 = cleanSql($move3);
	$move4 = cleanSql($move4);
	$gender = rand(0, 2);
	
	mysql_query("
		INSERT INTO `user_pokemon` (
			`uid`, `name`, `level`, `exp`, `move1`, `move2`, `move3`, `move4`, `gender`
		) VALUES (
			'{$uid}', '{$name}', '{$level}', '{$exp}', '{$move1}', '{$move2}', '{$move3}', '{$move4}', '{$gender}'
		)
	");
	$pokeId = mysql_insert_id();
	
	$query = mysql_query("SELECT `poke1`,`poke2`,`poke3`,`poke4`,`poke5`,`poke6` FROM `users` WHERE `id`='{$uid}'");
	if (mysql_num_rows($query) == 1) {
	
        $pokeIds = mysql_fetch_assoc($query);
	    for ($i=1; $i<=6; $i++) {
            if ($pokeIds['poke'.$i] == '0') {
                mysql_query("UPDATE `users` SET `poke{$i}`='{$pokeId}' WHERE `id`='{$uid}'");
                break;
            }
	    }
	}
	
	return $pokeId;
}

function secondsToTimeSince($seconds) {
	$seconds    = (int) $seconds;
	$timeString = '';
	
	$days = floor($seconds / (60*60*24));
	$dStr = $days == 1 ? ' day ' : ' days ' ;
	
	$hours = floor(($seconds / (60*60)) % 24);
	$hStr  = $hours == 1 ? ' hour ' : ' hours ' ;
	
	$mins = floor(($seconds / 60) % 60);
	$mStr = $mins == 1 ? ' minute ' : ' minutes ' ;
	
	$seconds = $seconds % 60;
	
	$timeString .= $days  > 0 ? $days  . $dStr : '' ;
	$timeString .= $hours > 0 ? $hours . $hStr : '' ;
	$timeString .= $mins  > 0 ? $mins  . $mStr : '' ;
	$timeString .= $seconds . ' seconds';
	
	return $timeString; 
}

function Send_Event ($id, $text){

	$timesent = time();
	$result= mysql_query("INSERT INTO `logs` (`to`, `timesent`, `text`)".
						 "VALUES ('$id', '$timesent', '$text')");

	mysql_query("UPDATE `users` SET `events` = `events` + 1 WHERE `id`='$id'");
}

function logs($id, $text){

	$timesent = time();
	/*$result= mysql_query("INSERT INTO `logs` (`to`, `timesent`, `text`)".
						 "VALUES ('$id', '$timesent', '$text')");*/
}

function maxHp($name, $level) {

	//if ($_SESSION['admin'] == true || $_SESSION['username'] == 'DarkMaster') {
		$prefixes = array('Snow', 'Shiny', 'Halloween', 'Rainbow', 'Helios', 'Possion', 'Shadow');
		
		$nName = trim(str_replace($prefixes, '', $name));
		
		$query = mysql_query("SELECT `hp` FROM `pokedex` WHERE `name`='{$nName}'");
		if (mysql_num_rows($query) == 1) {
			$pdexRow = mysql_fetch_assoc($query);
			$hp = ((($pdexRow['hp']*2)+110)/100)*$level;
			
			if (strpos($name, 'Shiny ') === 0) {
				$hp = $hp+(($hp/100)*10);
			}	
			
			/*if (strpos($name, 'Helios ') === 0) {
				$hp = $hp+(($hp/100)*20);
			}*/	
				
			if (strpos($name, 'Possion ') === 0) {
				$hp = $hp+(($hp/100)*25);
			}	
			 
			/*if (strpos($name, 'Rainbow ') === 0) {
				$hp = $hp+(($hp/100)*15);
			}*/
			
			return round($hp);
		}
		return $level*3;
	//}
	/*
	if (strpos($name, 'Shiny ') === 0) {
		return $level*5;
	}	
	
	if (strpos($name, 'Helios ') === 0) {
		return $level*6;
	}	
		
	if (strpos($name, 'Possion ') === 0) {
		return $level*6;
	}	
	 
	if (strpos($name, 'Rainbow ') === 0) {
		return $level*7;
	}
	
	return $level*4;
	*/
}
                
function logActivity($message, $uid, $image = '') {
	$uid     = (int) $uid;
	$message = cleanSql($message);
	$image   = cleanSql($image);
	$time    = time();

	//mysql_query("INSERT INTO `activity` (`message`, `uid`, `time`, `image`) VALUES ('{$message}', '{$uid}', '{$time}', '{$image}')");
}

function getUserTeamIds($uid) {
	$uid = (int) $uid;
	$query = mysql_query("SELECT `poke1`,`poke2`,`poke3`,`poke4`,`poke5`,`poke6` FROM `users` WHERE `id`='{$uid}' LIMIT 1");

	if (mysql_num_rows($query) == 0) {
		return false;
	}
	
	return mysql_fetch_assoc($query);
}

function getUserPokemon($pid) {
	$pid = (int) $pid;
	$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$pid}' LIMIT 1");

	if (mysql_num_rows($query) == 0) {
		return false;
	}

	return mysql_fetch_assoc($query);
}

function updateUserMoney($uid, $money) {
	$uid   = (int) $uid;
	$money = (int) $money;

	mysql_query("UPDATE `users` SET `money`={$money} WHERE `id`='{$uid}' LIMIT 1");

	return true;
}

function getUserMoney($uid) {
	$uid = (int) $uid;
	$query = mysql_query("SELECT `money` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
	$username = mysql_query("SELECT `username` FROM `users` WHERE `id`='{$uid}' LIMIT 1");

	if (mysql_num_rows($query) == 0) {
		return false;
	}

	$userMoney = mysql_fetch_assoc($query);

	return (int) $userMoney['money'];
}

function cleanHtml($input) {
	if (is_array($input)) {
		foreach ($input as $k => $v) {
			$output[$k] = cleanHtml($v);
		}
	} else {
		$output = (string) $input;
		$output = htmlentities($output, ENT_QUOTES, 'UTF-8');
	}

	return $output;
}

function isLoggedIn() {
	return isset($_SESSION['userid']);
}

function redirect($location) {
	header('Location: '.$location.'');
	die();
}

function isAdmin() {
	return (bool) (isset($_SESSION['admin']) && $_SESSION['admin'] == 1);
}

function isMod() {
	return (bool) (isset($_SESSION['mod']) && $_SESSION['mod'] == 1);
}

function expToLevel($exp) {
	for ($i = 10000; $i > 0; $i--) {
		if ($exp >= levelToExp($i)) {
			return $i;
		}
	}
	
	return 0;
}

function levelToExp($level) {
	return ($level * $level) * 10;
	//return floor(pow($level,1*3));
}

function cellsToRowsProfile($cells, $numColumns) {
	$tableRows = '';
	
	for ( $i = 0; $i < count($cells); $i += $numColumns ) {
		$tableRows .= '<tr>';
		
		for ($j = $i; $j < $i + $numColumns; $j++) {
			if ( isset($cells[$j]) ) {
				$tableRows .= '<td><div class="box">'.$cells[$j].'</div></td>';
			}
		}
		
		$tableRows .= '</tr>';
	}
	
	return $tableRows;
}

function cellsToRowsBadges($cells, $numColumns) {
	$tableRows = '';
	
	for ( $i = 0; $i < count($cells); $i += $numColumns ) {
		$tableRows .= '';
		
		for ($j = $i; $j < $i + $numColumns; $j++) {
			if ( isset($cells[$j]) ) {
				$tableRows .=  $cells[$j] ;
			}
		}
		
		$tableRows .= '<br>';
	}
	
	return $tableRows;
}

function cellsToRows($cells, $numColumns) {
	$tableRows = '';
	
	for ( $i = 0; $i < count($cells); $i += $numColumns ) {
		$tableRows .= '<tr>';
		
		for ($j = $i; $j < $i + $numColumns; $j++) {
			if ( isset($cells[$j]) ) {
				$tableRows .= '<td>'.$cells[$j].'</td>';
			}
		}
		
		$tableRows .= '</tr>';
	}
	
	return $tableRows;
}

function cellsToRowss($cells, $numColumns) {
	$tableColumns = '';
	
	for ( $i = 0; $i < count($cells); $i += $numColumns ) {
	
		$tableColumns .= '<td>';
		
		for ($j=$i; $j<$i+$numColumns; $j++) {
			if ( isset($cells[$j]) ) {
				$tableColumns .=$cells[$j];
			}
		}
		
		$tableColumns .= '</td>';
	}
	
	return $tableColumns;
}

function convert_bbcodes($t) { 
  $search = array_keys( $GLOBALS['bb_codes'] );
  $t = str_replace( $search, $GLOBALS['bb_codes'], $t );
  
  return $t;
}

function RecentEvents ($id,$text) {
  $sent = time();
  $result= mysql_query("INSERT INTO `events` (`to`, `sent`, `eventmsg`)"."VALUES ('$id', '$sent', '$text')");
  
  mysql_query("UPDATE `users` SET `recent` = `events` + 1 WHERE `id`='$id'");
}

function getUserToken($uid) {
	$uid = (int) $uid;
	$query = mysql_query("SELECT `token` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
	
	if (mysql_num_rows($query) == 0) { 		
		return false; 	
	}
	
	$userToken = mysql_fetch_assoc($query);

	return (int) $userToken['token'];
}

function updateUserToken($uid, $token) {
	$uid   = (int) $uid;
	$Token = (int) $token;
	
	mysql_query("UPDATE `users` SET `token`={$token} WHERE `id`='{$uid}' LIMIT 1");
	
	return true;
}

function getUserShard($uid) {
	$uid = (int) $uid;
	$query = mysql_query("SELECT `red_shard` FROM `users` WHERE `id`='{$uid}' LIMIT 1");
	
	if (mysql_num_rows($query) == 0) { 		
		return false; 	
	}
	
	$userShard = mysql_fetch_assoc($query);

	return (int) $userShard['red_shard'];
}

function updateUserShard($uid, $Shard) {
	$uid   = (int) $uid;
	$Shard= (int) $Shard;
	
	mysql_query("UPDATE `users` SET `red_shard`={$Shard} WHERE `id`='{$uid}' LIMIT 1");
	
	return true;
}

?>