<?php
// Definitions
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

$db->query("DELETE FROM DatePickerDates WHERE pickerid='{$picker['id']}'and datestamp='".strtotime(urldecode($_GET['theDateStamp']))."' LIMIT 1");
$db->close();

echo "Removed {$readableDate}";
?>