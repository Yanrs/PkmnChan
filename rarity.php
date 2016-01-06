<?php
include 'config.php'; 
include 'functions.php'; 
include '_header.php';

// If the page wasn't set, lets set $page to number 1 for the first page
if($page == ""){
	
	$page = "1";
	
}else{
	
	// If page is set, let's get it
	$page = $_GET['page'];
	
}

// Now lets get all messages from your database
$sql = "SELECT * FROM user_pokemon";
$query = mysql_query($sql);

// Lets count all messages
$num = mysql_num_rows($query);

// Lets set how many messages we want to display
$per_page = "10";

// Now we must calculate the last page
$last_page = ceil($num/$per_page);

// And set the first page
$first_page = "1";

// Here we are making the "First page" link
echo "<a href='?page=".$first_page."'>First page</a> ";

// If page is 1 then remove link from "Previous" word
if($page == $first_page){
	
	echo "Previous ";
	
}else{
	
	if(!isset($page)){
		
		echo "Previous ";
		
	}else{
		
		// But if page is set and it's not 1.. Lets add link to previous word to take us back by one page
		$previous = $page-1;
		echo "<a href='?page=".$previous."'>Previous</a> ";
	
	}
	
}

// If the page is last page.. lets remove "Next" link
if($page == $last_page){
	
	echo "Next ";	
	
}else{
	
	// If page is not set or it is set and it's not the last page.. lets add link to this word so we can go to the next page
	if(!isset($page)){
		
		$next = $first_page+1;
		echo "<a href='?page=".$next."'>Next</a> ";
		
	}else{
	
		$next = $page+1;
		echo "<a href='?page=".$next."'>Next</a> ";
	
	}
	
}

// And now lets add the "Last page" link
echo "<a href='?page=".$last_page."'>Last page</a>";

// Math.. It gets us the start number of message that will be displayed
$start = ($page-1)*$per_page;

// Now lets set the limit for our query
$limit = "LIMIT $start, $per_page";

$query = mysql_query("SELECT `name`, `gender`, count(`id`) as amount FROM `user_pokemon` GROUP BY `name`, `gender` $limit");
$pokeArray = array();
$genderArray = array('1'=>'male', '2'=>'female');

while ($r = mysql_fetch_assoc($query)) {
$pokeArray[ $r['name'] ][ $genderArray[ $r['gender'] ] ] = $r['amount'];
}
echo '
<table>
<tr>
<th>Name</th>
<th>Male</th>
<th>Female</th>
</tr>
';

foreach ($pokeArray as $pokeName => $genderAmount) {

$genderAmount['male'] = isset($genderAmount['male']) ? $genderAmount['male'] : 0 ;
$genderAmount['female'] = isset($genderAmount['female']) ? $genderAmount['female'] : 0 ;

echo '
<tr>
<td>' . $pokeName . '</td>
<td>' . $genderAmount['male'] . '</td>
<td>' . $genderAmount['female'] . '</td>
</tr>
';

}




echo '
</table>
';
include '_footer.php';
?>