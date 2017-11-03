<?php

/*
 * Keruald, core libraries for Pluton and Xen engines.
 * (c) 2010, Sébastien Santoro aka Dereckson, some rights reserved
 * Released under BSD license
 *
 * Login and logout handler.
 *
 * 0.1    2010-02-27 1:52    DcK
 *
 */

if ($_POST['LogIn']) {
    //User have submitted login form
    $username = $db->sql_escape($_POST['username']);
    $sql = "SELECT user_password, user_id FROM " . TABLE_USERS . " WHERE username = '$username'";
    if ( !($result = $db->sql_query($sql)) ) message_die(SQL_ERROR, "Can't get user information", '', __LINE__, __FILE__, $sql);
        if ($row = $db->sql_fetchrow($result)) {
            if (!$row['user_password']) {
                //No password set
                $LoginError = "This account exists but haven't a password defined. Contact the site administrator.";
            } elseif ($row['user_password'] != md5($_POST['password'])) {
                //The password doesn't match
                $LoginError = "Incorrect password.";
            } else {
                //Login successful
                Session::load()->user_login($row['user_id']);
                $LoginSuccessful = true;
            }
    }
} elseif ($_POST['LogOut'] || $_GET['action'] == "user.logout") {
    //User have submitted logout form or clicked a logout link
    Session::load()->user_logout();
}
