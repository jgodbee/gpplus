<?php defined('ABSPATH') or die('-1') ?>
<?php include('header.php') ?>

<div class="container">
	<div class="gpplus mosaicflow">
		<?php for ($i = 0, $iC = count($images); $i < $iC; $i++) { $image = wp_get_attachment_image_src($images[$i], 'medium'); ?>
		<div class="mosaicflow__item"><a href="<?php echo wp_get_attachment_url($images[$i]) ?>"><img src="<?php echo $image[0] ?>" alt="" /></a></div>
		<?php } ?>
	</div>
</div>

<?php include('footer.php') ?>