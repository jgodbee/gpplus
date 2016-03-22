<?php defined('ABSPATH') or die('-1') ?>

<div class="wrap">
	<h1>GP+Gallery Settings</h1>
	<form action="options.php" method="post">
		<?php
			settings_fields('settings');
			do_settings_sections('settings');
			submit_button();
		?>
	</form>
</div>
<!--
<pre><?php print_r(GalleryProPlus::getDebug()) ?></pre>
-->