<?php
/**
 * Represents a color
 */
class Color {
	/**
	 * The hexadecimal RGB representation of the color
	 */
	private $hex;

	/**
	 * Initializes a new instance of a Color object.
	 *
	 * @param string $color The color name or code.
	 */
	public function __construct ($color = null) {
		if ($color === null) return;
		if (!$color) {
			$this->hex = "000";
		} elseif (is_array($color)) {
			if (array_key_exists('r', $color) && array_key_exists('g', $color) && array_key_exists('b', $color)) {
				//RGB
				$this->hex = self::rgb2hex($color['r'], $color['g'], $color['b']);
			} else {
				throw new Exception("Unrecognized array format.");
			}
		} elseif (preg_match("/[0-9A-F]{6}/", $color) || preg_match("/[0-9A-F]{3}/", $color)) {
			$this->hex = $color;
		} elseif (preg_match("/#[0-9A-F]{6}/", $color) || preg_match("/#[0-9A-F]{3}/", $color)) {
			$this->hex = substr($color, 1);
		} else {
			throw new Exception("Unrecognized string format.");
		}
	}

	public function __toString () {
		return "#" . $this->hex;
	}

	public static function rgb2hex ($r, $g, $b) {
		return strtoupper (
			str_pad(dechex($r), 2, '0', STR_PAD_LEFT) .
			str_pad(dechex($g), 2, '0', STR_PAD_LEFT) .
			str_pad(dechex($b), 2, '0', STR_PAD_LEFT)
		);
	}

	public static function Random () {
		$rgb = [
			'r' => mt_rand(0, 255),
			'g' => mt_rand(0, 255),
			'b' => mt_rand(0, 255)
		];
		return new Color($rgb);
	}
}