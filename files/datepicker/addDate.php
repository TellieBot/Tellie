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
$result->free();

$readableDate = date('(D) j M',strtotime(urldecode($_GET['theDateStamp'])));

$db->query("INSERT INTO DatePickerDates(pickerid,datestamp,title) VALUES('{$picker['id']}','".strtotime(urldecode($_GET['theDateStamp']))."','{$readableDate}')") or die($db->error);
$db->close();

echo "Added {$readableDate}";
?>