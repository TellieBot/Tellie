<?php
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

// Load configurations and definitions
include('config.php');
define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/');

// Autoload classes
spl_autoload_register(function($class){
	include $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $class . '.php';
});

// Get incoming telegram data
$update = json_decode(file_get_contents("php://input"), true);
if (!$update) {
	exit;
}

// Process the incoming message
if (isset($update["message"])) {
	// Instantiate classes
	$sender = new Sender($update['message']['from']);
	$chat = new Chat($update['message']['chat']['id'], $update['message']['chat']['title']);
	$message = new Message($sender, $chat, $update['message']['message_id'], $update['message']['text'],$update['message']['reply_to_message']);
	$helper = new Helper();
	
	// Todo - better debug mode as flushing may be required from time to time
	if (DEBUG_MODE){
		$chat->send($helper->getString(error_debugmode), $message->id);
		die();
	}

	if ($message->replyTo){
		include("files/inreply.php");
	}else{
		// Run the command
		if (include('commands/' . $message->command . '.php')) {
			$message->log($chat->id);
		}
	}
}else if (isset($update["callback_query"])) {
	if (DEBUG_MODE){die();}
	include("files/callbackquery.php");
}

// Nothing more to it!
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="refresh" content="2; url=https://telliebot.me" />
	</head>
	<body>
		Redirecting...
	</body>
</html>