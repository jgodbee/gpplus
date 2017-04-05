<?php defined('ABSPATH') or die('-1') ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo $gallery->post_title ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo $theme_path ?>/styles.css" rel="stylesheet" type="text/css" />
	<style>body{background:<?php echo $page_background_color ?>;}header{background:<?php echo $header_background_color ?>;color:<?php echo $header_font_color ?>;}</style>
	<?php if (!empty($html_head)) echo $html_head ?>
</head>
<body>
	<?php if (!empty($body_header)) echo $body_header ?>
	<header>
		<div class="container">
			<?php if (isset($logo_path)) { ?>
			<div id="logo">
				<img src="<?php echo $logo_path ?>" alt="" />
			</div>
			<?php } ?>
			<h1><?php echo $gallery->post_title ?></h1>
		</div>
	</header>