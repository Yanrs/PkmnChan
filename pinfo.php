<?php
include 'config.php';
include 'functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

include '_header.php';
printHeader('Pokemon Information Page');


$pokeId = (int) $_GET['id'];
$userId = (int) $_SESSION['userid'];

$query = mysql_query("SELECT * FROM `user_pokemon` WHERE `id`='{$pokeId}'");

// check that the pokemon actually exists
if(mysql_num_rows($query) == 0){
    echo '<div class="error">The pokemon you were looking for was not found in our database.</div>';
    include '_footer.php';
    die();
}

$pokeRow   = mysql_fetch_assoc($query);
$query     = mysql_query("SELECT * FROM `users` WHERE `id`='{$pokeRow['uid']}' LIMIT 1");
$ownerRow  = mysql_fetch_assoc($query);

$query     = mysql_query("SELECT * FROM `user_items` WHERE `uid`='{$uid}' LIMIT 1");
$itemsRow  = mysql_fetch_assoc($query);


if(isset($_POST['update'])) {
    
    // check that this is their pokemon
    if ($ownerRow['id'] != $userId) {
        echo '<div class="error">This pokemon does not belong to you!</div>';
        
    // check that they have at least one rare candy
    } else if ($itemsRow['rare_candy'] <= 0) {
        echo '<div class="error">You do not have any rare candies!</div>';
    } else {
        $newLevel = $pokeRow['level']+1;
        $newExp   = levelToExp($newLevel);
        
        $pokeRow['level'] = $newLevel;
        $pokeRow['exp']   = $newExp;
        
        $itemsRow['rare_candy'] -= 1;
        
        mysql_query("UPDATE `user_pokemon` SET `level`='{$newLevel}', `exp`='{$newExp}' WHERE `id`='{$pokeId}'");
        mysql_query("UPDATE `user_items` SET `rare_candy`=`rare_candy`-1 WHERE `uid`='{$userId}'");
        
    	echo '<div class="notice">You used your rare candy and your pokemon\'s level was increased by 1.</div>';
    }
    
}

if($pokeRow['gender'] == "1"){$gender="Male";}
if($pokeRow['gender'] == "2"){$gender="Female";}
if($pokeRow['gender'] == "0"){$gender="Genderless";}

echo'
    <table class="pretty-table" style="width: 400px;">
        <tr>
            <th>Image</th>
        </tr>
        <tr>
            <td><img src="images/pokemon/'.$pokeRow['name'].'.png"><br>'.$pokeRow['name'].'</td>
        </tr>
    </table>
    <br />
    <table class="pretty-table" style="width: 400px;">
        <tr>
            <th>Owner</th>
            <td>'.cleanHtml($ownerRow['username']).'</td>
        </tr>
        <tr>
            <th>Level</th>
            <td>'.$pokeRow['level'].'</td>
        </tr>
        <tr>
            <th>EXP</th>
            <td>'.number_format($pokeRow['exp']).'</td>
        </tr>
        <tr>
            <th>Gender</th>
            <td>'.$gender.'</td>
        </tr>
        <tr>
            <th>Moves</th>
            <td>
                '.$pokeRow['move1'].'<br />
                '.$pokeRow['move2'].'<br />
                '.$pokeRow['move3'].'<br />
                '.$pokeRow['move4'].'<br />
            </td>
        </tr>
    </table>
    <br />
';


if ($pokeRow['uid'] == $userId){
    echo'
        <table class="pretty-table" style="width: 400px;">
            <tr>
                <th colspan=2>Use an item</th>
            </tr>
            <tr>
                <th>Rare Candy</th>
                <td>
                    You have x'.$itemsRow['rare_candy'].' rare candy!
                    <form method=post>
                        <input type="submit" name="update" value="Use"/>
                    </form>
                </td>
            </tr>
        </table>
    ';
}


include '_footer.php';
?>