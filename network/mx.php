<?php
	$mail = '';
	if (isset($_REQUEST['mail'])) {
		$mail = $_REQUEST['mail'];
	}
?>
<h2>Search</h2>
<form>
<div class="row collapse">
  <div class="ten mobile-three columns">
    <input type="text" name="mail" id="mail" value="<?= $mail ?>" />
  </div>
  <div class="two mobile-one columns">
    <input type="submit" class="button expand postfix" value="Search" />
  </div>
</div>
</form>
<?php
if ($mail) {
	echo "<h2>Result</h2>\n";
	if (is_mail($mail)) {
		//Gets domain name
		$data = explode('@', $mail);
		$domain = $data[1];

		//Digs MX record
		$getmx = '/home/dereckson/bin/getmx'; //TODO: move tools to a bin folder
		$cmd = $getmx . ' ' . escapeshellarg($mail); //TODO: a nice function to automate this code
		$mx = `$cmd`;

		//Prints result
		echo "<h3>SMTP server</h3>\n<p id='mx' class='result'>$mx</p>\n<h3>Determination method</h3>\n<p><strong>DNS lookup:</strong> $domain MX ?</p>";
	} else {
		echo "<h3>Error</h3>\n<p class='error'>$mail isn't a valid mail address.</p>";
		echo "<h3>Improve this tool</h3><p>This code is open source, you can improve it. Fork it and add JS validation mail code. If the mail is wrong, apply the class .error to the input box.</p>";
	}
}
?>
