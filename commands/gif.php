<?php
// -> Serves up an Urban Dictionary definition
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

// Init function
$reply = '';

// If this query was called in a /more-fashion, use the right number.
$resultNo = 0;
if ($moreCounter){$resultNo=$moreCounter;}

// Exception for when there's no query
// Request query from user via reply
if (empty($message->query)){ 
	$keyboard = array('force_reply' => true,selective => true); $chat->sendKeyboard($helper->getString(search_error_withoutquery, "gif"), $keyboard, $message->id); die();
}

// Open API and browse to result
$url = 'https://api.giphy.com/v1/gifs/search?q='.urlencode($message->query).'&api_key=' . $urbanDictApiKey;
$json = json_decode(file_get_contents($url), true);
$url = $json['data'][$resultNo]['images']['downsized']['url'];


if (!$url){
	// Nothing found
	$reply = $helper->getString(error_noresults, $message->query);
}else{
	$helper->saveFile($url,"files/cache/cache.gif"); // Artwork
	$chat->sendImage('"'.$message->query.'"',"files/cache/cache.gif", $message->id,'document');

}
?>
