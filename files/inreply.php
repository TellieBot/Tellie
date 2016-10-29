<?PHP
$botMsg = $message->replyTo['text'];
$db = $helper->db;

/*
| Search without query
*/
if (strstr($botMsg,"What kind of image")){
	$message->command = "img"; $message->query = $message->text; $message->queryNC = strtolower($message->text);
	include("commands/img.php");
	$message->log($chat->id);
}
if (strstr($botMsg,"What kind of definition")){
	$message->command = "define"; $message->query = $message->text; $message->queryNC = strtolower($message->text);
	include("commands/define.php");
	$message->log($chat->id);
}
if (strstr($botMsg,"What kind of song")){
	$message->command = "spotify"; $message->query = $message->text; $message->queryNC = strtolower($message->text);
	include("commands/spotify.php");
	$message->log($chat->id);
}
if (strstr($botMsg,"What kind of gif")){
	$message->command = "gif"; $message->query = $message->text; $message->queryNC = strtolower($message->text);
	include("commands/gif.php");
	$message->log($chat->id);
}
if (strstr($botMsg,"What kind of video")){
	$message->command = "youtube"; $message->query = $message->text; $message->queryNC = strtolower($message->text);
	include("commands/youtube.php");
	$message->log($chat->id);
}
if (strstr($botMsg,"the reason for your")){
	$message->command = "pickdate"; $message->query = $message->text; $message->queryNC = strtolower($message->text);
	include("commands/pickdate.php");
	$message->log($chat->id);
}




/*
| Reminders
*/
/* Catch: person has replied to bot message (triggering this file instead of the regular command). Redirect them to the regular command.*/
if (strstr(strtolower($message->text),"remindme")){
	$message->command = "remindme"; 
	include("commands/remindme.php");
	$message->log($chat->id);
}

/* Cancel Reminders */
if (strstr($botMsg,"to cancel this") && ((strstr(strtolower($message->text),"cancel") || strstr(strtolower($message->text),"delete")))){
	// Get reminder # from botMsg
	$reminderno = explode('(#',$botMsg);$reminderno = explode(')',$reminderno[1]);$reminderno=$reminderno[0];

	// Fetch the reminder
	$result = $db->query("SELECT * FROM Reminders WHERE chatid={$chat->id} AND messageid={$reminderno} LIMIT 1");
	$reminder = $result->fetch_assoc();

	if (!$reminder['id'] || $reminder['sent']=='1'){
		// Reminder was already deleted
		$chat->send($helper->getString(reminder_delete_error_deleted), $message->id);	
	}else{
		if ($reminder['username'] == $sender->username){
			$result = $db->query("DELETE FROM Reminders WHERE chatid={$chat->id} AND messageid={$reminderno} LIMIT 1");
			$chat->send($helper->getString(reminder_delete_success), $message->id);
		}else{
			$chat->send($helper->getString(reminder_delete_error_permission,$reminder['username']), $message->id);	
		}
	}
}

/* When is this? */
if (strstr($botMsg,"message to cancel this") && strstr(strtolower($message->text),"when")){
	// Get reminder # from botMsg
	$reminderno = explode('(#',$botMsg);$reminderno = explode(')',$reminderno[1]);$reminderno=$reminderno[0];

	// Set timezone
	$q = $db->query("SELECT * FROM Chats WHERE id='".$db->escape_string($chat->id)."' LIMIT 1") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));
	$chatDB = $q->fetch_assoc();
	$timezone = $chatDB['timezone'];

	if ($timezone){
		date_default_timezone_set($timezone);
	}

	// Fetch the reminder
	$result = $db->query("SELECT * FROM Reminders WHERE chatid={$chat->id} AND messageid={$reminderno} LIMIT 1");
	$reminder = $result->fetch_assoc();

	if (!$reminder['id']){
		// Reminder was already deleted
		$chat->send($helper->getString(reminder_delete_error_deleted), $message->id);	
	}else{
			$chat->send(date("F jS Y (G:i)",$reminder['remindertime']), $message->id);
	}
}

