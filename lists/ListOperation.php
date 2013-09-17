<?php
class ListOperation {
	/**
	 * Adds two lists
	 *
	 * @param Array $a The left list
	 * @param Array $b The right list
	 * @return Array The resultant list
	 */
	public static function Add ($a, $b) {
		return array_merge($a, $b);
        }

	/**
	 * Intersects two lists
	 *
	 * @param Array $a The left list
	 * @param Array $b The right list
	 * @return Array The resultant list
	 */
	public static function Intersect ($a, $b) {
		if (count($a) == 0 || count($b) == 0) { return array(); }

		return array_intersect($a, $b);
        }

	/**
	 * Substracts a list from another list
	 *
	 * @param Array $a The left list
	 * @param Array $b The right list
	 * @return Array The resultant list
	 */
	public static function Substract ($a, $b) {
		if (count($b) == 0) { return $a; }
		$result = array();
		foreach ($a as $key => $value) {
			$toRemove = false;
			foreach ($b as $itemToRemove) {
				if ($value == $itemToRemove) {
					$toRemove = true;
					break;
				}
			}
			if (!$toRemove) $result[$key] = $value;
		}
		return $result;
        }
}
