<h2>Convert text to alphabet variant</h2>
<?php
define('DEFAULT_VARIANT_MAP', "A Ꭿ B Ᏸ C Ꮯ D Ꭰ E Ꭼ F Ꭸ G Ꮳ H Ꮋ I Ꮖ J Ꭻ K Ꮶ L Ꮂ M Ꮇ N Ꮑ O Ꮎ P Ꭾ Q Ꮼ R Ꭱ S Ꭶ T Ꮏ U Ꮜ V Ꮴ W Ꮃ X Ꮊ Y Ꮍ Z Ꮓ");

/**
 * Gets default variant map
 *
 * @return string The default variant map
 */
function get_default_variant_map () {
	return get_variant_map(DEFAULT_VARIANT_MAP);
}

/**
 * Gets a variant map from the specified map expression
 *
 * @param string The map expression (see TCL string map for an idea)
 * @return array A array, to use as an hashtable to substitute charachters according the specified map
 */
function get_variant_map ($map_expression) {
	$hashtable = array();
	$map = explode(' ', $map_expression);
	if (count($map) % 2 == 1) {
		throw new Exception("Invalid map expression. An even number of elements is expected");
	}
	$n = count($map);
	for ($i = 0 ; $i < $n ; $i++) {
		$hashtable[$map[$i]] = $map[++$i];
	}
	return $hashtable;
}

/**
 * Converts txt according the variant
 */
function convert_text ($text, $map, $forceUppercase = true) {
	if ($forceUppercase) $text = strtoupper($text);
	$hashtable = get_variant_map($map);

	$converted_text = '';
	foreach (str_split($text) as $char) {
		$converted_text .= array_key_exists($char, $hashtable) ? $hashtable[$char] : $char;
	}
	return $converted_text;
}

$text = '';
if (isset($_REQUEST['text'])) {
	$text = $_REQUEST['text'];
}
$map = isset($_REQUEST['map']) ? $_REQUEST['map'] : DEFAULT_VARIANT_MAP;

if ($text) {
	echo '<div class="row collapse"><div class="twelve mobile-four columns">';
	echo '<p id="text-variant">', convert_text($text, $map), '</p>';
	echo '</div></div>';
	echo "<hr />";
}
?>
<form method="POST">
<div class="row collapse" style="margin-bottom: 1em;">
  <div class="ten mobile-three columns">
    <input type="text" name="text" id="text" value="<?= $text ?>" placeholder="Lorem ipsum dolor sit amet nunc abuntur - Write here the text to rewrite." />
  </div>
  <div class="two mobile-one columns">
    <input type="submit" class="button expand postfix" value="Convert" />
  </div>
</div>
<div class="row collapse">
  <div class="twelve mobile-four columns">
    <label for="text">Customize the character map:</label>
    <br />
    <input type="text" name="map" id="map" value="<?= $map ?>" placeholder="Write here the translation character map to use" />
  </div>
</div>
</form>
