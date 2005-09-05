<div id="errors" class="frame">
	<ul>
		<?php 
			$errors = rss_errors_errors(); 
			foreach($errors as $s => $es) {
				foreach($es as $e) {
					?><li class="error_l<?= $s ?>"><?= $e ?></li><?php
				}
			}
		?>
	</ul>
	<p style="text-align:right">
		<button onclick="document.getElementById('errors').style.display = 'none'; return false;">OK</button>
	</p>
</div>
