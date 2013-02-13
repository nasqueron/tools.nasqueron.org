<?php

/**
 * Keruald, core libraries for Pluton and Xen engines.
 * (c) 2010-2013, Sébastien Santoro aka Dereckson, some rights reserved
 * Released under BSD license
 *
 * @package Pluton
 * @subpackage Pluton
 * @copyright Copyright (c) 2010-2013, Sébastien Santoro aka Dereckson
 * @license Released under BSD license
 * @version 0.1
 *
 * @todo Add protection against DoS for cache mode: we can't easily fill an hard disk with this one.
 */

/**
 * A Wikimedia Commons media
 */
class InstantCommonsMedia {
	public $filename;
	public $remoteFilename;

	private $dataInfo;

	public $urlInfo;
	public $urlFile;
	public $width = 0;

	public function __construct ($filename) {
		$this->filename = self::clean_filename($filename);
		$this->get_url_info();
		$this->get_url_file();
	}

	public static function clean_filename ($filename) {
		$filename = urldecode($filename);
		$filename = str_replace(' ', '_', $filename);
		return $filename;
	}

	public static function get_context () {
		return stream_context_create([
			'http' => [
				'method' => 'GET',
				'header' => "User-Agent: InstantCommons/0.1 (Keruald/Pluton)",
			]
		]);

	}

	private function get_data_info () {
		if (!$this->urlInfo) {
			$this->urlInfo = $this->get_url_info();
		}
		$this->dataInfo = file_get_contents($this->urlInfo, false, self::get_context());
	}

	private function get_url_info () {
		// Determines URL according the thumbnail or original format request
		$data = array();
		if (preg_match("@(.+)\.svg\-([0-9]+)px\.png$@", $this->filename, $data)) {
			//.svg-200px.png
			$this->remoteFilename = $data[1] . '.svg';
			$this->width = $data[2];
		} elseif (preg_match("@(.+)-([0-9]+)px(\.[a-zA-Z0-9]+)$@", $this->filename, $data)) {
			//-200px.<ext>
			$this->remoteFilename = $data[1] . $data[3];
			$this->width = $data[2];
		} else {
			//Original size
			$this->remoteFilename = $this->filename;
		}

		$this->urlInfo = "http://commons.wikimedia.org/wiki/File:" . urlencode($this->remoteFilename);
        }

	/**
	 * Gets the URL of the file
	 *
	 * @string The URL of the file, ready to download or hotlink.
	 */
	public function get_url_file () {
		//Original media
		if (!$this->dataInfo) $this->get_data_info();
		if (!$this->dataInfo) die("Can't get data info.");
		$this->urlFile = 'http:' . string_between($this->dataInfo, '<div class="fullMedia"><a href="', '"');

		//Thumbnail
		if ($this->width > 0) {
			//Original:	http://upload.wikimedia.org/wikipedia/commons/7/75/Wikimedia_Community_Logo.svg
			//Thumbnail:	http://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Wikimedia_Community_Logo.svg/200px-Wikimedia_Community_Logo.svg.png
			$this->urlFile  = str_replace('/wikipedia/commons/', '/wikipedia/commons/thumb/', $this->urlFile);
			$this->urlFile .= "/{$this->width}px-{$this->remoteFilename}";
			if (substr($this->urlFile, -4) == ".svg") { $this->urlFile .= '.png';  }
		}
	}

	/**
	 * Serves media file
	 *
	 * @todo Handle 404
	 */
	public function serve () {
		switch ($extension = strtolower(get_extension($this->filename))) {
			case "png":
				header('Content-type: image/png');
				break;

			case "gif":
				header('Content-type: image/gif');
				break;

			case "jpg":
			case "jpeg":
				header('Content-type: image/jpeg');
				break;

			case "svg":
				header('Content-type: image/svg+xml');
				break;

			default:
				die("Unknown file extension: $extension");
		}
		header("Content-Disposition: filename=$media->filename");
		header("Content-Transfer-Encoding: binary");

		//Reads and flushes file
		$out = ob_get_clean();
		ob_clean();
		flush();
		readfile($this->urlFile);
		exit;
	}

	public function dump ($includeDataInfo = false, $die = true) {
		if (!$includeDataInfo) { $this->dataInfo = ""; }
		dprint_r($this);
		if ($die) exit;
	}
}

// Loads Keruald library
if (!defined('IN_KERUALD')) {
	require('core.php');
}

// Gets media information
$url = get_current_url_fragments();
$media = new InstantCommonsMedia(array_pop($url));

// Serves media
//$media->dump();
$media->serve();
