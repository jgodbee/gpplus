<?php defined('ABSPATH') or die('-1') ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title><?php echo $gallery->post_title ?></title>
	<link href="<?php echo $theme_path ?>/styles.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo GALLERYPROPLUS_URL ?>/scripts/jquery-1.11.1.min.js"></script>
	<script src="<?php echo GALLERYPROPLUS_URL ?>/scripts/jquery.cycle2.min.js"></script>
	<?php if (isset($audio_meta) && isset($audio_path)) { ?>
	<link href="<?php echo GALLERYPROPLUS_URL ?>/scripts/jPlayer-2.9.2/dist/skin/blue.monday/css/jplayer.blue.monday.min.css" rel="stylesheet" type="text/css" />
	<script src="<?php echo GALLERYPROPLUS_URL ?>/scripts/jPlayer-2.9.2/dist/jplayer/jquery.jplayer.min.js"></script>
	<?php } ?>
	<?php if (!empty($html_head)) echo $html_head ?>
</head>
<body>
<?php if (!empty($body_header)) echo $body_header ?>