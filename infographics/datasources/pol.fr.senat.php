<?php

require_once('mediawikidatasources.php');

/**
 * Sénateurs de France
 */
class SenateursFrance extends FrWikipediaDataSource {
	/**
	 * Loads data
	 */
	public function load () {
		$this->source_mode = 'html';
		$this->source_html = "http://fr.wikipedia.org/wiki/Liste_des_sénateurs_français_(période_2011-2014)";
		parent::load();
	}

	/**
 	 * Gets the list of senators
	 *
	 * @return Array The list of senators, each item an array with URL, name and born properties.
	 */
	public function get_senateurs () {
		$data = string_between($this->source_data, '<table class="wikitable alternance sortable">', '</table>', true, true);
		$data = get_array_from_html_table($data, false);
		array_shift($data);

		$senateurs = [];
		foreach ($data as $item) {
			$matches = [];
			if (preg_match('/href="(.*)" title="(.*)"/', $item[0], $matches)) {
				$senateur = [];

				//URL
				$senateur['URL'] = "http://fr.wikipedia.org" . $matches[1];

				//Name
				$title = substr($senateur['URL'], 29);
				$name = str_replace('_', ' ', urldecode($title));
				$pos = strpos($name, '(');
				if ($pos !== false) {
					$name = substr($name, 0, $pos - 1);
				}
				$senateur['name'] = $name;

				//Birth year
				$ds = new FrWikipediaDataSource($title);
				$categories = $ds->get_categories();
				foreach ($categories as $category) {
					$matches = [];
					if (preg_match('/Catégorie:Naissance en ([0-9]{4})/', $category, $matches)) {
						$senateur['born'] = $matches[1];
					}
				}

				$senateurs[] = $senateur;
			}
		}

		return $senateurs;
	}
}
