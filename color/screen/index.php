<?php
	require('color/color.php');

	//Gets the URL part after /color/screen
	$url = get_current_url_fragments();
	array_shift($url);
	array_shift($url);

	//Parses color or if omitted uses a random one.
	if (count($url) == 0 || !$url[0]) {
		$color = Color::random();
	} else {
		try {
			$color = new Color($url[0]);
		} catch (Exception $ex) {
			die("Can't parse color $url[0]. " . $ex->getMessage());
		}
	}
?>
<style>
	body {
		background-color: <?= $color ?>;
	}
</style>
