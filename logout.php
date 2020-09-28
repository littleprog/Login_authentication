#!/usr/local/bin/php
<?php
//specify session save path
session_save_path(dirname(realpath(__FILE__)) . '/sessions/');
//resume session
session_name('AccessingWebsite');
//start the session
session_start();
//send the user back to the login page
header('Location: index.php');
//clear all variables created in session
session_unset();
 ?>
