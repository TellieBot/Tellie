<?PHP
// -> Is used to authorize date pickers and send the management link
/****************************************\
|*                                      *|
|*          @TellieBot source           *|
|*    2016, Niels Bik & Vic van Cooten  *|
|*                                      *|
|*      Feel free to use this code      *| 
|*  to build and improve your own bot!  *|
|*                  👌                  *|
|*                                      *|
\****************************************/

// Set up DB
$db = $helper->db;
$result = $db->query("SELECT * FROM DatePickers WHERE authcode='".$db->escape_string($message->query)."' LIMIT 1");
$picker = $result->fetch_assoc();

// Send a message for reach reminder
if ($sender->username==$picker['user']){
	$buttonlink = "https://telliebot.me/datepicker/".$picker['authcode2'];
	$x1 = array("text"=>$helper->getString(datepicker_admin_button),"url"=>$buttonlink);
	$opz = [[$x1]];
	$keyboard=array("inline_keyboard"=>$opz);
	$chat->sendKeyboard($helper->getString(datepicker_admin_msg,$picker['name']), $keyboard, $message->id); 
}else{
	if ($message->query){ // If not, we don't have a payload. Don't send an error!
		$chat->send($helper->getString(datepicker_auth_error,array($picker['user_firstname'],$picker['user'])), $message->id);	
	}	
}


?>