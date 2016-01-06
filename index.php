<?php
require_once 'config.php';
require_once 'functions.php';

if (isLoggedIn()) { redirect('membersarea.php'); }

include '_header.php';

error_reporting(-1);

?>

<div class="content">
	<div class="wrap">
		<table>
			<tr>
				<td>
					<div class="welcome">
						<div class="poke two"></div>
						
						<div class="about"><?=$lang['welcome_about']?></div>
						
						<div class="news-title"><?=$lang['welcome_news_title']?></div>
						<?php 	
							$queryNews = mysql_query("SELECT * FROM news ORDER BY `id` DESC LIMIT 1");
							while ($news = mysql_fetch_array($queryNews)) {
						?>
						<div class="news">
							<div class="subject"><?=$news['title']?></div>
							
							<div class="post"><?=nl2br($news['news'])?></div>
							
							<div class="footer">
								<div class="by"><?=$lang['welcome_news_by']?><?=$news['bywho']?></div>
								<div class="date"><?=date('j M Y', $news['date'])?></div>
							</div>
						</div>
						<?php } ?>
					</div>
					
					<?php include '_footer.php'; ?>
				</td>
			</tr>
		</table>
	</div>
</div>