<?php

require_once('includes/cache/cache.php');

function get_stops ($line, $date = null, $bypassCache = false) {
	//Gets current date if needed
	if ($date === null) $date = today();

	//Loads cache information
	$cache = Cache::load();
	$key = "tools-ds-TEC-$line-$date";
	if (!$bypassCache && $data = $cache->get($key)) {
		return unserialize($data);
	}

	//JSON query
	$url = "http://www.infotec.be/hastinfo/published/Horaire.axd?date=$date&ligne=$line";
	$data = file_get_contents($url);
	$json = json_decode($data);
	$stops = $json->items[0]->horaire->stops;

	$cache->set($key, serialize($stops));

	return $stops;
}

$stops = get_stops($_REQUEST['line']);
echo json_encode($stops);