if (strstr($botMsg,"message to cancel this") && preg_match("/(how( many| long| much))/",strtolower($message->text))){
	// Get reminder # from botMsg
	$reminderno = explode('(#',$botMsg);$reminderno = explode(')',$reminderno[1]);$reminderno=$reminderno[0];

	// Set timezone
	$q = $db->query("SELECT * FROM Chats WHERE id='".$db->escape_string($chat->id)."' LIMIT 1") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));
	$chatDB = $q->fetch_assoc();
	$timezone = $chatDB['timezone'];

	if ($timezone){
		date_default_timezone_set($timezone);
	}

	// Fetch the reminder
	$result = $db->query("SELECT * FROM Reminders WHERE chatid={$chat->id} AND messageid={$reminderno} LIMIT 1");
	$reminder = $result->fetch_assoc();

	if (!$reminder['id']){
		// Reminder was already deleted
		$chat->send($helper->getString(reminder_delete_error_deleted), $message->id);	
	}else{
		$timeleft = round(($reminder['remindertime']-time())/60/60);
		if ($timeleft==0){
			$timeleft = round(($reminder['remindertime']-time())/60);
			if ($timeleft==1){$timeleft.=" minute";}else{$timeleft.=" minutes";}	
		}else{
			if ($timeleft==1){$timeleft.=" hour";}else{$timeleft.=" hours";}	
		}
		
		$chat->send($timeleft." from now", $message->id);
	}
}

if ((strstr($botMsg,"to snooze this reminder") || strstr($botMsg,"message to cancel this")) && strstr(strtolower($message->text),"snooze")){
	$q = $message->query;
	$parsed = $helper->parsyMcParseFace($q);
	$reminderno = explode('(#',$botMsg);$reminderno = explode(')',$reminderno[1]);$reminderno=$reminderno[0];


	// Set timezone
	$q = $db->query("SELECT * FROM Chats WHERE id='".$db->escape_string($chat->id)."' LIMIT 1") or $chat->send($helper->getString(dev_error_mysql).$db->error.'. '.$helper->getString(dev_error_app));
	$chatDB = $q->fetch_assoc();
	$timezone = $chatDB['timezone'];
	if ($timezone){
		date_default_timezone_set($timezone);
	}

	$result = $db->query("SELECT * FROM Reminders WHERE chatid={$chat->id} AND messageid={$reminderno} LIMIT 1");
	$reminder = $result->fetch_assoc();

	if ($reminder['username'] == $sender->username){
		// If the timestamp is 'X from now', we need to say 'in X from now'
		$timeText = $helper->getString(remindme_mode_snoozetimefromnow).' '.$parsed[0];
		
		$db->query("UPDATE Reminders SET remindertime={$parsed[2]}, sent=0 WHERE chatid='".$chat->id."' AND messageid ='".$reminderno."'");
		$chat->send($helper->getString(reminder_snooze_confirm,array($timeText)),$message->id);
	}else{
		$chat->send($helper->getString(reminder_delete_error_permission,$reminder['username']), $message->id);	
	}

}

/*
| Datepicker
*/
// Get overview (actual overview is m ade in /files/datepicker/getOverview.php)
if ((strstr($botMsg,"to get a full availability overview") || strstr($message->replyTo['caption'],"finalise a date")) && strstr(strtolower($message->text),"overview")){
	$msg = $botMsg; if(!$msg){$msg = $message->replyTo['caption'];}
	$optioncounter = explode('(1-',$msg);$optioncounter = explode(')',$optioncounter[1]);$optioncounter=$optioncounter[0];
	$pickerid = explode('(#',$msg);$pickerid = explode(')',$pickerid[1]);$pickerid=$pickerid[0];
	$helper->saveFile("https://bot.telliebot.me/files/datepicker/getOverview.php?id=".$pickerid."", 'files/cache/datepicker.png');
	$result = $db->query("SELECT * FROM DatePickers WHERE id={$pickerid} LIMIT 1");
	$picker = $result->fetch_assoc();
	$shortname = preg_replace("/[^A-Za-z0-9]/", "", $picker['name']);
	$chat->sendImage($helper->getString(datepicker_overview_text,[$optioncounter,$pickerid,$shortname]),"files/cache/datepicker.png",$message->id);
}

