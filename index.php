<?php

/*
 * Keruald, core libraries for Pluton and Xen engines.
 * (c) 2010, Sébastien Santoro aka Dereckson, some rights reserved
 * Released under BSD license
 *
 * Application entry point
 *
 * Keruald is mainly a repository for common libraries elements between
 * engines like Pluton (content-oriented site) and Xen (MVC).
 *
 * You should consider to start with one of those.
 *
 */

////////////////////////////////////////////////////////////////////////////////
///
/// Initialization
///

//Keruald libraries
include('includes/core.php');

//Pluton libraries
include('includes/document.php');

////////////////////////////////////////////////////////////////////////////////
///
/// Session
///

//[TODO] If your session contains classes, and you don't implement __autoload,
//you've to require those items before session_start();
//You can implement this here or in _includes/sessions.php

//Starts a new session or recovers current session
$Session = Session::load();

//Handles login or logout
include("includes/login.php");

//Gets current user information
$CurrentUser = $Session->get_logged_user();

////////////////////////////////////////////////////////////////////////////////
///
/// Your application initialization logic
///

//[TODO] Loads your template engine or prepares the document to print
//[TODO] Loads languages file if you're into L10n

////////////////////////////////////////////////////////////////////////////////
///
/// Serves the requested page
///

$url = get_current_url();
$theme = $Config['Theme'];
$document = new Document($url);

include("themes/$theme/header.php");
$document->render();
include("themes/$theme/footer.php");
