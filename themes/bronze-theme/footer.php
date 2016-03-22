<?php defined('ABSPATH') or die('-1') ?>
<?php if (isset($images)) { ?>
<script>
	var progress = $('#progress'), slideshow = $('.cycle-slideshow');
	slideshow.on('cycle-initialized cycle-before', function(e, opts) {
		progress.stop(true).css('width', 0);
	});
	slideshow.on('cycle-initialized cycle-after', function(e, opts) {
		if (!slideshow.is('.cycle-paused')) {
			progress.animate({ width:'100%' }, opts.timeout, 'linear');
		}
	});
	slideshow.on('cycle-paused', function(e, opts) {
		progress.stop();
	});
	slideshow.on('cycle-resumed', function(e, opts, timeoutRemaining) {
		progress.animate({ width:'100%' }, timeoutRemaining, 'linear');
	});
</script>
<?php } ?>
<?php if (isset($audio_meta) && isset($audio_path)) { ?>
<script>
	jQuery(document).ready(function(){
		$('#jplayer').jPlayer({
			ready: function () {
				$(this).jPlayer('setMedia', {
					title: "<?php echo str_replace('"', "'", $audio_meta['title']) ?>",
					<?php echo $audio_meta['dataformat'] ?>: '<?php echo $audio_path ?>'
				}).jPlayer('play');
			},
			swfPath: '<?php echo GALLERYPROPLUS_URL ?>/js/jPlayer-2.9.2/dist/jplayer/jquery.jplayer.swf',
			supplied: '<?php echo $audio_meta['dataformat'] ?>',
			wmode: 'window',
			useStateClassSkin: true,
			autoBlur: true,
			smoothPlayBar: true,
			keyEnabled: true,
			remainingDuration: true,
			toggleDuration: true,
			loop : true
		});
	});
</script>
<?php } ?>
<?php if (!empty($body_footer)) echo $body_footer ?>
</body>
</html>