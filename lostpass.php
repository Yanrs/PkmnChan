<? 
die();
include "functions.php";
include "config.php";
include "_header.php";


 if(isset($_POST['lostpassword'])) {
 $email = $_POST['email'];
 if($email == NULL) {
 $final_report.="Please make sure you have completed the form!";
 }else{
 $query_data = mysql_query("SELECT * FROM `users` WHERE `email`='".$email."'");
 if(mysql_num_rows($query_data) == 0){
 $final_report.="This email addresss is not in our database.";
 }else{
 $query_data = mysql_query("SELECT * FROM `users` WHERE `email`='".$email."'");
 $final_report.="Your details have been sent to your account email!";
 $get_data = mysql_fetch_array($query_data);
 $subject = "Forgoten Password"; 
 $message = "Hello ".$get_data['username'].", 
 
Your password is: ".pack("H*", sha1($get_data['password']));"
 
Thank you, 
Pokemon Helios RPG
 
This is a automated password response, please do not reply to this email!
";  
mail($get_data['email'], $subject, $message,"From: Pokemon Helios RPG <roby@pkmnhelios.net>\n"); 
header( 'refresh: 3; url=index.php');
}}}
?>

<form method="post">
  <table>

    <tr>
      <td><? if(!isset($_POST['lostpassword'])){?>
        Please enter your email address if you have lost your password.
        <? }else{ echo "".$final_report."";}?></td>
    </tr>
    <tr>
      <td width="37%">Email:</td>
      <td width="63%"><input name="email" type="text" id="email" size="30" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="lostpassword" type="submit" value="Lost Password" /></td>
    </tr>
 
  </table>
</form>
<? 
include "_footer.php";
?>
