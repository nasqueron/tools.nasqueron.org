<?php

/*
 * Keruald, core libraries for Pluton and Xen engines.
 * (c) 2010, Sébastien Santoro aka Dereckson, some rights reserved
 * Released under BSD license
 *
 * Core
 *
 * 0.1    2010-02-27 2:04    DcK
 */

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Configures PHP and loads site-wide used libraries                        ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Disables register globals
ini_set('register_globals', 'off');

//Reports all errors, help notices (including STRICT in PHP 6)
error_reporting(E_ALL & ~E_NOTICE);

//Load config
require_once("default-config.php");

if (file_exists("config.php")) {
    include_once("config.php");
}

//Load libraries
include_once("error.php");               //Error management
include_once("mysqli.php");             //MySQL layer
include_once("session.php");           //Sessions handler
include_once("autoload.php");         //Autoloader

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Information helper functions                                             ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets the username matching specified user id
 * @param string $user_id the user ID
 * @return string the username
 */
function get_username ($user_id) {
    $db = sql_db::load();

    $user_id = $db->sql_escape($user_id);
    $sql = 'SELECT username FROM '. TABLE_USERS . " WHERE user_id = '$userid'";
    return $db->sql_query_express($sql, "Can't get username from specified user id");
}

/*
 * Gets the user id matching specified username
 * @param string $username the username
 * @return string the user ID
 */
function get_userid ($username) {
    $db = sql_db::load();

    $username = $db->sql_escape($username);
    $sql = 'SELECT user_id FROM '. TABLE_USERS . " WHERE username LIKE '$username'";
    return $db->sql_query_express($sql, "Can't get user id from specified username");
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Misc helper functions                                                    ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

//Plural management

/*
 * Gets a "s" if the specified amount requests the plural
 * @param mixed $amount the quantity (should be numeric)
 * @return string 's' if the amount is greater or equal than 2 ; otherwise, ''
 */
function s ($amount) {
    if ($amount >= 2 || $amount <= -2 ) return 's';
}

/*
 * Prints human-readable information about a variable, wrapped in a <pre> block
 * @param mixed $mixed the variable to dump
 */
function dprint_r ($mixed) {
    echo '<pre>';
    print_r($mixed);
    echo '</pre>';
}

/*
 * Generates a new GUID
 * @return string a guid (without {})
 */
function new_guid () {
    //The guid chars
    $chars = explode(',', 'a,b,c,d,e,f,0,1,2,3,4,5,6,7,8,9');

    //Let's build our 36 characters string
    //e.g. 68ed40c6-f5bb-4a4a-8659-3adf23536b75
    $guid = "";
    for ($i = 0 ; $i < 36 ; $i++) {
        if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
            //Dashes at position 9, 14, 19 and 24
            $guid .= "-";
        } else {
            //0-f hex digit elsewhere
            $guid .= $chars[mt_rand() % sizeof($characters)];
        }
    }
    return $guid;
}

/*
 * Determines if the expression is a valid guid (in uuid notation, without {})
 * @param string $expression the guid to check
 * @return true if the expression is a valid guid ; otherwise, false
 */
