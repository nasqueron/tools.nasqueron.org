<?php
include('RegexpFactory.php');

// Handles permanent links
if (array_key_exists('r', $_REQUEST)) {
	$_REQUEST = unserialize(base64_decode($_REQUEST['r']));
}

$result = '';
$enable_join = array_key_exists('join', $_REQUEST) && $_REQUEST["join"] == 'on';
$enable_split = array_key_exists('split', $_REQUEST) && $_REQUEST["split"] == 'on';

if (array_key_exists('expression', $_REQUEST)) {
    $requestSerialized = base64_encode(serialize($_REQUEST));

    if (array_key_exists('lists', $_REQUEST) && array_key_exists(0, $_REQUEST['lists']) && array_key_exists('replacement', $_REQUEST)) {
        $regexp = new RegexpFactory($_REQUEST['expression']);
        $regexp->addDelimiters();
        if ($regexp->isValid()) {
            $items = explode("\n", $_REQUEST['lists'][0]);
            $replace_callback = function (&$item, $key, $replaceExpression) use ($regexp) {
                $item = $regexp->replace(trim($item), $replaceExpression);
            };
            array_walk($items, $replace_callback, $_REQUEST['replacement']);
            $result = join("\n", $items);
            if ($enable_join) {
                $result = join($_REQUEST["joinglue"], $items);
            }
            if ($enable_split) {
                $split_result = [];
                foreach ($items as $item) {
                    $split_result = array_merge($split_result, explode($_REQUEST["splitseparator"], $item));
                }
                $result = join("\n", $split_result);
            }
        }
    }
    //If no list is given, or the replacement expression is blank, the result list is blank.
}
?>
    <script>
        /**
         * Updates the form's widgets
         */
        function updateUI () {
            //Checks the join enable box when a glue string is provided
             if (document.getElementById("joinglue").value != "" && !document.getElementById("join").checked) {
                 document.getElementById("join").checked = true;
                 $("#join-checkbox .checkbox").addClass("checked");
             }

             //Checks the split enable box when a split seperator is provided
             if (document.getElementById('splitseparator').value != "" && !document.getElementById("split").checked) {
                document.getElementById("split").checked = true;
                $("#split-checkbox .checkbox").addClass("checked");
             }
         }
    </script>
    <form name="lists" method="post" action="/lists/replace" class="custom">
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Replace</span>
        </div>
        <div class="five mobile-three columns">
            <input
                   name="expression" id="expression" type="text"
                   placeholder="The Nginx or Apache mod_rewrite-like regular expression"
                   value="<?= $_REQUEST['expression'] ?>"
            />
            <?php if (isset($regexp) && $regexp->lastError) echo '<small class="error" style="font-weight: 400;">', $regexp->lastError, '</small>'; ?>
        </div>
        <div class="five mobile-three columns">
            <input
                   name="replacement" id="replacement" type="text"
                   placeholder="The list format. Use $1, $2, … to use the regexp groups."
                   value="<?= $_REQUEST['replacement'] ?>"
            />
        </div>
        <div class="one mobile-one columns">
            <input type="submit" class="button expand postfix" value="Format" />
        </div>
    </div>
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Join</span>
        </div>
        <div class="ten mobile-six columns">
            <input name="joinglue" id="joinglue" type="text" placeholder="Glue text to join the list into a string. Leave blank to concat without seperator. Don't forget to check the checkbox to enable." value="<?= $_REQUEST['joinglue'] ?>" onchange="updateUI();" />
        </div>
        <div class="one mobile-one columns" style="text-align: center;">
            <span id="join-checkbox"><input type="checkbox" id="join" name="join" <?= $enable_join ? 'checked ' : '' ?>/><br /><label for="join">Enable</label></span>
        </div>
    </div>
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Split</span>
        </div>
        <div class="ten mobile-six columns">
            <input name="splitseparator" id="splitseparator" type="text" placeholder="Separator text to split the list furthermore." value="<?= $_REQUEST['splitseparator'] ?>" onchange="updateUI();" />
        </div>
        <div class="one mobile-one columns" style="text-align: center;">
            <span id="split-checkbox"><input type="checkbox" id="split" name="split" <?= $enable_split ? 'checked ' : '' ?>/><br /><label for="split">Enable</label></span>
        </div>
    </div>
    <div class="row collapse">
        <div class="six columns">
            <textarea name="lists[0]" rows="16" style="width: 99%;" placeholder="The list to format"><?= $_REQUEST['lists'][0] ?></textarea>
        </div>
        <div class="six columns">
            <textarea name="lists[1]" rows="16" style="width: 99%;" placeholder="The formatted list will appear here."><?= $result ?></textarea>
       </div>
    </div>
    </form>
<?php
if (isset($requestSerialized)) {
	echo "<p><a href=\"/lists/replace/?r=$requestSerialized\">Permanent link to this query</a></p>";
}
?>
    <p><strong>Documentation resources:</strong> <a href="http://perldoc.perl.org/perlre.html">PCRE syntax</a> • <a href="http://www.cheatography.com/davechild/cheat-sheets/regular-expressions/">Regular Expressions Cheat Sheet</a>
    <br /><strong>Note:</strong> Write your regexp without delimiter.</p>
