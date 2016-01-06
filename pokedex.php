<?php
include 'config.php';
include 'functions.php';
include '_header.php';
printHeader('Pokedex');
?>

<script>
function showUser(str)
{
if (str=="")
  {
  document.getElementById("txtHint").innerHTML="";
  return;
  } 
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","pokedex45.php?id="+str,true);
xmlhttp.send();
}
</script>

<center>
<form>
<select name="users" onchange="showUser(this.value)" style="font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;">
<?php
$query = mysql_query("SELECT id, name, num FROM pokedex order by `num` asc");
while($info = mysql_fetch_assoc($query))
{
$text = ' '.str_pad($info['name'], 30, ' ', STR_PAD_RIGHT) . str_pad('#'.$info['num'], 7, ' ', STR_PAD_LEFT) .' ';
$text = str_replace(' ', '&nbsp;', $text);
?>
<option value="<?php echo $info['id']; ?>" style="font-family:Consolas,Monaco,Lucida Console,Liberation Mono,DejaVu Sans Mono,Bitstream Vera Sans Mono,Courier New, monospace;"><?php echo $text; ?></option>
<?php
}
?>
</select>
</form>
</center>
<br>
<div id="txtHint"><div class=notice>please select a pokemon</div></div>

<?php include '_footer.php'; ?>