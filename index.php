<?php

/*  -------------------------------------------------------------
    Nasqueron Tools
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Sébastien Santoro aka Dereckson
    Project:        Nasqueron
    Created:        2013
    Licence:        Released under BSD license
    -------------------------------------------------------------    */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

define('IN_KERUALD', true);
define('IN_PLUTON', true);
define('NO_SESSION', true);

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

if (!NO_SESSION) {
	$Session = Session::load();
	$CurrentUser = $Session->get_logged_user();
}

////////////////////////////////////////////////////////////////////////////////
///
/// Serves the requested page
///

$url = get_current_url();
$document = new Document($url);
$document->render();
