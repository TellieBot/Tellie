<?php
// -> The fabled Spotify feature. If only all bots had this.
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
	$keyboard = array('force_reply' => true,selective => true); $chat->sendKeyboard($helper->getString(search_error_withoutquery, "song"), $keyboard, $message->id); die();
}

// Open Spotify API and browse to result
$json = $helper->getFromApi('https://api.spotify.com/v1/search?type=track&q='.urlencode($message->query),array('tracks'));

if ($json['total']=='0'){
	// Nothing found
	$chat->send($helper->getString(error_noresults, $message->query), $message->id, true);

}else{
	$jsonObj = $json['items'][$resultNo];

	if (empty($jsonObj['name'])){
		$chat->send($helper->getString(more_error),$message->id);
	}else{
		// Artwork & Music
		$helper->saveFile($jsonObj['album']['images'][1]['url'],"files/cache/cache.jpg"); // Artwork
		$chat->sendImage('"'.$jsonObj['album']['name'].'"',"files/cache/cache.jpg", $message->id);

		// 30-seconds preview
		$helper->saveFile($jsonObj['preview_url'],"files/cache/cache.mp3"); 
		$chat->sendAudio("files/cache/cache.mp3", $jsonObj['artists'][0]['name'], $jsonObj['name']);

		// Formatted message
		$reply = $helper->getString(spotify_txt,array($jsonObj['name'],$jsonObj['external_urls']['spotify'],$jsonObj['artists'][0]['name'],$jsonObj['artists'][0]['external_urls']['spotify'],$jsonObj['album']['name'],$jsonObj['album']['external_urls']['spotify']));

		$chat->send($reply, false, true);
	}
}

?>