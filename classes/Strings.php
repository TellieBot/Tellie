<?PHP
// -> The 🔑 to success is having good strings
/****************************************\
|*                                      *|
|*          @TellieBot source           *|
|*    2016, Niels Bik & Vic van Cooten  *|
|*                                      *|
|*      Feel free to use this code      *| 
|*  to build and improve your own bot!  *|
|*                  👌                   *|
|*                                      *|
\****************************************/

class Strings {
/* Just found out these apparently aren't required. Kill this comment if the bot still functions after a week. 
	public $dev_error_mysql;

	public $error_global;
	public $error_noresults;
	public $error_debugmode;
	public $define_response1;
	public $define_response2;
	public $search_error_withoutquery;
	public $spotify_txt;
	public $more_error;
	public $stats_error;
	public $stats_response;
	public $remindme_confirm;
	public $remindme_past;
	public $remindme_mode_timefromnow;
	public $remindme_reminder;
	public $remindme_reminderattachment;
	public $reminder_delete_error_permission;
	public $reminder_delete_error_deleted;
	public $reminder_delete_success;
	public $reminder_snooze_confirm;
	public $remindme_mode_snoozetimefromnow;
	public $datepicker_make_errornotitle;
	public $datepicker_confirm_auth;
	public $datepicker_confirm_auth_button;
	public $datepicker_auth_error;
	public $datepicker_admin_msg;
	public $datepicker_admin_button;
	public $datepicker_admin_intro;
	public $datepicker_mainpost;
	public $datepicker_mainpost_noyet;
	public $datepicker_overview_intro;
	public $datepicker_overview_overflow;
	public $datepicker_overview_text;
	public $datepicker_pickdate_error_notoption;
	public $datepicker_pickdate_error_permission;
	public $datepicker_finalise_success;
*/

