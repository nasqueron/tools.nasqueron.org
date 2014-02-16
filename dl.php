<?php

/**
 * Nasqueron Tools
 *
 * Entry point to download a file
 *
 * @package     NasqueronTools
 * @subpackage  EntryPoints
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @filesource
 *
 */

$filename = isset($_REQUEST['filename']) ? $_REQUEST['filename'] : '';
$contentType = isset($_REQUEST['contentType']) ? $_REQUEST['contentType'] : 'text/plain';

header('Content-Type: ' . $contentType);
if ($filename !== '') {
    header("Content-Disposition: attachment; filename=\"$filename\"");
}
header('Pragma: no-cache');
header('Expires: 0');

echo $_REQUEST['result'];
