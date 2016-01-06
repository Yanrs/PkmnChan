<?php
require_once 'config.php';
require_once 'functions.php';

$_GET['id'] = abs((int) $_GET['id']);
$_GET['id'] = mysql_real_escape_string($_GET['id']);

$pinfo1 = mysql_query("SELECT * FROM `pokedex` WHERE `id`='".$_GET['id']."'");
$p_class = mysql_fetch_object($pinfo1);


if($p_class->id == ""){
echo "<div class=notice>Please select a pokemon</div>";
include '_footer.php';
die();
}
if($p_class->gender == "1"){$gender="Male";}
if($p_class->gender == "2"){$gender="Female";}
if($p_class->gender == "0"){$gender="Genderless";}

if($p_class->evolution == ""){$p_class->evolution="0";}


?>

<?php
echo'
<center>
   <table class="pretty-table">
     <tr>
       <td>
         
               <img src="images/pokemon/'.$p_class->name.'.png">
<br><img src="images/dex/'.$p_class->type1.'.png">&nbsp;<img src="images/dex/'.$p_class->type2.'.png">
	       
       </td>
    <td><table class="pretty-table">
	  
	            <tr>
	              <th>HP</th>
	              <th>Attack</th>
	              <th>Defense</th>
	            </tr>
		    <tr>
		      <td>'.$p_class->hp.'</td>
		      <td>'.$p_class->attack.'</td>
		      <td>'.$p_class->def.'</td>
		    </tr>
		    <tr>
		      <th>Sp. Attack</th>
		      <th> Sp. Defense</th>
		      <th>Speed</th>
		    </tr>
		    <tr>
		      <td>'.$p_class->spattack.'</td>
		      <td>'.$p_class->spdef.'</td>
		      <td>'.$p_class->speed.'</td>
		    </tr>
	          </table>
	       </div>
	     </td>
	   </tr>
	 </table>
       </td>
     </tr>
   </table>
<br>
	
		<table class="pretty-table">
		  <tr>
		    <td>
		         
		      	   <div><b>'.$p_class->name.'</b></div>
		           <img src="images/pokemon/'.$p_class->name.'.png>"
		            
		    </td>
		    <td>Evolves into<br><br><b>Requirements:</b><br>Level: <b>'.$p_class->level.'</b><br>Item: </td>
		    <td>  
		      
		      	   <div><b>'.$p_class->evolution.'</b></div>
		           <img src="images/pokemon/'.$p_class->evolution.'.png">
		    </td>
		  </tr>
		</table>
</center>';

// asdd stuff
// rarity list
// added 5/26/2013


$name = cleanSql($p_class->name);

$query = mysql_query("SELECT `name`, `gender`, count(`id`) as amount FROM `user_pokemon` WHERE `name` LIKE '%{$name}%' GROUP BY `name`, `gender`");
$pokeArray = array();
$genderArray = array('0'=>'genderless', '1'=>'male', '2'=>'female');

while ($r = mysql_fetch_assoc($query)) {
	$pokeArray[ $r['name'] ][ $genderArray[ $r['gender'] ] ] = $r['amount'];
}

echo '
	<br />
	<table class="pretty-table">
		<tr>
			<th>Name</th>
			<th>Male</th>
			<th>Female</th>
			<th>Genderless</th>
		</tr>
';

foreach ($pokeArray as $pokeName => $genderAmount) {

	$genderAmount['male'] = isset($genderAmount['male']) ? $genderAmount['male'] : 0 ;
	$genderAmount['female'] = isset($genderAmount['female']) ? $genderAmount['female'] : 0 ;
	$genderAmount['genderless'] = isset($genderAmount['genderless']) ? $genderAmount['genderless'] : 0 ;
	
	echo '
		<tr>
			<td>' . $pokeName . '</td>
			<td>' . $genderAmount['male'] . '</td>
			<td>' . $genderAmount['female'] . '</td>
			<td>' . $genderAmount['genderless'] . '</td>
		</tr>
	';
}

echo '
	</table><br />
';
?>