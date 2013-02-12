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

//Load libraries
include_once("config.php");               //Site config
include_once("error.php");               //Error management
include_once("mysql.php");              //MySQL layer
include_once("session.php");           //Sessions handler

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
	global $db;
    
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
	global $db;
    
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

/*
 * Gets file extension
 * @param string $file the file to get the extension
 */
function get_extension ($file) {
    $dotPosition = strrpos($file, ".");
    return substr($file, $dotPosition + 1);
}

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
	switch ($port = $_SERVER['SERVER_PORT']) {
		case '80':
            return "http://$_SERVER[SERVER_NAME]";
        
        case '443':
            return "https://$_SERVER[SERVER_NAME]";
        
        default:
            return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]";
	}
}

/*
 * Gets $_SERVER['PATH_INFO'] or computes the equivalent if not defined.
 * @return string the relevant URL part
 */
function get_current_url () {
    global $Config;
            
    //Gets relevant URL part from relevant $_SERVER variables
    if (array_key_exists('PATH_INFO', $_SERVER)) {
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
    
    if (array_key_exists('REDIRECT_URL', $_SERVER)) {
        //With mod_rewrite, we can use REDIRECT_URL
        //We takes the end of the URL, ie *FROM* $len position
        return substr(get_server_url() . $_SERVER["REDIRECT_URL"], $len);
    }
    
    //Last possibility: use REQUEST_URI, but remove QUERY_STRING
    //If you need to edit here, use $_SERVER['REQUEST_URI']
    //but you need to discard $_SERVER['QUERY_STRING']
       
    //We takes the end of the URL, ie *FROM* $len position
    $url = substr(get_server_url() . $_SERVER["REQUEST_URI"], $len);
    
    //But if there are a query string (?action=... we need to discard it)	
    if ($_SERVER['QUERY_STRING']) {
        return substr($url, 0, strlen($url) - strlen($_SERVER['QUERY_STRING']) - 1);
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
    $url = get_server_url() . '/' . $Config[BaseURL];
    if (func_num_args()) {
        $url .= implode('/', func_get_args());
    }
    return $url;
}

////////////////////////////////////////////////////////////////////////////////
///                                                                          ///
/// URL xmlHttpRequest helpers functions                                     ///
///                                                                          ///
////////////////////////////////////////////////////////////////////////////////

/*
 * Gets an hash value to check the integrity of URLs in /do.php calls
 * @param Array $args the args to compute the hash
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
