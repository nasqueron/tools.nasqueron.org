<?php

/**
 * Nasqueron Tools
 *
 * Excel to JSON converter
 *
 * @package     NasqueronTools
 * @subpackage  JSON
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

$data = isset($_REQUEST['data']) ? $_REQUEST['data'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$outputAsObject = false;

if ($data !== '') {
    $outputAsObject = array_key_exists('json-output', $_REQUEST) && ($_REQUEST['json-output'] == 'object');

    $dataArray = [];
    $rows = explode("\n", $data);

    //Allows user to add an extra line (default behavior from Excel by the way)
    if (end($rows) === '') {
        array_pop($rows);
    }

    foreach ($rows as $row) {
        $row = trim($row, "\r");
        $dataArray[] = explode("\t", $row);
    }
    if ($outputAsObject) {
        require 'JsonComposer.php';

        //First line as properties
        $properties = array_shift($dataArray);
        if ($properties === ['']) {
            //If the first line is empty, we don't assume an unique empty string property,
            //but we assume the user wants to autofill property0, property1, property2, etc.
            $properties = [];
        }

        $json = JsonComposer::fromKeysAndValuesArrays($properties, $dataArray);
    } else {
        $json = json_encode($dataArray, JSON_PRETTY_PRINT);
    }

    $url = get_url('dl.php');
    echo "<form method=\"post\" action=\"$url?contentType=application%2Fjson\">\n";
    echo "    <h2>Result</h2>\n";
    echo '    <div class="row">', "\n\t", '<div class="twelve columns"><textarea id="result" name="result" rows="8">',
         $json, '</textarea></div>', "\n", '</div>';
    echo '    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Save as</span>
        </div>
        <div class="four mobile-two columns">
            <input type="text" name="filename" id="filename" value="data.json" />
        </div>
        <div class="one mobile-one columns end">
            <input type="submit" class="button expand postfix" name="action" value="Download" />
        </div>
    </div>
</form>';
}

?>
<form method="post">
    <h2>Source</h2>
    <div class="row"><div class="twelve columns">
          <label for="data">Paste cells from your spreadsheet application or write tab separated values:</label>
    </div></div>
    <div class="row"><div class="twelve columns">
        <textarea id="data" name="data" rows="8"><?= $data ?></textarea>
    </div></div>
    <div class="row"><div class="twelve columns">
        <label for="json-output-array"><input type="radio" name="json-output" id="json-output-array" value="array"<?= !$outputAsObject ? ' checked="true"' : '' ?> /> Convert rows to arrays. The first line is already data.</label>
        <label for="json-output-object"><input type="radio" name="json-output" id="json-output-object" value="object"<?= $outputAsObject ? ' checked="true"' : '' ?> /> Convert rows to objects. The first line contains the properties names.</label>
    </div></div>
    <div class="row"><div class="twelve columns">
        <input type="submit" rows="8" value="Convert" class="button" />
    </div></div>
</form>

<!-- Allows to use tab in textarea fields -->
<script>
function enableTab (id) {
    document.getElementById(id).onkeydown = function(e) {
        if (e.keyCode === 9) { // tab was pressed
            var start = this.selectionStart,
                end = this.selectionEnd;

            this.value = this.value.substring(0, start)
                       + '\t'
                       + this.value.substring(end);
            this.selectionStart = this.selectionEnd = ++start;

            return false;
        }

        return true;
    };
}

enableTab('data');
<?php
if ($data !== '') {
    echo "enableTab('result');\n";
}
?>
</script>
