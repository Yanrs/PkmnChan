<?php
if (!defined('GOT_CONFIG')) {
    die();
}

if (isset($uid)) :


?>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-50512768-1', 'pkmnhelios.net');
  ga('send', 'pageview');

</script>


<br /><br />

<div style="display:none;" class="ads-three-mobile">
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- mobile ad 120x50 -->
	<ins class="adsbygoogle"
		 style="display:inline-block;width:120px;height:50px"
		 data-ad-client="ca-pub-5362441234156231"
		 data-ad-slot="3980781705"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
</div>	

<div class="ads-two" style="padding: 0 0; text-align: center;">
	    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	    <!-- Pokemon Thunder RPG 2 -->
	    <ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-5362441234156231" data-ad-slot="1696165306"></ins>
	    <script> (adsbygoogle = window.adsbygoogle || []).push({}); </script>
    </div>

<?php /*
<div class="ads-three" style="padding-bottom: 30px; text-align: center;">
	<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- Pokemon Helios RPG -->
	<ins class="adsbygoogle"
		 style="display:inline-block;width:728px;height:90px"
		 data-ad-client="ca-pub-5362441234156231"
		 data-ad-slot="2057290905"></ins>
	<script> 
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
</div>

<div class="ads-four" style="text-align: center;">
	<script async src="http://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- Pokemon Helios RPG 2 -->
	<ins class="adsbygoogle"
		 style="display:inline-block;width:336px;height:280px"
		 data-ad-client="ca-pub-5362441234156231"
		 data-ad-slot="4568906500"></ins>
	<script>
	(adsbygoogle = window.adsbygoogle || []).push({});
	</script>
</div>
*/ ?>

<?php
/*

<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Pokemon Helios RPG 2 -->
<ins class="adsbygoogle"
     style="display:inline-block;width:336px;height:280px"
     data-ad-client="ca-pub-5362441234156231"
     data-ad-slot="4568906500"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>
*/?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>


<center> Copyright &copy; 2013 <a href="http://pkmnhelios.net/" title="Pokemon Helios RPG" alt="Pokemon Helios RPG">Pokemon Helios</a>. All rights reserved.<br />
This site is not affiliated with Nintendo, Creatures, or GameFreak. <br />
Designed by <a href="http://robylayouts.com" title="Roby Layouts - Web design &amp; development" alt="Roby Layouts - Web design &amp; development">Roby Layouts</a> <center>

<div class="social">
<g:plusone size="standard" count="true" href="http://www.pkmnhelios.net/index.php"></g:plusone>
<div class="fb-like" data-href="https://www.facebook.com/pkmnheliosrpg" data-send="false" data-layout="button_count" data-width="450" data-show-faces="true" data-font="arial"></div>
</div>

<div class="count">
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<br />
<?php
    $query = mysql_query("SELECT `id` FROM users ORDER BY `id` DESC LIMIT 1");
    if ($query) {
        $lastId = mysql_fetch_assoc($query);
	    echo "Total Members: " . number_format($lastId['id']) . "";
    }
?>
</div>
</div>
</div>	
</body>
</html>

<?php

else :
?>

<br>

<div id="footer" align="center">
	<p class="info">© 2014 Pokemon Helios RPG <a href="legalinfo.php" title="Read legal information of Pokemon Helios RPG">(Legal Info)</a></p>
	<p class="credits">Designed by <b><a href="http://robylayouts.com" title="Roby Layouts - Web design &amp; development" alt="Roby Layouts - Web design &amp; development">Roby Layouts</a></b></p>
</div>

<br>

<center class="ads">
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<!-- Pokemon Helios rpg 3 -->
	<ins class="adsbygoogle" style="display:inline-block;width:728px;height:90px" data-ad-client="ca-pub-5362441234156231" data-ad-slot="3646269700"></ins>
	<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
</center>
<?php
endif;

if ($connection) { mysql_close($connection); }

?>