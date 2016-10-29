<?php
// -> Serves up a bing image
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
	$keyboard = array('force_reply' => true,selective => true); $chat->sendKeyboard($helper->getString(search_error_withoutquery, "image"), $keyboard, $message->id); die();
}

// Open API and browse to result
$ServiceRootURL =  'https://api.datamarket.azure.com/Bing/Search/v1/';
$WebSearchURL = $ServiceRootURL . 'Image?$format=json&Adult=%27Strict%27&Query=%27'.urlencode($message->query).'%27';
$context = stream_context_create(array(
    'http' => array(
        'request_fulluri' => true,
        'header'  => "Authorization: Basic " . base64_encode($bingApiKey . ":" . $bingApiKey)
    )
));

// This is not a $helper function, because BING needs context
$response = file_get_contents($WebSearchURL, 0, $context);       
$json = json_decode($response);

$media = $json->d->results[$resultNo]->MediaUrl;
if ($media){
	$helper->saveFile($media,"files/cache/cache.jpg"); 
	$chat->sendImage('"'.ucfirst($message->query).'"',"files/cache/cache.jpg", $message->id);
}else{
	$chat->send($helper->getString(error_noresults, $message->query),$message->id);
}

?>