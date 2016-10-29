<?PHP
//-> Handle callback queries for datepicker
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

// Set up context
$sender = new Sender($update['callback_query']['from']);
$chat = new Chat($update['callback_query']['message']['chat']['id']);
$message = new Message($sender, $chat, $update['callback_query']['message']['message_id'], $update['callback_query']['message']['text'],$update['callback_query']['message']['reply_to_message']);
$helper = new Helper();

// Get payload
$payload = explode("|||",$update['callback_query']['data']);

// Update availability
$db = $helper->db;

// Check if availability is true
if ($payload[1]!="acceptall"){
	// Single date
	$q = $db->query("SELECT * FROM DatePickerDatesAvailability WHERE pickerid='".$db->escape_string($payload[0])."' and dateid='".$db->escape_string($payload[1])."' and username='".$db->escape_string($sender->username)."' LIMIT 1");
	$result = $q->fetch_assoc();

	if ($result['pickerid']){
		// Is already available, remove availability
		$db->query("DELETE FROM DatePickerDatesAvailability WHERE pickerid='".$db->escape_string($payload[0])."' and username='".$db->escape_string($sender->username)."' and dateid='".$db->escape_string($payload[1])."'");
		$chat->answerCallbackQuery("You are now unavailable on {$payload[2]}",$update['callback_query']['id']);
	}else{
		// Isn't available yet, add availability
		$db->query("REPLACE INTO DatePickerDatesAvailability(pickerid,dateid,username,firstname,uid) VALUES('".$db->escape_string($payload[0])."','".$db->escape_string($payload[1])."','".$db->escape_string($sender->username)."','".$db->escape_string($sender->first_name)."','".$helper->str_rand()."' )");
		$chat->answerCallbackQuery("You are now available on {$payload[2]}",$update['callback_query']['id']);
	}
}else{
	// All dates
	// bish whet
}

// Update message

// Replace string in original message (dp_findmostpopular generates the text)
$updatedMSG = preg_replace("/〰〰〰〰(.*)〰〰〰〰/s","〰〰〰〰\n".$helper->dp_findPopular($payload[0])."\n〰〰〰〰",$message->text);//preg_replace("/〰〰〰〰(.*)〰〰〰〰/s","〰〰〰〰\n".$payload[0]."\n〰〰〰〰",$update['callback_query']['message']['text']).$helper->str_rand();


// Regenerate the buttons
$keyboard = $helper->dp_generatebuttons($payload[0]);

// Update the message with these findings
$chat->updateMessage($updatedMSG,$message->id,$keyboard);


?>