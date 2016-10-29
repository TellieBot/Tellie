<?PHP
// -> Chat class that contains information about the chat
//    that the command and query was sent to. Also implements
//    various send() methods.
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

class Chat {
  public $id;
  public $title;
  public $enabled;
  private $helper;

  function __construct($id = null, $title = null) {
    $this->id = $id;
    $this->title = $title;
    //$this->enabled = $this->getStatus();
    $this->helper = new Helper();
  }

  /**
   * Performs the actual HTTP POST with which we send messages/media to Telegram
   * url - The full URL to which we send our request, already containing the basic post fields
   * [file] - The file we're sending, if any
   */
  private function putTelegram($url, $file) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    
    if($file) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type:multipart/form-data"
      ));
      curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
    }

    curl_exec($ch);
  }

  // FUNCTION send(this->helper,text,reply_to,disable_web_page_previews)
  // -- Sends a message to this chat
  // this->helper - A reference to the this->helper object
  // Text - The text to send
  // [reply_to] - ID of the message to reply to
  // [disable_web_page_previews] - Set flag to disable web previews
  public function send($text, $reply_to = false, $disable_web_page_preview = false) {
        $post_fields = array(
                        'chat_id' => $this->id,
                        'parse_mode' => 'Markdown',
                        'text' => $text,
                      );

    if ($reply_to) $post_fields = $post_fields + array('reply_to_message_id' => $reply_to);

    if ($disable_web_page_preview) $post_fields = $post_fields + array('disable_web_page_preview' => 'true');

    $url = $this->helper->API_URL . "sendMessage?" . http_build_query($post_fields);

    $this->putTelegram($url);
    $this->log("SNDMSG", "Sending message \"" . $text . "\"");
  }


  // FUNCTION sendAudio(this->helper,text,reply_to,disable_web_page_previews)
  // -- Sends a song to this chat
  // this->helper - A reference to the this->helper object
  // file - Path to the file
  // [artist] - Artist name
  // [title] - Song title
  // [reply_to] - ID of the message to reply to
  public function sendAudio($filepath, $artist='Unknown', $title = 'Unknown', $reply_to = false, $replyMarkup=false) {
    $post_fields = array(   
                        'chat_id' => $this->id,
                        'performer' => $artist,
                        'title' => $title
                        );
    
    if ($reply_to) $post_fields = $post_fields + array('reply_to_message_id' => $reply_to);
    
    $url = $this->helper->API_URL . "sendAudio?" . http_build_query($post_fields);

    $file = array('audio' => new CURLFile(realpath($filepath)));

    $this->putTelegram($url, $file);
    $this->log("SNDAUD", "Sending audio \"" . $filepath . "\"");
  }


  // FUNCTION sendImage(this->helper,text,reply_to,disable_web_page_previews)
  // -- Sends an image to this chat
  // this->helper - A reference to the this->helper object
  // file - Path to the file
  // [reply_to] - ID of the message to reply to
  public function sendImage($text,$filepath, $reply_to = false,$filetype='photo') {
        $post_fields = array(
                        'chat_id' => $this->id,
                        'parse_mode' => 'Markdown',
                        'caption' => $text
                      );
    
    if ($reply_to) $post_fields = $post_fields + array('reply_to_message_id' => $reply_to);
    
    $url = $this->helper->API_URL . "send".ucfirst($filetype)."?" . http_build_query($post_fields);

    $file = array($filetype => new CURLFile(realpath($filepath)));

    $this->putTelegram($url, $file);
    $this->log("SNDIMG", "Sending image \"" . $filepath . "\"");
  }




  // FUNCTION sendKeyboard(this->helper,text,reply_to,disable_web_page_previews)
  // -- Variant of send with keyboard or forcereply support

function sendKeyboard($text, $keyboard, $reply_to_message_id=false, $disable_web_page_preview=false) {
  $post_fields = array('chat_id' => $this->id,
    'parse_mode' => 'Markdown',
    'text' => $text
  );
  
  if ($reply_to_message_id) $post_fields = $post_fields + array('reply_to_message_id' => $reply_to_message_id);
  
  if ($disable_web_page_preview) $post_fields = $post_fields + array('disable_web_page_preview' => 'true');
  
  $url = $this->helper->API_URL . "sendMessage?" . http_build_query($post_fields);

  $ch = curl_init(); 
  curl_setopt($ch, CURLOPT_URL, $url); 
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('reply_markup' => $keyboard)));
  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
  $output = curl_exec($ch);
}

  // FUNCTION send(this->helper,text,reply_to,disable_web_page_previews)
  // -- Sends a message to this chat
  // this->helper - A reference to the this->helper object
  // Text - The text to send
  // [reply_to] - ID of the message to reply to
  // [disable_web_page_previews] - Set flag to disable web previews
  public function answerCallbackQuery($text,$id) {
        $post_fields = array(
                        'callback_query_id' => $id,
                        'text' => $text
                      );

    $url = $this->helper->API_URL . "answerCallbackQuery?" . http_build_query($post_fields);

    $this->putTelegram($url);
  }

  // FUNCTION updateMessage()
  // -- Updates an existing message
  public function updateMessage($text, $msgid = false,$keyboard=false) {
        $post_fields = array(
                        'chat_id' => $this->id,
                        'message_id' => $msgid,
                        'parse_mode' => 'Markdown',
                        'text' => $text
                      );


    $url = $this->helper->API_URL . "editMessageText?" . http_build_query($post_fields);

    if ($keyboard){
      $ch = curl_init(); 
      curl_setopt($ch, CURLOPT_URL, $url); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('reply_markup' => $keyboard)));
      curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
      $output = curl_exec($ch);
    }else{
      $this->putTelegram($url);
    }
  }

  public function log($event, $details) {
    $db = $this->helper->db;
    $db->query("INSERT INTO Log (chatid, event, details) VALUES (" . $this->id . ", " . $event . ", " . $details . ")");
  }
}
?>