<?php defined('ABSPATH') or die('-1') ?>
<?php include('header.php') ?>

<?php if (!is_null($pub) && $now < $pub) { ?>
<h1>Gallery will be available after <?php echo date('m/d/Y', $pub) ?>.</h1>
<?php } else if (!is_null($exp) && $now > $exp) { ?>
<h1>Gallery is no longer available.</h1>
<?php } ?>

<?php include('footer.php') ?>