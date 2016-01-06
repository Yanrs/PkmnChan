<?php
require_once 'config.php';
?>

<form action="" method="post">
<?php 

function expToLevel($exp) {

for ($i=10000; $i>0; $i--) {
if ($exp >= levelToExp($i)) {
return $i;
}
}

return 0;
}

function levelToExp($level) {
return ($level*$level)*10;
}
if(isset($_POST['Calculate']))
{
$level = abs((int) $_POST['lvl']);
$level = htmlentities($_POST['lvl']);
$level = mysql_real_escape_string($_POST['lvl']);

if(!isset($level))
{
echo "you havent entered a number";
}
elseif($level < 5)
{
echo "level must be greater than or equal to 5";
}
elseif( $level > 10000)
{
echo "level must be between 0 and 10,000";
}
else
{
echo "level"." ".number_format($level)." "." pokemon requires"." ".number_format(levelToExp($level))." "."exp";
}
}

if(isset($_POST['cal']))
{
$exp = abs((int) $_POST['exp']);
$exp = htmlentities($_POST['exp']);
$exp = mysql_real_escape_string($_POST['exp']);

if(!isset($exp))
{
echo "you havent entered a number";
}
elseif($exp < 250)
{
echo "exp should be greater than or equal to 250";
}
else
{
echo "you will need ".number_format($exp).""." exp to get to level"." ".number_format(expToLevel($exp));
}
}

?>
<fieldset><legend>Helios Calculator</legend>
<table border="0" cellspacing="0" cellpadding="4" style="margin: 0 auto 0 auto; text-align: left;">
 <tr><td style="text-align: right;" valign="middle">Level:</td><td><input name="lvl" type="text" id="lvl" class="button"/></td>
 <td style="text-align: right;" valign="middle">Level:</td>
 <td><input name="exp" type="text" id="exp" class="button"/></td>
 </tr>
 
 <tr style="text-align: center;" valign="middle">
 <td colspan="2"><input type="submit" name="Calculate" id="Calculate" value="level to exp">
 </td><td colspan="2"><input type="submit" name="cal" id="cal" value="exp to level">
 </td>
 </table></fieldset>
 
 
 
</form>