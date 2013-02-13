<?php
	//Reads an XML file
	function readxml ($file) {
		$xml = new DOMDocument();
		$xml->load($file);
		return $xml;
	}

	//Gets feed file from URL
	function get_feed_from_url() {
		$url = get_current_url_fragments();
		$feed = array_pop($url);
		$type = array_pop($url);
		if ($type != 'user' && $type != 'project') {
			message_die(GENERAL_ERROR, "Unknown feed type: $type", 'URL error');
		}
		return "wikimedia/dev/feeds/$type/$feed.xml";
	}

	$feed = get_feed_from_url();
	$xsl = 'xslt/activity-feed.xsl';


	if (!file_exists($feed)) {
		message_die(GENERAL_ERROR, "Feed not found: $feed");
	}

	$xslt = new XSLTProcessor();
	$xslt->importStylesheet(readxml($xsl));

?>
<div class="alert-box alert">
	This document is rendered from <a href="/<?= $feed ?>">this XML feed</a> with its XSL stylesheet. This is a temporary output, not the web one.
	<a href="" class="close">&times;</a>
</div>
<?= $xslt->transformToXml(readxml($feed)); ?>
