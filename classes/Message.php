	<?PHP
// -> Message class that contains information about the message
//    that contains the command and query that was sent to the
//    bot
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

class Message {
	public $id;
	public $text;
	public $sender;
	public $chat;
	public $replyTo;

	public $command;
	public $query;
	public $queryNC;

	private $helper;

	function __construct(Sender $sender, Chat $chat, $id = null, $text = null, $replyTo = null) {
		// Set message variables:
		$this->id = $id;
		$this->text = $text;
		$this->sender = $sender;
		$this->chat = $chat;
		$this->replyTo = $replyTo;

		$this->command = $this->getCommand();
		$this->query = $this->getQuery();
		$this->queryNC = $this->getQuery(false);

		// Initiate helper:
		$this->helper = new Helper();
	}

	private function getCommand() {
		$querytext = explode(' ', $this->text);
		return preg_replace('/\@telliebot/', '', ltrim(strtolower($querytext[0]), '/'));
	}

	private function getQuery($keep_case = true) {
		$querytext = explode(' ', $this->text);
		unset($querytext[0]);
		if($keep_case) return preg_replace('/\@' . strtolower($this->helper->BOT_NAME) . '/', '', implode(" ", $querytext));
		else return preg_replace('/\@' . strtolower($this->helper->BOT_NAME) . '/', '', strtolower(implode(" ", $querytext)));
	}

	public function log($chatid) {
		// Set up db connection:
		$db = $this->helper->db;

		// // Log last command
		$morecommands = array('img','spotify','gif','define','youtube');
		if (in_array($this->command,$morecommands)){
			// CODE IS HANDIG VOOR LATER mysqli_query($db, "INSERT INTO Chats(id, lastactive, lastcommand, lastquery) VALUES({$chatid}, '".time()."','".mysqli_real_escape_string($db,$this->command)."','".mysqli_real_escape_string($db,$this->queryNC)."') ON DUPLICATE KEY UPDATE morecounter = morecounter + 1");
			$db->query("INSERT INTO Chats(id, title, lastactive, lastcommand, lastquery) values('".$chatid."', '".$db->escape_string($this->chat->title)."', '".time()."','".mysqli_real_escape_string($db,$this->command)."','".mysqli_real_escape_string($db,$this->queryNC)."') ON DUPLICATE KEY UPDATE title='".$db->escape_string($this->chat->title)."', lastactive='".time()."', lastcommand='".mysqli_real_escape_string($db,$this->command)."', morecounter=morecounter+1, lastquery='".mysqli_real_escape_string($db,$this->queryNC)."'")  or $this->chat->send($this->helper->getString(dev_error_mysql).$db->error.'. '.$this->helper->getString(dev_error_app));
		}

	}
}
?>