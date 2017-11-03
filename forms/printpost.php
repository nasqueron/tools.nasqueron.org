<?php
if (count($_POST)) {
    echo "    <h2>POST request content</h2>\n";
    dprint_r($_POST);
} else {
    echo "    <p>Posts something to this URL to see the content.</p>\n";
}
