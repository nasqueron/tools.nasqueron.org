<?php
	$getmx = '/home/dereckson/bin/getmx';
	$mail = '';
	if (isset($_REQUEST['mail'])) {
		$mail = $_REQUEST['mail'];
		$mx = `$getmx $mail`;
	}
?>
<div class="row collapse">
  <div class="ten mobile-three columns">
    <label for="mail">E-mail:</label> <input type="text" name="mail" id="mail" value="<?= $mail ?>" />
  </div>
  <div class="two mobile-one columns">
    <a class="button expand postfix">Search</a>
  </div>
</div>
<?php
	if ($mail) {
		echo "<h2>Result</h2>\n<p>$mx</p>";
	}
?>
