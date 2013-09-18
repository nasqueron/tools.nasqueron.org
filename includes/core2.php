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

/**
 * Cleans a string, trying to repair non-UTF8 characters
 *
 * @param string $text the text
 * @return string The cleaned text
 */
function clean_string ($text) {
	$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3
    ){1,100}                      # ...one or more times
  )
| ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
| ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
/x
END;

	$utf8replacer = function ($captures) {
	  if ($captures[1] != "") {
	    // Valid byte sequence. Return unmodified.
	    return $captures[1];
	  }
	  elseif ($captures[2] != "") {
	    // Invalid byte of the form 10xxxxxx.
	    // Encode as 11000010 10xxxxxx.
	    return "\xC2".$captures[2];
	  }
	  else {
	    // Invalid byte of the form 11xxxxxx.
	    // Encode as 11000011 10xxxxxx.
	    return "\xC3".chr(ord($captures[3])-64);
	  }
	};

	return preg_replace_callback($regex, $utf8replacer, $text);
}
