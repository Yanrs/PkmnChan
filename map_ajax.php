<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}

$map = (int) $_GET['map'];
$uid = (int) $_SESSION['userid'];

if (!isset($_SESSION['catchLegneds'])) {
	require_once 'gym_functions.php';
	$_SESSION['catchLegneds'] = canCatchLegends($uid);
}

switch ($map) {
	case '1':
	case '2':
	case '3':
	case '4':
		// grass 
		$wildPokemon = array(
			'Venipede', 'Togepi', 'Pidgey', 'Caterpie', 'Weedle', 'Nidoran (f)', 'Nidoran (m)', 'Exeggcute','Happiny',
			 'Foongus', 'Karrablast', 'Kricketot', 'Combee', 'Eevee', 'Sewaddle', 'Caterpie', 'Snivy', 'Caterpie', 'Ditto', 'Mawile', 'Seviper', 'Zangoose', 'Zigzagoon', 'Poochyena'
		);
		
		$legends = array(
			'Rayquaza', 'Azelf', 'Mesprit', 'Shaymin', 'Deoxys', 'Virizion', 'Celebi',
			'Mew', 'Tornadus', 'Regigigas', 'Venasaur (Mega)'
		
			
		);
	break;
	
	case '5':
	case '6':
	case '7':
		//water
		$wildPokemon = array(
			'Mudkip', 'Shellder', 'Seel', 'Oshawott', 'Tentacool', 'Poliwag', 'Psyduck', 'Squirtle', 'Horsea',
			'Goldeen', 'Staryu', 'Magikarp', 'Chinchou', 'Krabby', 'Azurill',
			'Remoraid', 'Totodile', 'Totodile', 'Wingull', 'Tympole', 'Buizel', 'Cubchoo',
			'Panpour', 'Wailmer', 'Feebas', 'Staryu'
		);
		
		$legends = array(
			'Keldeo', 'Palkia', 'Kyogre', 'Suicune', 'Phione', 'Manaphy'
		);
	break;
	
	case '8':
	case '9':
		//rock
		$wildPokemon = array(
			'Sawk', 'Throh', 'Timburr', 'Gible', 'Onix', 'Geodude', 'Omanyte', 'Kabuto', 'Aerodactyl', 'Larvitar', 'Archen', 'Lunatone', 'Shieldon', 
			'Solrock', 'Lileep', 'Anorith',  'Charmander',  'Spearow', 'Ekans', 'Igglybuff',
			'Zubat', 'Meowth', 'Mankey',  'Gastly', 'Cubone', 'Rhyhorn', 'Kangaskhan',  'Shuckle',
			'Riolu', 'Roggenrola', 'Pawniard', 'Phanpy',  'Golett', 'Scraggy', 'Durant', 'Cleffa',
			'Mawile', 'Trapinch', 'Shieldon', 'Sandile', 'Druddigon', 'Aron', 'Machop', 'Diglett'
		);
		
		$legends = array(
			'Heatran', 'Reshiram', 'Moltres', 
			'Giratina', 'Groudon', 'Regice', 'Regirock', 'Registeel', 'Jirachi', 'Mew', 'Mewtwo', 'Ho-oh',
			
		);
	break;
	
	case '10':
	case '11':
		//ice
		$wildPokemon = array(
			'Jynx', 'Lapras', 'Cloyster', 'Swinub', 'Delibird', 'Smoochum', 'Spheal', 'Froslass',
			'Sneasel', 'Wynaut', 'Vanillite', 'Jynx', 'Seel', 'Bronzor', 'Smoochum', 'Nincada', 
		);
		
		$legends = array(
			'Dialga', 'Palkia', 'Landorus', 'Victini', 'Terrakion', 'Cobalion', 'Landorus (Therian)' ,
			'Arceus', 'Darkmuj'
		);
	break;
	
	// fire
	case '12':
	case '13':
	case '14':
		$wildPokemon = array(
			'Vulpix',  'Growlithe', 'Ponyta', 'Magmar', 'Flareon', 'Slugma', 'Houndour', 'Numel', 'Torkoal',
			'Pansear', 'Darumaka', 'Heatmor', 'Larvesta', 'Chimchar', 'Tepig', 'Torchic', 'Charmander' 
		
			
		);
		
		$legends = array(
			'Moltres', 'Entei', 'Ho-oh', 'Heatran', 'Reshiram', 'Lightsor'
		);
	break;
	
	case '15':
		//ghost
		$wildPokemon = array(
			'Gastly', 'Golurk', 'Misdreavus', 'Shuppet', 'Cofagrigus',
			'Drifloon', 'Litwick', 'Frillish', 'Duskull', 'Spiritomb'
			
		);
		
		$legends = array(
			'Darkrai', 'Giratina', 'Darkcune', 'Kyurem (White)', 'Giratina (Origin)', 'Gengar (Mega)', 'Darksor'
		);
	break;
	
	// electric
	case '16':
	case '17':
		$wildPokemon = array(
			'Pikachu', 'Magnemite', 'Voltorb', 'Electabuzz', 'Jolteon', 'Mareep', 'Electrike',
			'Plusle', 'Minun', 'Shinx', 'Blitzle', 'Emolga', 'Stunfisk', 'Magnimite', 'Voltorb', 'Pachirisu'
		);
		
		$legends = array(
			'Zapdos', 'Rotom', 'Thundurus', 'Zekrom', 'Raikou', 'Solarsor'
		);
	break;
	
	
	case '18':
		// Flying Throne
		$wildPokemon = array(
			 'Scyther', 'Pidgey', 'Gligar', 'Drifloon', 'Archen', 'Emolga', 
			 'Rufflet', 'Woobat', 'Mothim', 'Combee', 'Delibird', 'Swablu', 'Yanma',
			 'Starly', 'Pidove', 'Zubat', 'Hoothoot',
			 
		);
		
		$legends = array(
		    'Azelf', 'Thundurus', 'Landorus', 'Tornadus', 'Celebi',
			'Mew', 'Tornadus', 'Thundurus', 'Rayquaza', 'Latias', 'Latios', 'Moltres'
		);
	break;
    
	case '19':
		// Psychic Throne
		$wildPokemon = array(
			 'Abra', 'Slowpoke', 'Drowzee', 'Mr. Mime', 'Jynx', 'Natu', 
			 'Espeon', 'Woobat', 'Wobbuffet', 'Girafarig', 'Ralts', 'Meditite', 'Spoink',
			 'Lunatone', 'Solrock', 'Baltoy', 'Chimecho', 'Beldum', 'Chingling', 'Bronzor',
			 'Munna', 'Gothita', 'Solosis', 'Elgyem'			 
		);
		
		$legends = array(
		    'Azelf', 'Mewtwo', 'Lugia', 'Celebi', 'Meloetta',
			'Mew', 'Latios', 'Uxie', 'Latias', 'Mesprit', 'Cresselia', 'Gardevoir (Mega)'
		);
	break;
	
	case '25':
		// Dragon Throne
		$wildPokemon = array(
			 'Dratini', 'Kingdra', 'Bagon', 'Gible', 'Axew', 'Deino'
		);
		
		$legends = array(
		    'Latias', 'Latios', 'Rayquaza', 'Lugia', 'Dialga', 'Palkia',
			'Giratina', 'Goomy', 'Goomy', 'Reshiram', 'Zekrom', 'Eeveon'
		);
	break;
		
	case '30':
		// Dark Throne
		$wildPokemon = array(
			 'Umbreon', 'Poochyena', 'Absol', 'Purrloin', 'Zorua', 'Murkrow', 'Sneasel',
			 'Scraggy', 'Pawniard', 'Nuzleaf', 'Cacturne', 'Spiritomb', 'Drapion',
			 'Sandile', 'Carvanha'
		);
		
		$legends = array(
		    'Inkay', 'Darkrai', 'Tyranitar (Mega)', 'Gyarados (Mega)'
		);
	break;
	
		case '31':
		// Bug Throne
		$wildPokemon = array(
			 'Caterpie', 'Pinsir', 'Pineco', 'Wurmple', 'Beedrill', 'Venonat', 'Scyther',
			 'Forretress', 'Shuckle', 'Heracross', 'Dustox', 'Surskit', 'Shedinja',
			 'Venipede', 'Beautifly'
		);
		
		$legends = array(
		    'Celebi', 'Scizor (Mega)', 'Heracross (Mega)'
		);
	break;


    default:
		die();
	break;
}

