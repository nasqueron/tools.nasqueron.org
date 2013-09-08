<?php

require_once('mediawikidatasources.php');

/**
 * Sénateurs de France
 */
class AssembleeNationale extends FrWikipediaDataSource {
	/**
	 * Loads data
	 */
	public function load () {
		$this->source_mode = 'html';
		$this->source_html = "http://fr.wikipedia.org/wiki/Liste_des_députés_de_la_XIVe_législature_de_la_Cinquième_République";
		parent::load();
	}

	/**
 	 * Gets the list of reprensentatives
	 *
	 * @return Array The list of representatives, each item an array with URL, name and born properties.
	 */
	public function get_members () {
		$data = string_between($this->source_data, '<table class="wikitable sortable">', '</table>', true, true);
		$data = get_array_from_html_table($data, false);
		array_shift($data);

		$members = [];
		foreach ($data as $item) {
			$matches = [];
			if (preg_match('/href="(.*)" title="(.*)"/', $item[0], $matches)) {
				$member = [];

				//URL
				$member['URL'] = "http://fr.wikipedia.org" . $matches[1];

				//Name
				$title = self::get_title_from_url($member['URL']);
				$member['name'] = self::get_name_from_title($title);

				//Birth year
				$member['born'] = self::get_birth_year($title);

				$members[] = $member;
			}
		}

		return $members;
	}
}
