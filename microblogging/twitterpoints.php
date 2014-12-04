<form>
<div class="row collapse">
  <div class="ten mobile-three columns">
    <input type="text" name="username" id="username" value="" placeholder="Enters the Twitter username." />
  </div>
  <div class="two mobile-one columns">
    <input type="submit" class="button expand postfix" value="Count" />
  </div>
</div>
</form>
<?php
if (isset($_REQUEST['username']) && $_REQUEST['username'] != '') {
	$username = $_REQUEST['username'];
	$errorMessage = '';

	if (!preg_match('/^[A-Za-z0-9_]{1,15}$/', $username)) {
		$errorMessage = "$username doesn't seem to be a valid Twitter username.";
	} else {
		require_once('TwitterAPIExchange.php');

		$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
		$getfield = '?screen_name=' . urlencode($_REQUEST['username']) . '&include_rts=false&count=200';
		$requestMethod = 'GET';

		$twitter = new TwitterAPIExchange($Config['TwitterAPI']);
		$response = $twitter->setGetfield($getfield)
                    ->buildOauth($url, $requestMethod)
                    ->performRequest();
		$response = json_decode($response);

        	if (isset($response->errors)) {
			foreach ($response->errors as $error) {
				$errorMessage .= "<p>$error->message</p>";
			}
		} elseif (isset($response->error)) {
			$errorMessage = "<p>$response->error</p>";
		}
	}        

	if ($errorMessage !== '') {
		echo "<h2>Can't compute stats.</h2>$errorMessage";
	} else {

	$stats = [
		'tweets' => 0,
		'total_length' => 0,
		'len_140' => 0
	];

	foreach ($response as $tweet) {
		$len = strlen($tweet->text);
		$stats['tweets']++;
		$stats['total_length'] += $len;
		if ($len == 140) { $stats['len_140']++; }
	}
	$name = $tweet->user->name;
	$avatar = $tweet->user->profile_image_url_https;
	echo '<div style="text-align: center;">';
	echo "<h2>$name</h2>";
	echo '<img src="', $avatar, '" alt="', $name, "'s", ' avatar" />';
	$score = $stats[len_140] . " Twitter Point" . s($stats['len_140']);
	echo "<h3>$score</h3>";
	$s = s($stats['tweets']);
	echo "<h4>Computed from the last $stats[tweets] tweet$s</h4>";
	$avg = $stats['total_length'] / $stats['tweets'];
	$avg_rnd = round($avg);
	$avg = round($avg, 5);
	$s = s($avg);
	echo "<h4 class=\"hide-for-touch\">Average tweet length: <abbr title=\"$avg\">$avg_rnd</abbr> character$s</h4><h4 class=\"show-for-touch\">Average tweet length: $avg character$s</h4>";

	//RT
	$url = 'https://twitter.com/intent/tweet?url=http://tools.dereckson.be/TP/' . $username . '&text=%23TP ' . $score . '%20%E2%80%94&related=dereckson,weneldur';
	echo '<p>[ <a href="', $url, '" target="_blank"><i class="social foundicon-twitter"> Share score</i></a> ]</p>';

	//Info
	echo '<p style="margin-top: 3em;">You got a Twitter Point each time your tweet is exactly 140 characters.<br />Rules: Yours, not the RT. Points expire after a little less than 200 messages.<br />The Twitter Point concept <a href="https://twitter.com/Weneldur/status/380416650899488768">has been created</a> by Ælfgar.</p>';
	echo '</div>';
	echo '<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>';
}}