$x = (int) $_GET['x'];
$x = $x < 0 || $x > 25 ? 3 : $x;

$y = (int) $_GET['y'];
$y = $y < 0 || $y > 25 ? 3 : $y;

$time = time();

mysql_query("UPDATE `users` SET `map_num`='{$map}', `map_x`='{$x}', `map_y`='{$y}', `map_lastseen`='{$time}' WHERE `id`='{$uid}'");

if (mt_rand(1,2) == 2) {
	$type = mt_rand(1, 150) == 100 ? 'Shiny ' : '' ;
    $type = mt_rand(1, 150) == 130 ? 'Snow ' : $type ;
   $type = mt_rand(1, 300) == 150  ? 'Shadow ' : $type ;
	$isLegend = mt_rand(1, 300) == 1 ? true : false ;
	
	
	if ($isLegend && $_SESSION['catchLegneds']) {
		$randomPokemon = $legends[ mt_rand(0, count($legends)-1) ];
		$randomLevel = mt_rand(70, 90);
	} else {
		$randomPokemon = $wildPokemon[ mt_rand(0, count($wildPokemon)-1) ];
		$randomLevel = mt_rand(4, 11);
	}
	
	$query = mysql_query("SELECT * FROM `pokemon` WHERE `name`='{$randomPokemon}' LIMIT 1");
	
	if (mysql_num_rows($query) == 1) {
		$pokeRow = mysql_fetch_assoc($query);
		
		$_SESSION['battle']['opponent'][0]          = $pokeRow;
		$_SESSION['battle']['opponent'][0]['name']  = $type.$pokeRow['name'];
		$_SESSION['battle']['opponent'][0]['level'] = $randomLevel;
		$_SESSION['battle']['opponent'][0]['maxhp'] = maxHp($type.$pokeRow['name'], $randomLevel);
		$_SESSION['battle']['opponent'][0]['hp']    = maxHp($type.$pokeRow['name'], $randomLevel);
		$_SESSION['battle']['wild'] = true;
		$_SESSION['battle']['rebattlelink'] = '<a href="map.php?map='.$map.'">Back to map</a>';
		$_SESSION['battle']['onum'] = 0;
	
		$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `name`='{$type}{$randomPokemon}' AND `uid`='{$uid}' LIMIT 1");	
		
		$json = array('name'=>$type.$randomPokemon, 'level'=>$randomLevel, 'caught'=>mysql_num_rows($query));
		echo json_encode($json);
	} else {
		$fh = @fopen('map_errors.txt', 'a') or die();
		fwrite($fh, "Failed to find: '{$randomPokemon}' ". time() . PHP_EOL);
		fclose($fh);
	
		echo json_encode(array());
	}
} elseif (rand(1, 15) == 7) {
	$randMoney = rand(1, 100);
	mysql_query("UPDATE `users` SET `money`=`money`+{$randMoney} WHERE `id`='{$uid}'");
	
	$json = array('money'=>$randMoney);
	echo json_encode($json);
} else {

	$failMsg = array(
		'You did not find any Pokémon',
		'It ran away. Will try search more carefully',
		'I will try catch them all',
		'I think I saw one',
		'What was that behind me?! Oh, just my shadow.',
		'I think I saw one! oh its a rock',
		'You scared it off',
		'Is that a legendary Pokémon!? Oh, it\'s just a huge boulder.',
		'A wild Pokémon appeared, but Tushin\'s face scared it away!',
		'Boulders boulders , everywhere!',
		'What did I just step on?',
		'Darkmuj struck you with lightning!'.
		'It\'s freezing down there!',
		'A will Pokémon appeared! But ChatBot banned it.',
		'Why won\'t you just come out so I can catch you!?',
		'I\'m gonna catch ya!',
		'Where are all of the Pokémon they must be ninjas.',
		'Where are all the Pokémon, am I in the digimon universe?',
		'How long does this take I only want a single Pokémon.',
		'It\'s a Pokémon! oh its just a cloud.',
		'You\'re very good at hiding.',
		'Pokémon, Pokémon where can you be I\'m trying to find you OH there is one in that tree! Nope just kidding.',
		'You trip over your shoelace.',
		'Come on. All I want to do is catch you.',
		'Lightning struck me and I am freezing.'
	);

	$randFailMsg = array_rand($failMsg); 
	$showFailMsg = $failMsg[$randFailMsg];
	
	$json = array('failmsg'=>$showFailMsg);

	echo json_encode($json);
	//echo json_encode(array());
}

?>