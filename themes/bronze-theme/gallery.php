<?php defined('ABSPATH') or die('-1') ?>
<?php /*
//
// Variables available
//
$html_head		// Optional - String content provided in gallery to insert into page <head> tag; used in header include.
$body_header	// Optional - String content provided in gallery to insert right after opening <body> tag; used in header include.
$body_footer	// Optional - String content provided in gallery to insert right before closing </body> tag; used in footer include.
$audio_id		// Optional - Integer ID of audio file provided in gallery.
$audio_meta		// Optional - Array of meta data for audio file provided, if any.
$audio_path		// Optional - String path to audio file provided, if any.
$images			// Array of gallery images.
$logo_id		// Optional - Integer ID of logo file provided in gallery.
$logo_path		// Optional - String path to logo file provided, if any.
$theme_path		// String path to theme selected in gallery; used in header include.

*/ ?>
<?php include('header.php') ?>

<?php if (isset($logo_path)) { ?>
<img src="<?php echo $logo_path ?>" alt="" />
<?php } ?>

<div class="cycle-slideshow" data-cycle-loader="wait" data-cycle-fx="fadeout" data-cycle-pause-on-hover="true">
	<?php for ($i = 0, $iC = count($images); $i < $iC; $i++) { ?>
		<!--<a href="<?php echo wp_get_attachment_url($images[$i]) ?>"></a>-->
		<?php echo wp_get_attachment_image($images[$i], 'large', false, array('title'=>GalleryProPlus::getImageProperty($images[$i], 'filename-without-extension'))) ?>
	<?php } ?>
	<div id="progress"></div>
</div>

<?php if (isset($audio_path)) { ?>
<div id="jplayer" class="jp-jplayer"></div>
<div id="jp_container_1" class="jp-audio" role="application" aria-label="media player">
	<div class="jp-type-single">
		<div class="jp-gui jp-interface">
			<div class="jp-volume-controls">
				<button class="jp-mute" role="button" tabindex="0">mute</button>
				<button class="jp-volume-max" role="button" tabindex="0">max volume</button>
				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
			</div>
			<div class="jp-controls-holder">
				<div class="jp-controls">
					<button class="jp-play" role="button" tabindex="0">play</button>
					<button class="jp-stop" role="button" tabindex="0">stop</button>
				</div>
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
				<div class="jp-current-time" role="timer" aria-label="time">&nbsp;</div>
				<div class="jp-duration" role="timer" aria-label="duration">&nbsp;</div>
				<div class="jp-toggles">
					<button class="jp-repeat" role="button" tabindex="0">repeat</button>
				</div>
			</div>
		</div>
		<div class="jp-details">
			<div class="jp-title" aria-label="title">&nbsp;</div>
		</div>
		<div class="jp-no-solution">
			<span>Update Required</span>
			To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
		</div>
	</div>
</div>
<?php } ?>

<?php include('footer.php') ?>