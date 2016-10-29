<?PHP
// -> This script will be pinged to check and send reminders
/****************************************\
|*                                      *|
|*          @TellieBot source           *|
|*    2016, Niels Bik & Vic van Cooten  *|
|*                                      *|
|*      Feel free to use this code      *| 
|*  to build and improve your own bot!	*|
|*                 👌                    *|
|*                                      *|
\****************************************/
header('Access-Control-Allow-Origin: *');

// Definitions
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Autoload classes
spl_autoload_register(function($class){
	include $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $class . '.php';
});

// Instantiate helper and prepare db
$helper = new Helper();
$db = $helper->db;

// Query reminders past their due
$result = $db->query("SELECT * FROM Reminders WHERE sent=0 and remindertime < ".time());
echo "SELECT * FROM Reminders WHERE sent=0 and remindertime < ".time();
// Send a message for reach reminder
while ($reminder = $result->fetch_assoc()) {
	$chat = new Chat($reminder['chatid']);
	if ($reminder['attachment']){
		// Reminder with attachment
		$chat->send($helper->getString(remindme_reminderattachment,array($reminder['username'],$reminder['userfirstname'],$reminder['attachmentsender'],$reminder['messageid'])), $reminder['attachment'], true);	
	}else{
		// No attachment, regular reminder
		$chat->send($helper->getString(remindme_reminder,array($reminder['username'],$reminder['userfirstname'],$reminder['remindertext'],$reminder['messageid'])), $reminder['messageid'], true);	
	}
	
}

// Register the fact that we just sent this one
$db->query("UPDATE Reminders SET sent=1 WHERE remindertime < ".time());
$db->query("DELETE FROM Reminders WHERE remindertime < ".strtotime("-1 days"));

$result->free();
$db->close();
?>