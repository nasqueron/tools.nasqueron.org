<?php
require_once('datasource.php');
require_once('includes/mediawikibot.php');
require_once('includes/cache/cache.php');

/**
 * A MediaWiki data source
 *
 * @todo Evaluate if it makes sense to split this class in two, one for wikitext parsing, one for HTML output parsing
 */
class MediaWikiDataSource extends DataSource {
	/**
	 * The source reading mode: wiki to parse wikitext, html to parse HTML output
	 */
	public $source_mode;

	/**
	 * The name of the article (used in wiki source mode)
	 */
	public $source_wiki;

	/**
	 * The URL of the article (used in HTML source mode)
	 */
	public $source_html;

	/**
	 * The text of the article (wikitext in wiki source mode, raw HTML in HTML source mode)
	 */
	public $source_data;

	/**
	 * The API entry point URL (e.g. http://www.mediawiki.org/w/api.php)
	 */
	public $api_url;

	/**
	 * Initializes a new instance of the MediaWikiDataSource class
	 *
	 * @param $source The source target, article title in wiki mode, article URL in html mode
	 * @param $mode The source mode (facultative, by default 'wiki')
	 */
	public function __construct ($source = false, $mode = 'wiki') {
		$this->source_mode = $mode;
		if ($source !== false) {
			switch ($mode) {
				case 'wiki': $this->source_wiki = $source; break;
				case 'html': $this->source_html = $source; break;
				default: throw new Exception("Unknown source mode: $mode.");
			}
		}
	}

	/**
	 * Gets the general purpose MediaWiki entry point (e.g. http://www.mediawiki.org/w/index.php)
	 *
	 * @return string The MediaWiki entry point
	 *
	 * @todo This currently considers nobody has prepared rewrite rules blocking access to index.php and api.php
	 */
	public function get_entry_point () {
		return substr($this->api_url, 0, -7) . "index.php";
	}

	/**
	 * Loads the source content into the source_data property.
	 */
	public function load () {
		switch ($this->source_mode) {
			case 'wiki':
				$url = $this->get_entry_point() . "?title=" . urlencode($this->source_wiki) . "&action=raw";
				break;

			case 'html':
				$url = $this->source_html;
				break;

			default:
				throw new Exception("$source_mode isn't a valid MediaWikiDataSource source mode.");
		}
		$this->source_data = file_get_contents($url, false, get_http_context());
	}

	/**
	 * Gets categories, from cache or API.
	 *
	 * @param $bypassCache If true, bypass the cache; otherwise, use the cached result if available.
	 * @return array an single dimension array, each row a category, including Category: prefix.
	 */
	public function get_categories ($bypassCache = false) {
		if ($this->source_mode != 'wiki') {
			throw new Exception("get_categories is only supported in wiki mode.");
		}

		//Loads cache information
		$cache = Cache::load();
		$key = 'tools-ds-mwcats-' . md5($this->api_url . $this->source_wiki);
		if (!$bypassCache && $data = $cache->get($key)) {
			return unserialize($data);
		}

		//API query
		$api = new MediaWikiBot($this->api_url);
		$reply = $api->query([
			'prop' => 'categories',
			'titles' => $this->source_wiki,
			'cllimit' => 500,
		]);

		//Processes the query reply and caches it
		$reply = array_shift($reply['query']['pages']);
		$categories = [];
		if (count($reply['categories'])) {
			foreach ($reply['categories'] as $category) {
				$categories[] = $category['title'];
			}
		}
		$cache->set($key, serialize($categories));

		return $categories;
	}


	/**
	 * Gets title from URL
	 *
	 * @param string The article URL
	 * @return string The article title
	 */
	public static function get_title_from_url ($url) {
		$pos = strpos($url, "/wiki/");
		return ($pos === false) ? "" : substr($url, $pos + 6);
	}

	/**
	 * Gets name from title
	 *
	 * @param string The article title
	 * @return string The subject of the article's name
	 */
	public static function get_name_from_title ($title) {
		$name = str_replace('_', ' ', urldecode($title));
		$pos = strpos($name, '(');
		if ($pos !== false) {
			$name = substr($name, 0, $pos - 1);
		}
		return $name;
	}

	/**
	 * Gets name from URL
	 *
	 * @param string The article URL
	 * @return string The subject of the article's name
	 */
	public static function get_name_from_url ($url) {
		if ($title = get_title_from_url($url)) {
			return get_name_from_title($title);
		}
		return "";
	}
}

/**
 * A MediaWiki data source, matching a Wikipedia project.
 *
 * @todo use case to get a wikipedia specific class: do we need WP specific helper methods?
 */
class WikipediaDataSource extends MediaWikiDataSource {
	/**
	 * Initializes a new instance of the WikipediaDataSource class
	 *
	 * @param $source The source target, article title in wiki mode, article URL in html mode
	 * @param $mode The source mode
	 * @param $language The language project (e.g. "en")
	 */
        public function __construct ($source, $mode, $language) {
		parent::__construct($source, $mode);
		$this->api_url = "http://$language.wikipedia.org/w/api.php";
	}
}

/**
 * French Wikipedia data source.
 */
class FrWikipediaDataSource extends WikipediaDataSource {
	/**
	 * Initializes a new instance of the FrWikipediaDataSource class
	 *
	 * @param $source The source target, article title in wiki mode, article URL in html mode
	 * @param $mode The source mode
	 */
        public function __construct ($source = false, $mode = 'wiki') {
		parent::__construct($source, $mode, 'fr');
	}

	/**
	 * Gets birth year, from the category Naissance en.
	 *
 	 * @param $name The person article title
	 * @param $bypassCache If true, bypass the cache; otherwise, use the cached result if available.
	 * @return integer|null The birth year of the person if available; otherwise, null.
	 */
	static public function get_birth_year ($name, $bypassCache = false) {
		$ds = new FrWikipediaDataSource($name);
		$categories = $ds->get_categories($bypassCache);
		foreach ($categories as $category) {
			$matches = [];
			if (preg_match('/Catégorie:Naissance en ([0-9]{4})/', $category, $matches)) {
				return $matches[1];
			}
		}
		return null;
	}
}

?>