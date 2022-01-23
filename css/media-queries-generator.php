<?php
	$widths = $_REQUEST['widths'] ?? '400, 720, 1280, 1440, 1920, 2880';
?>
    <form name="media-queries-generator" class="custom">
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Widths</span>
        </div>
        <div class="ten mobile-six columns">
            <input
                   name="widths" id="widths" type="text"
                   placeholder="The widths you want to target, separated by comma"
                   value="<?= $widths ?>"
                   onClick="BuildCSS();"
            />
            <?php if (isset($regexp) && $regexp->lastError) echo '<small class="error" style="font-weight: 400;">', $regexp->lastError, '</small>'; ?>
        </div>
        <div class="one mobile-one columns">
            <input type="button" class="button expand postfix" value="Generate" />
        </div>
    </div>
    <div class="row collapse">
        <div class="one mobile-one columns">
            <span class="prefix">Indent</span>
        </div>
        <div class="one mobile-one columns">
            <input name="indentcount" id="indentcount" type="number" min="0" value="4">
        </div>
        <div class="ten mobile-six columns">
            <select name="indenttype" id="indenttype">
                <option value="tab">Tabs</option>
                <option value="space" selected>Spaces</option>
            </select>
        </div>
    </div>
    <div class="row collapse">
        <div class="six columns">
            <textarea name="css" id="css" rows="16" style="width: 99%;" placeholder="The CSS content" onChange="BuildCSS();"><?= $_REQUEST['css'] ?? "" ?></textarea>
        </div>
        <div class="six columns">
            <textarea name="result" id="result" rows="16" style="width: 99%;" placeholder="The final CSS will appear here."></textarea>
       </div>
    </div>
    <div class="row">
       <div class="twelve columns">
           <p><strong>Note:</strong> Use <strong>%width%</strong> in your CSS to generate code to substitude the current breakpoint width.</p>
       </div>
    </div>
    </form>
    <script>
        /**
         * Build CSS
         */
        function BuildCSS () {
            widths = document.getElementById('widths').value.split(', ').map(function(s) { return s.trim() });
            document.getElementById('result').value = GetMediaQueriesCSS(
                widths,
                document.getElementById('css').value,
                parseInt(document.getElementById('indentcount').value),
                document.getElementById('indenttype').value == 'space'
            );
        }

        function GetMediaQueriesCSS(widths, cssContent, indentAmout, useSpaceForIndent) {
            var css = '';
            for (var i = 0 ; i < widths.length ; i++) {
                if (i == 0) {
                    css += "@media screen and (max-width: " + widths[0] + "px) {";
                } else if (i == widths.length - 1) {
                    css += "@media screen and (min-width: " + (parseInt(widths[i-1]) + 1) + "px) {";
                } else {
                    css += "@media screen and (min-width: " + (parseInt(widths[i-1]) + 1) + "px) and (max-width: " + widths[i] + "px) {";
                }
                css += '\n';
                css += Indent(cssContent.replace(/%width%/g, widths[i]), indentAmout, useSpaceForIndent);
                css += '\n}';
                if (i < widths.length - 1) {
                    css += '\n\n';
                }
            }
            return css;
        }

        /**
         * Repeats the current string a specified number of times
         *
         * @param int count The number of times to repeat thestring
         * @return The repeated string
         */
        String.prototype.repeat = function (count) {
            // Code by artistoex and pimvdb - http://jsfiddle.net/disfated/GejWV/
            if (count < 1) {
                return '';
            }
            var result = '';
            var pattern = this.valueOf();
            while (count > 0) {
                if (count & 1) {
                    result += pattern;
                }
                count >>= 1;
                pattern += pattern;
            }
            return result;
        };

        function Indent (code, amount, useSpace) {
            return code.split('\n').map(
                function(s) {
                    c = useSpace ? ' ' : '\t';
                    return c.repeat(amount) + s;
                }
            ).join('\n');
        }

        BuildCSS();
    </script>
