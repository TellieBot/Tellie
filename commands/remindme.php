<?PHP
// -> These few lines of code will change the way you use a chat app.
/****************************************\
|*										*|
|*          @TellieBot source           *|
|*    2016, Niels Bik & Vic van Cooten  *|
|*										*|
|*      Feel free to use this code      *| 
|*  to build and improve your own bot!	*|
|*				   👌					*|
|*										*|
\****************************************/

$db = $helper->db;

// To consider: move this to $chat? Might be a bad idea.
$q = $db->query("SELECT * FROM Chats WHERE id='".$db->escape_string($chat->id)."' LIMIT 1") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));
$chatDB = $q->fetch_assoc();
$timezone = $chatDB['timezone'];

// Set timezone from DB
if ($timezone){
	date_default_timezone_set($timezone);
}


// Parse text
$parsed = $helper->parsyMcParseFace($message->query);


// If the timestamp is 'X from now', we need to say 'in X from now'
if (in_array(substr($parsed[0],0,1),array('a','1','2','3','4','5','6','7','8','9','0')) && !preg_match('/(am|pm)/',$parsed[0])){
	$timeText = ucfirst($helper->getString(remindme_mode_timefromnow)).' '.$parsed[0];
}else{
	$timeText = ucfirst($parsed[0]);
}

// If the timestamp is valid, set the reminder
if ($parsed[2]>time()){
	// Set reminder

	if($message->replyTo){
		// Reminder contains an attachment
		$db->query("INSERT INTO Reminders(chatid, messageid, remindertext, remindertime, username, userfirstname, attachment,attachmentsender) VALUES({$chat->id},{$message->id},'".$db->escape_string($parsed[1])."',{$parsed[2]},'".$db->escape_string($sender->username)."','".$db->escape_string($sender->first_name)."',".$message->replyTo['message_id'].",'".$db->escape_string($message->replyTo['from']['first_name'])."')") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));
	}else{
		$db->query("INSERT INTO Reminders(chatid, messageid, remindertext, remindertime, username, userfirstname) VALUES({$chat->id},{$message->id},'".$db->escape_string($parsed[1])."',{$parsed[2]},'".$db->escape_string($sender->username)."','".$db->escape_string($sender->first_name)."')") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));	
	}

	// Send a confirmation
	$reminderno = $message->id;
	$chat->send($helper->getString(remindme_confirm,array($timeText,$parsed[1],$reminderno)), $message->id, true);	
}else{
// If the timestamp is in  the past, propose a new time.
	// TODO - Propose a valid new time.

	// Cases:
	// XXM today - Tomorrow XXM
	// Yesterday - Tomorrow
	// Last X - Next X
if (preg_match('/yesterday/',strtolower($timeText))) {
	// If you're asking for a reminder for yesterday, you may be a bit odd.
    $suggestion = 'tomorrow';
} elseif (date('Ymd') == date('Ymd', $parsed[2])) {
	// So, we're trying to set a reminder for earlier today. That can happen!
    $suggestion = 'tomorrow '.$timeText;
} elseif (preg_match('/last/',strtolower($timeText))){
    $suggestion = preg_replace('/last/i','next',$timeText);
}
	$chat->send($helper->getString(remindme_past,array($suggestion)), $message->id, true);
	$message->query = "{$suggestion} {$parsed[1]}";
	include('remindme.php');

}

if (!$timezone){
	$chat->sendKeyboard($helper->getString(error_notimezone),array('force_reply'=>true));
}

?>