// Finalize date
if ((strstr($botMsg,"to get a full availability overview") || strstr($message->replyTo['caption'],"finalise a date")) && strstr(strtolower($message->text),"pick")){
	$msg = $botMsg; if(!$botMsg){$msg = $message->replyTo['caption'];}
	$optioncounter = explode('(1-',$msg);$optioncounter = explode(')',$optioncounter[1]);$optioncounter=$optioncounter[0];
	$pickerid = explode('(#',$msg);$pickerid = explode(')',$pickerid[1]);$pickerid=$pickerid[0];
	$no = filter_var($message->text,FILTER_SANITIZE_NUMBER_INT);
	
	$result = $db->query("SELECT * FROM DatePickers WHERE id={$pickerid} LIMIT 1");
	$picker = $result->fetch_assoc();

	// Authorization check
	if ($sender->username==$picker['user']){
		// Picked option was never an option
		if ($no>$optioncounter){
			$chat->send($helper->getString(datepicker_pickdate_error_notoption,$optioncounter),$message->id);
			die();
		}

		// We're good to go. Pick date!
		$result = $db->query("SELECT * FROM DatePickerDates WHERE pickerid={$pickerid} ORDER BY datestamp LIMIT ".($no-1).",1");
		$option = $result->fetch_assoc();
		// Set ID as finalised date
		$result = $db->query("UPDATE DatePickers SET finaldate='{$option['id']}' WHERE id='{$pickerid}' LIMIT 1");
		$shortname = preg_replace("/[^A-Za-z0-9]/", "", $picker['name']);
		$chat->send($helper->getString(datepicker_finalise_success,[date('F jS',$option['datestamp']),$shortname]));
		$helper->saveFile("https://bot.telliebot.me/files/datepicker/getICS.php?id=".$pickerid, "files/cache/{$shortname}.ics");
		$chat->sendImage('',"files/cache/{$shortname}.ics", false,'document');
		unlink("files/cache/{$shortname}.ics");
	}else{
		$chat->send($helper->getString(datepicker_pickdate_error_permission,$picker['user']),$message->id);
	}
}

/*
| Timezone
*/
if (strstr($botMsg,"Please send me your location")){
	$lat = $update['message']['location']['latitude'];
	$long = $update['message']['location']['longitude'];

	if (!$lat){
		// Probably a text message. Google it!
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($message->text);
		$json = json_decode(file_get_contents($url), true);
		$lat = $json['results'][0]['geometry']['location']['lat'];
		$long = $json['results'][0]['geometry']['location']['lng'];
	}
	
		function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
		    $timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
		                                    : DateTimeZone::listIdentifiers();

		    if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

		        $time_zone = '';
		        $tz_distance = 0;

		        //only one identifier?
		        if (count($timezone_ids) == 1) {
		            $time_zone = $timezone_ids[0];
		        } else {

		            foreach($timezone_ids as $timezone_id) {
		                $timezone = new DateTimeZone($timezone_id);
		                $location = $timezone->getLocation();
		                $tz_lat   = $location['latitude'];
		                $tz_long  = $location['longitude'];

		                $theta    = $cur_long - $tz_long;
		                $distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat))) 
		                + (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
		                $distance = acos($distance);
		                $distance = abs(rad2deg($distance));
		                // echo '<br />'.$timezone_id.' '.$distance; 

		                if (!$time_zone || $tz_distance > $distance) {
		                    $time_zone   = $timezone_id;
		                    $tz_distance = $distance;
		                } 

		            }
		        }
		        return  $time_zone;
		    }
		    return 'unknown';
		}

		$timezonename=get_nearest_timezone($lat,$long);
		$db->query("UPDATE Chats SET timezone='".$db->escape_string($timezonename)."' WHERE id='".$chat->id."'") or $chat->send($db->error);
		$chat->send($helper->getString(confirmation_timezone,$timezonename),$message->id);
	}
?>
