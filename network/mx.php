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
    <input type="text" name="mail" id="mail" value="<?= $mail ?>" placeholder="Enter e-mail address here." />
  </div>
  <div class="two mobile-one columns">
    <input type="submit" class="button expand postfix" value="Search" />
  </div>
</div>
</form>
<?php
function is_domain ($string) {
	return preg_match($string, '/[a-zA-Z\d-]{,63}(\.[a-zA-Z\d-]{,63})*/');
}

function print_mx ($domain) {
	if (getmxrr($domain, $mxhosts, $weights)) {
		echo '<table id="mx" class="result">';
		echo '<tr><th>Host</th><th>Weight</th></tr>';
		$n = count($mxhosts);
		for ($i = 0 ; $i < $n ; $i++) {
			echo "<tr><td>$mxhosts[$i]</td><td>$weights[$i]</td></tr>";
		}
		echo '</table>';
	} else {
		echo '<p id="mx" class="result emptyresult">—</p>';
	}
	echo "<h3>Determination method</h3>\n<p><strong>DNS lookup:</strong> $domain MX ?</p>";
}

if ($mail) {
	echo "<h2>Result</h2>\n<h3>MX records</h3>";
	if (is_mail($mail)) {
		//Gets domain name
		$data = explode('@', $mail);
		print_mx($data[1]);
	} elseif (is_domain($mail)) {
		//We silently accept direct domain entries given instead of a mail
		print_mx($mail);
	} else {
		echo "<h3>Error</h3>\n<p class='error'>$mail isn't a valid mail address.</p>";
		echo "<h3>Improve this tool</h3><p>This code is open source, you can improve it. Fork it and add JS validation mail code. If the mail is wrong, apply the class .error to the input box.</p>";
	}
}
