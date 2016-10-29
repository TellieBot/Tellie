<?PHP
// -> Initialize the datepicker set-up. The heavy lifting is done externally
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

// We're still going to need a title
if (empty($message->query)){ 
	$keyboard = array('force_reply' => true,selective => true); $chat->sendKeyboard($helper->getString(datepicker_make_errornotitle), $keyboard, $message->id); die();
}

// Set up database
$db = $helper->db;
$authcode=$helper->str_rand();
$db->query("INSERT INTO DatePickers(chatid,messageid,name,user,user_firstname,authcode,authcode2) VALUES({$chat->id},{$message->id},'".$db->escape_string($message->query)."','".$db->escape_string($sender->username)."','".$db->escape_string($sender->first_name)."','{$authcode}','".$helper->str_rand()."')") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));

// Create a button that sends the auth code to a private chat
$buttonlink = "https://telegram.me/TellieBot?start=".$authcode;
$x1 = array("text"=>$helper->getString(datepicker_confirm_auth_button),"url"=>$buttonlink);
$opz = [[$x1]];
$keyboard=array("inline_keyboard"=>$opz);
$chat->sendKeyboard($helper->getString(datepicker_confirm_auth,$sender->first_name), $keyboard, $message->id); 

?>