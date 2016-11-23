<?php
session_start();
$loggedIn = false;
if(isset($_SESSION['id']))
{
	$loggedIn = true;
	//header('Location: /unread.php');
	//die();
}

require_once("functions.php");
$activePage = "home";
$clientIp = getClientIp();
require_once("header.php");
require_once("default.php");
require_once("footer.php");
?>
