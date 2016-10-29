<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Autoload classes
spl_autoload_register(function($class){
	include $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $class . '.php';
});

// Set up DB
$helper = new Helper();
$db = $helper->db;
$result = $db->query("SELECT * FROM DatePickers WHERE authcode2='".$db->escape_string($_GET['auth'])."' LIMIT 1");
$picker = $result->fetch_assoc();

// For each date, add a button
$chat = new Chat($picker['chatid']);

$keyboard = $helper->dp_generatebuttons($picker['id']);
if (!$keyboard){
	die("Maybe add some dates before publishing?");
}

// Send with buttons
$shortname = preg_replace("/[^A-Za-z0-9]/", "", $picker['name']);
$msg = preg_replace("/〰〰〰〰〰〰〰〰/","〰〰〰〰\n".$helper->dp_findPopular($picker['id'])."\n〰〰〰〰",$helper->getString(datepicker_mainpost,array($picker['user_firstname'],$picker['name'],$picker['id'],$helper->dp_generatebuttons($picker['id'],true),$shortname)));
$chat->sendKeyboard($msg, $keyboard, $message->id); 
$result->free();
$db->close();


?>Date picker was published to chat!