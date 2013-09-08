<?php
function is_mail ($string) {
	return preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z0-9]{2,4}$/', $string);
}

/*
Unit testing ->
1 is_mail("dereckson@gmail.com");
1 is_mail("dereckson@trustspace.42");
1 is_mail("dereckson@gmail.a.a.a.a.a.a.a.ax.org");
1 is_mail("dereckson+wazza@gmail.com");
0 is_mail("dereckson`ls`@gmail.com");
*/

/**
 * Gets an HTTP context
 */
function get_http_context () {
	return stream_context_create([
		'http' => [
			'method' => 'GET',
			'user_agent' => 'NasqueronTools/0.1'
		]
	]);
}

function get_innerHtml (DOMNode $element) {
	$innerHtml = "";

	foreach ($element->childNodes as $childNode) {
		$dom = new DOMDocument('1.0', 'utf-8');
		$dom->appendChild(
			$dom->importNode($childNode, true)
		);
		$innerHtml .= trim($dom->saveHTML());
	}

	return $innerHtml;
}

function get_array_from_html_table($html, $stripHTML = true) {
	$array = [];

	$html = str_ireplace('<th ', '<td ', $html);
	$html = str_ireplace('<th>', '<td>', $html);
	$html = str_ireplace('</th>', '</td>', $html);
	$html = str_ireplace('<th/>', '<td/>', $html); //No reason not to be as permissive as browsers.

	$dom = new DOMDocument('1.0', 'utf-8');
	$dom->loadHTML($html);
	$rows = $dom->getElementsByTagName('tr');
	foreach ($rows as $row) {
		$values = [];
		$rowCells = $row->getElementsByTagName('td');
		foreach ($rowCells as $cell) {
			$values[] = $stripHTML ? $cell->nodeValue : get_innerHtml($cell);
		}
		$array[] = $values;
	}
	return $array;
}

//Gets current date, ISO format.
function today () {
	return date('Y-m-d');
}