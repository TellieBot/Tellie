<?PHP
// -> Sender class that contains information about the sender
//    of the command
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
class Sender {
 	public $id;
	public $first_name;
	public $username;

	function __construct($sender = null) {
		$this->id = $sender['id'];
		$this->first_name = $sender['first_name'];
		$this->username = $sender['username'];
	}
}
?>