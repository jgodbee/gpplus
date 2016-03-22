<?php defined('ABSPATH') or die('-1') ?>
<?php include('header.php') ?>

<form action="<?php echo esc_url(site_url('wp-login.php?action=postpass', 'login_post')) ?>" class="post-password-form" method="post">
	<p>This gallery is password protected. To view it please enter your password below:</p>
	<p>
		<label for="post-password-form-post_password">Password:</label>
		<input name="post_password" id="post-password-form-post_password" type="password" size="20" />
	</p>
	<input type="submit" name="Submit" value="Submit" />
</form>

<?php include('footer.php') ?>