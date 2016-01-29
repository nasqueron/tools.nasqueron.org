<?php

/*  -------------------------------------------------------------
    Nasqueron Tools
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Sébastien Santoro aka Dereckson
    Project:        Nasqueron
    Created:        2010
    Licence:        Released under BSD license
    -------------------------------------------------------------    */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

define('IN_KERUALD', true);
define('IN_PLUTON', true);

//Keruald libraries
include('includes/core.php');

//Pluton libraries
include('includes/document.php');

//Site libraries
include('includes/core2.php');

////////////////////////////////////////////////////////////////////////////////
///
/// Session
///

$Session = Session::load();
$CurrentUser = $Session->get_logged_user();

////////////////////////////////////////////////////////////////////////////////
///
/// Serves the requested page
///

/**
 * Forces bare display for broken mobiles
 *
 * @return true if header and footer must be skipped ; otherwise, false.
 */
function force_bare_display () {
	if (strpos($_SERVER["HTTP_USER_AGENT"], " NetFront/") !== false) {
		return true;
	}
	return false;
}

$url = get_current_url();
$document = new Document($url);
if (force_bare_display()) {
	$document->noheader = true;
	$document->nofooter = true;
}
$document->render();
