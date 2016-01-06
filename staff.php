<?php
require_once 'config.php';
require_once 'functions.php';

if (!isLoggedIn()) {
	redirect('index.php');
}

include '_header.php';
printHeader('Staff List');

function getAvatarUrl($uid) {
    $uid = (int) $uid;
    $defaultAvatar = 'http://pkmnhelios.net/images/trainers/Darkmuj.png';
    
    $query = mysql_query("SELECT `avatar` FROM `users` WHERE `id`='{$uid}'");
    $userRow = mysql_fetch_assoc($query);
    $avatar = !filter_var($userRow['avatar'], FILTER_VALIDATE_URL) ? $defaultAvatar : cleanHtml($userRow['avatar']) ;
    return $avatar;
}


$fbicon = 'http://icons.iconarchive.com/icons/cute-little-factory/beer-cap-social/32/Beer-Cap-Facebook-icon.png';
?>

<span style="color: orange;">
    
<table class="pretty-table">
    <tr>
        <th>ID</th>
        <th>Avatar</th>
        <th>Username</th>
        <th>Rank</th>
        <th>Follow On</th>
    </tr>
    <tr>
        <td>#1</td>
        <td><img src="<?= getAvatarUrl(1); ?>" /></td>
        <td><a href="profile.php?id=1">Apex</a></td>
        <td><font color="red">Owner</font></td>
        <td><img src="<?=$fbicon?>"></td>

    </tr>
    <tr>
        <td>#1854</td>
        <td><img src="<?= getAvatarUrl(1854); ?>" /></td>
        <td><a href="profile.php?id=1854">Guy_912(Demon)</a></td>
        <td><font color="orange">Chat moderator</font></td>
        <td><a href="#"><img src="<?=$fbicon?>"></a></td>
    </tr>
    <tr>
    
        <td>#52017</td>
        <td><img src="<?= getAvatarUrl(52017); ?>" /></td>
        <td><a href="profile.php?id=52017">Luis</a></td>
        <td><font color="pink">FB/Chat mod</font></td>
        <td><a href="#"><img src="<?=$fbicon?>"></a></td>
            </tr>
    <tr>
        </tr>
        
                <td>#4266</td>
        <td><img src="<?= getAvatarUrl(41513); ?>" /></td>
        <td><a href="profile.php?id=41513">Rebornz</a></td>
        <td><font color="yellow">GFX spriter</font></td>
        <td><a href="#"><img src="<?=$fbicon?>"></a></td>
            </tr>
    <tr>
        </tr>
        

    <tr>
        <td>#28</td>
        <td><img src="<?= getAvatarUrl(28); ?>" /></td>
        <td><a href="profile.php?id=28">OVO (Cherreh)</a></td>
        <td><font color="blue">Chat mod</font></td>
        <td><a href="#"><img src="<?=$fbicon?>"></a></td>

    </tr>
</table>

<?php include '_footer.php'; ?>