  function __construct($id = null) {
  	// Dev responses
  	$this->dev_error_mysql = "*Oops, mysql error:* ";
  	$this->dev_error_app = "Message @TellieBotSupport for help.";

  	// Global responses
	$this->error_global = "Something went wrong. Sorry!";
	$this->error_noresults = $this->get_random(array("Whoops.","Sorry!","Oh dear.","Oh no!","Gosh.","Uck.","I'm sorry.",'Cheese it.',"You won't believe this.","Wow.","This hasn't happened to me before.","This is a little emberassing.","Yikes!","So.. You won't like this, but.."))." ".$this->get_random(array("I was unable to find anything for *%s*.","I tried, but couldn't find anything for *%s.*.","I've looked all over the interwebs, but *%s* was nowhere to be found."))." ".$this->get_random(array("☹️","😱","😳","😩","😅","😶","😐","😑","😒","🙄🤔","😞","😟","😔","😕","🙁","☹️","😣","😖","😫","😩","😮","😱","😨","😰","😯","😦","😧","😢","😥","😪","😓","😭","😵","😲","🤐","💩","👎🏼","🕵"));
	$this->error_debugmode = "Sorry! I'm currently being flushed. It's not a pleasant experience, but it has to be done! ☹️ I'll be back!";
	$this->search_error_withoutquery = "What kind of %s are you looking for?";
	$this->error_notimezone = "*Tip:* I don't know your timezone yet. Some time-related commands may behave weirdly. Please send me your location to set the correct timezone. You can also reply with your country's name.";
	$this->confirmation_timezone = "Thanks. I've set your timezone to %s.";

	// Specific responses
	// /Define
	$this->define_response1 = "Top result for *%s* (by %s):\n\n%s\n\n_%s_\n\n*Find more definitions at »* %s";
	$this->define_response2 = "Top result for *%s* (by %s):\n\n%s\n\n*Find more definitions at »* %s";

	// Remindme
	$this->remindme_confirm = $this->get_random(array("Okay.","Sure!",'Gotcha.','👍','👌','No problem.','I gotchu fam.'))." %s ".$this->get_random(array("I'll remind you about *%s*.","I'll make sure you won't forget *%s*.","I'll buzz you about *%s*.","*%s* will be on top of your mind. I'll make sure of that.","you can trust me to remind you about *%s*."))."\n\n*Options*\n_▫️ You can say cancel in reply to this message to cancel this reminder. (#%s)_\n_▫️ You can ask when the reminder is, or how much time is still left_\n_▫️ You can snooze this reminder to a later time_";
	$this->remindme_past = "Looks like you're setting a reminder in the past. I'll just set the reminder for *%s* instead. Use the snooze function to change.";
	$this->remindme_mode_timefromnow = "in";
	$this->remindme_mode_snoozetimefromnow = "until";
	$this->remindme_reminder = "@%s ".$this->get_random(array('Hi ','Heya ','Yo ','Hey ','_Ding-dong_ ','*PING* ',''))."%s! ".$this->get_random(array("Here's your reminder about *%s*.","I promised to remind you about *%s*.","Don't forget about *%s*.","This is a friendly reminder about *%s*.", "Remember *%s*?","You know *%s* you didn't want to forget about? Well.. Don't!"))."\n\n_Reply with `Snooze + how long` to snooze this reminder (#%s)._";
	$this->remindme_reminderattachment = "@%s ".$this->get_random(array('Hi ','Heya ','Yo ','Hey ','_Ding-dong_ ','*PING* ',''))."%s! ".$this->get_random(array("Remember that message from %s you didn't want to forget about?","Don't forget about the message from %s.","I'm just here to remind you about the message from %s."))." You can use my reply to jump back to the message.\n\n_Reply with `Snooze + how long` to snooze this reminder (#%s)._";
	$this->reminder_delete_error_permission = "Yeaaah... You're not @%s. Can't do it, sorry.";
	$this->reminder_delete_error_deleted = "You're a bit late. This reminder was sent or deleted already.";
	$this->reminder_delete_success = "Reminder was unset. ".$this->get_random(array("I'll pretend like this never happened.","Won't do anything."));
	$this->reminder_snooze_confirm = "Snoozing %s";

	// /Spotify
	$this->spotify_txt = "You're listening to [%s](%s) by [%s](%s) (from [%s](%s))";

	// More
	$this->more_error = $this->get_random(array("That's all, folks.","That's all there is to it.","Nothing more to see here.","Apparently, you've seen it all.","Nothing more.","Alas. That was all.","I don't know what to say. That's all!"));

	// /Stats
	$this->stats_error = "Please provide me with a command you'd like to know the stats of";
	$this->stats_response = "The *%s* command has been used *%s* times across *%s* different chats by *%s* unique users so far!";

	// datepicker
	$this->datepicker_make_errornotitle = "What's the reason for your gettogether?";
	$this->datepicker_confirm_auth = "Let's finish setting up the date picker in a private chat, %s. Nobody likes group chat spam.";
	$this->datepicker_confirm_auth_button = "Click here, then press 'start' to set-up.";
	$this->datepicker_auth_error = "*Sorry. You're not %s.* \n\nThey were the one to set up the picker originally. Set up your own datepicker or ask @%s to add the dates you need added. ";
	$this->datepicker_admin_msg = "Use this button to add some dates to date picker *'%s'* and when finished send it to your original chat. ";
	$this->datepicker_admin_button = "Manage date picker";
	$this->datepicker_admin_intro_header = "Hi, %s.";
	$this->datepicker_admin_intro = "Continue to set up the date picker <b>'%s'</b> here!</p><p>You can select <i>up to 18</i> dates from the following datepicker. When you're ready press the floating button at the bottom right. I''ll send your dates to your friends immediately!";
	$this->datepicker_mainpost = "Hey all,\n\n%s wants to know when everyone is available for *'%s'*. Use the buttons below to let them know when you _are_ available so they can pick a date.\n\n*Popular dates*\n〰〰〰〰〰〰〰〰\n\n*Options*\n_▫️ Reply with 'Overview' to get a full availability overview (#%s)._\n_▫️ Reply with 'Pick date' plus one of the options (1-%s) to finalise a date._\n\nUse #%s to find all related messages. ";
	$this->datepicker_mainpost_noyet = "No preferred date yet.";
	$this->datepicker_overview_intro = "This is when people are available for '%s'.";
	$this->datepicker_overview_overflow = "And %s more";
	$this->datepicker_overview_text = "Here's an overview of availability. Reply with 'Pick date' plus one of the options (1-%s) to finalise a date (#%s). #%s";
	$this->datepicker_pickdate_error_notoption = "Pick a number from 1 to %s, please.";
	$this->datepicker_pickdate_error_permission = "Only @%s can finalise a date.";
	$this->datepicker_finalise_success = "%s will be the date for #%s. I'll send you a file so you can quickly add it to your calendar. *Have fun!*";
  }

  public function get_random($a) { 
    $k=array_rand($a);
    return $a[$k];
} 


}
?>