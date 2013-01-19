<?php

/*
 * Keruald, core libraries for Pluton and Xen engines.
 * (c) 2010, Sébastien Santoro aka Dereckson, some rights reserved
 * Released under BSD license
 *
 * Error handling
 *
 * 0.1    2010-02-27 16:00    DcK
 *
 * @todo add exception handling facilities
 *
 * There are 3 standard error types:
 *  - SQL_ERROR         error during a sql query
 *  - HACK_ERROR        error trying to access a protected resource
 *  - GENERAL_ERROR     miscelleanous error
 *
 * The message_die/SQL_ERROR idea were found in phpBB 2 code.
 *
 * Tip: use HACK_ERROR when an user can't access a page, and edit message_die
 *      to output a login/pass form if the user isn't logged in, so the user
 *      will be invited to log in properly and legetimely access to the page.
 *      (cf. the Pluton's error.php for a sample)
 *
 * Tip: if you use a MVC model or at least templates, message_die should calls
 *      an error template but only if the template engine is initialized.
 *      (cf. the Xen's    error.php for a sample)
 *
 * Tip: evaluate the cost/benefit to output a SQL error to the user and consider
 *      not to output the sql query or the error code to standard users.
 *
 * Tip: if you need more help to understand where exactly the error have occured
 *      consider Advanced PHP debugger: www.php.net/manual/en/book.apd.php
 */

//Error code constants
define ("SQL_ERROR",      65);
define ("HACK_ERROR",     99);
define ("GENERAL_ERROR", 117);

/*
 * Prints human-readable information about a variable
 * wrapped in a general error and dies
 * @param mixed $mixed the variable to dump
 */
function dieprint_r ($var, $title = '') {
    if (!$title) $title = 'Debug';
    
    //GENERAL_ERROR with print_r call as message
    message_die(GENERAL_ERROR, '<pre>' . print_r($var, true) .'</pre>', $title);
}

/*
 * Prints an error message and dies
 * @param int $code A constant identifying the type of error (SQL_ERROR, HACK_ERROR or GENERAL_ERROR)
 * @param string $text the error description
 * @param string $text the error title
 * @param int $line the file line the error have occured (typically __LINE__)
 * @param string $file  the  file the error have occured (typically __FILE__)
 * @param string $sql the sql query which caused the error
 */
function message_die ($code, $text = '', $title = '', $line = '', $file = '', $sql = '') {
    //Ensures we've an error text
    $text = $text ? $text : "An error have occured";

    //Adds file and line information to error text
    if ($file) {
        $text .= " — $file";
        if ($line) {
            $text .= ", line $line";
        }
    }
    
    //Ensures we've an error title and adds relevant extra information
    switch ($code) {
        case HACK_ERROR:
            $title = $title ? $title : "Access non authorized";
            break;
        
        case SQL_ERROR:
            global $db;
            $title = $title ? $title : "SQL error";
            
            //Gets SQL error information
            $sqlError = $db->sql_error();
            if ($sqlError['message'] != '') {
                $text .= "<br />Error n° $sqlError[code]: $sqlError[message]";
            }
            $text .= '<br />&nbsp;<br />Query: ';
            $text .= $sql;
            
            break;
        
        default:
            //TODO: here can be added code to handle error error ;-)
            //Falls to GENERAL_ERROR
        
        case GENERAL_ERROR:
            $title = $title ? $title : "General error";
            break;
    }
    
    //HTML output of $title and $text variables
    echo '<div class="FatalError"><p class="FatalErrorTitle">', $title,
         '</p><p>', $text, '</p></div>';
    
    exit;
}
?>