<?php
// -> Serves up an youtube video
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
	$keyboard = array('force_reply' => true,selective => true); $chat->sendKeyboard($helper->getString(search_error_withoutquery, "video"), $keyboard, $message->id); die();
}

// Open API and browse to result
$json = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet&q='.urlencode($message->query).'&key='.$googleApiKey), true);

		// loop through array, get variables for first actual video
		$count = 0; $found=0;
		while ($count < sizeof($json['items'])) {
			if ($json['items'][$count]['id']){
			if ($json['items'][$count]['id']['kind'] === 'youtube#video') {
				$found++;
				$videoID = $json['items'][$count]['id']['videoId'];
				$videoURL = 'https://www.youtube.com/watch?v='.$videoID;
				$videoname = $json['items'][$count]['snippet']['title'];
				$videodescription = $json['items'][$count]['snippet']['description'];
				if ($found==$moreCounter){
					break;	
				}
			}
			$count++;
			}
		}
		
		// send video link
		if ($videoID) {
			$chat->send("[{$videoname}]({$videoURL})  \n\n".$videodescription, $message->id);
		} else {
			$chat->send("Nothing found.");
		}	
	?>