function is_guid ($expression) {
    //We avoid regexp to speed up the check
    //A guid is a 36 characters string
    if (strlen($expression) != 36) return false;

    $expression = strtolower($expression);
    for ($i = 0 ; $i < 36 ; $i++) {
        if ($i == 8 || $i == 13 || $i == 18 || $i == 23) {
            //with dashes
            if ($expression[$i] != '-') return false;
        } else {
            //and hex numbers
            if (!is_numeric($expression[$i]) && $expression[$i] != 'a' && $expression[$i] != 'b' && $expression[$i] != 'c' && $expression[$i] != 'd' && $expression[$i] != 'e' && $expression[$i] != 'f' ) return false;
        }
    }
    return true;
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Files, extensions and directories                                        ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Gets files in a directory
 *
 * @param string $dir The directory files are located (optional, by default the current directory)
 * @param string $extension The extension to lookup without initial dot (optional, return every file if omitted)
 * @return array the files in the specified directory optionally filtered by extension
 */
function get_files ($dir = '.', $extension = NULL) {
    $handle = opendir($dir);
    $files = [];
    while ($file = readdir($handle)) {
        if ($file == '.' || $file == '..' || is_dir($file)) {
            continue;
        }
        if ($extension === NULL || get_extension($file) == $extension) {
            $files[] = $file;
        }
    }
    return $files;
}

/**
 * Gets file extension
 *
 * @param string $file the file to get the extension
 */
function get_extension ($file) {
    return pathinfo($file, PATHINFO_EXTENSION);
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// Strings manipulation                                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/**
 * Determines if a string starts with specified substring
 *
 * @param string $haystack the string to check
 * @param string $needle the substring to determines if it's the start
 * @param boolean $case_sensitive determines if the search must be case sensitive
 * @return boolean true if $haystack starts with $needle ; otherwise, false.
 */
function string_starts_with ($haystack, $needle, $case_sensitive = true) {
    if (!$case_sensitive) {
        $haystack = strtoupper($haystack);
        $needle = strtoupper($needle);
    }
    if ($haystack == $needle) return true;
    return strpos($haystack, $needle) === 0;
}

/**
 * Gets the portion of the string between $includeFrom and $includeTo
 */
function string_between ($haystack, $from, $to, $includeFrom = false, $includeTo = false) {
    //Gets start position
    $pos1 = strpos($haystack, $from);
    if ($pos1 === false) {
        return "";
    }
    if (!$includeFrom) $pos1 += strlen($from);

    //Gets end position
    $pos2 = strpos($haystack, $to, $pos1 + strlen($from));
    if ($pos2 === false) {
        return substr($haystack, $pos1);
    }
    if ($includeTo) $pos2 += strlen($to);

    //Gets middle part
    return substr($haystack, $pos1, $pos2 - $pos1);
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL helpers functions                                                    ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets URL
 * @return string URL
 */
function get_url () {
    global $Config;
    if (func_num_args() > 0) {
        $pieces = func_get_args();
        return $Config['BaseURL'] . '/' . implode('/', $pieces);
    } elseif ($Config['BaseURL'] == "" || $Config['BaseURL'] == "/index.php") {
        return "/";
    } else {
        return $Config['BaseURL'];
    }
}

/*
 * Gets page URL
 * @return string URL
 */
function get_page_url () {
    $url = $_SERVER['SCRIPT_NAME'] . $_SERVER['PATH_INFO'];
    if (substr($url, -10) == "/index.php") {
        return substr($url, 0, -9);
    }
    return $url;
}

/*
 * Gets server URL
 * @todo find a way to detect https:// on non standard port
 * @return string the server URL
 */
function get_server_url () {
    if (isset($_SERVER['HTTP_HOST']) && str_contains($_SERVER['HTTP_HOST'], ":")) {
        list($name, $port) = explode(':', $_SERVER['HTTP_HOST'], 2);
    } else {
        $port = $_SERVER['SERVER_PORT'];
        $name = $_SERVER['SERVER_NAME'];
    }

    switch ($port) {
        case '80':
            return "http://$name";

        case '443':
            return "https://$name";

        default:
            return "http://$name:$port";
    }
}

/*
 * Gets $_SERVER['PATH_INFO'] or computes the equivalent if not defined.
 * @return string the relevant URL part
 */
function get_current_url () {
    global $Config;

    //Gets relevant URL part from relevant $_SERVER variables
    if (!empty($_SERVER['PATH_INFO'])) {
        //Without mod_rewrite, and url like /index.php/controller
        //we use PATH_INFO. It's the easiest case.
        return $_SERVER["PATH_INFO"];
    }

    //In other cases, we'll need to get the relevant part of the URL
    $current_url = get_server_url() . $_SERVER['REQUEST_URI'];

    //Relevant URL part starts after the site URL
    $len = strlen($Config['SiteURL']);

    //We need to assert it's the correct site
    if (substr($current_url, 0, $len) != $Config['SiteURL']) {
        dieprint_r(GENERAL_ERROR, "Edit includes/config.php and specify the correct site URL<br /><strong>Current value:</strong> $Config[SiteURL]<br /><strong>Expected value:</strong> a string starting by " . get_server_url(), "Setup");
    }

    //Last possibility: use REQUEST_URI or REDIRECT_URL, but remove QUERY_STRING
    //TODO: handle the case of a nginx misconfiguration, where the query_string have been removed.
    //      e.g. 'fastcgi_param  SCRIPT_FILENAME  $document_root/index.php;' will remove the QS.
    //      (a working could be $document_root/index.php?$query_string);
    $url = array_key_exists('REDIRECT_URL', $_SERVER) ? $_SERVER["REDIRECT_URL"] : $_SERVER["REQUEST_URI"];
    $url = substr(get_server_url() . $url, $len);

    $pos = strpos($url, '?');
    if ($pos !== false) {
        return substr($url, 0, $pos);
    }
    return $url;
}

/*
 * Gets an array of url fragments to be processed by controller
 * @return array an array containing URL fragments
 */
function get_current_url_fragments () {
    $url_source = get_current_url();
    if ($url_source == '/index.php') return array();
    return explode('/', substr($url_source, 1));
}

/**
 * Gets the URL for the specified resources
 *
 * @param ... string a arbitray number of path info
 */
function get_url_for () {
    global $Config;
    $url = get_server_url() . '/' . $Config['BaseURL'];
    if (func_num_args()) {
        $url .= implode('/', func_get_args());
    }
    return $url;
}

/**
 * Gets directory relative to the site root
 *
 * @param string $dir the absolute path
 * @return string the relative directory to the site root
 */
function get_directory ($dir) {
    $rootPath = dirname(__DIR__);
    $rootPathLen = strlen($rootPath);
    if (substr($dir, 0, $rootPathLen) != $rootPath) {
        throw new InvalidArgumentException("Directory $dir doesn't start by root directory $rootPath.");
    }
    return substr($dir, ++$rootPathLen);
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL xmlHttpRequest helpers functions                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets an hash value to check the integrity of URLs in /do.php calls
 * @param array $args the args to compute the hash
 * @return the hash paramater for your xmlHttpRequest url
 */
function get_xhr_hash ($args) {
    global $Config;

    array_shift($args);
    return md5($_SESSION['ID'] . $Config['SecretKey'] . implode('', $args));
}

/*
 * Gets the URL to call do.php, the xmlHttpRequest controller
 * @return string the xmlHttpRequest url, with an integrity hash
 */
function get_xhr_hashed_url () {
    global $Config;

    $args = func_get_args();
    $args[] = get_xhr_hash($args);
    return $Config['DoURL'] . '/' . implode('/', $args);
}

/*
 * Gets the URL to call do.php, the xmlHttpRequest controller
 * @return string the xmlHttpRequest url
 */
function get_xhr_url () {
    global $Config;

    $args = func_get_args();
    return $Config['DoURL'] . '/' .implode('/', $args);
}
