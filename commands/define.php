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
	$keyboard = array('force_reply' => true,selective => true); $chat->sendKeyboard($helper->getString(search_error_withoutquery, "definition"), $keyboard, $message->id); die();
}

// Open UD API and browse to result
$json = $helper->getFromApi('http://api.urbandictionary.com/v0/define?term=' . urlencode($message->query));

if ($json['result_type'] == 'no_results'){
	// Nothing found
	$reply = $helper->getString(error_noresults, $message->query);
}else{
	// Formulate a response based on API
	$jsonObj = $json['list'][$resultNo];
	if (empty($jsonObj['definition'])){
		$reply = $helper->getString(more_error);
	}else{
		if ($jsonObj['example']){$reply = $helper->getString(define_response1,array($jsonObj['word'],$jsonObj['author'],$jsonObj['definition'],$jsonObj['example'],$jsonObj['permalink']));}
		else{$reply = $helper->getString(define_response2,array($jsonObj['word'],$jsonObj['author'],$jsonObj['definition'],$jsonObj['permalink']));}
	}
}

// Send our reply
$chat->send($reply, $message->id, true);
?>