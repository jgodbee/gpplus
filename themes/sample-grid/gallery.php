<?php defined('ABSPATH') or die('-1') ?>
<?php include('header.php') ?>

<div class="container">
	<div class="gpplus">
		<?php for ($i = 0, $iC = count($images); $i < $iC; $i++) { $image = wp_get_attachment_image_src($images[$i], 'large'); ?>
		<div class="gpplus-image"><img src="<?php echo $image[0] ?>" alt="" /></div>
		<?php } ?>
	</div>
</div>

<?php include('footer.php') ?>