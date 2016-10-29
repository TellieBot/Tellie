<?PHP
// -> Contains helper functions that can be re-used often
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

class Helper {
  public $BOT_NAME;
  public $BOT_TOKEN;
  public $API_URL;

  public $db;

  public $strings;

  function __construct() {
    $this->BOT_NAME = BOT_NAME;
    $this->BOT_TOKEN = BOT_TOKEN;
    $this->API_URL = 'https://api.telegram.org/bot' . $this->BOT_TOKEN . '/';

    $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_BASE);
    $this->strings = new Strings();
  }

  // FUNCTION getFromApi(URL, [depth])
  // -- Fetches a JSON from an URL. Returns JSON as array. Optionally returns an object deeper in the array
  // URL - Api URL to fetch from
  // [depth] - specify how to navigate the hierarchy in the form of a one dimensional array. Each entry is a level.
  public function getFromApi($apiURL,$depths='') {
    //$context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
    $json = json_decode(file_get_contents($apiURL), true);

    if ($depths!=''){
      foreach($depths as $level){
        $json=$json[$level];
      }
    }

    return $json;
  }

  // FUNCTION saveFile(fromURL,toURL) 
  // -- Copies the bytestream from one place to another. For example to enable caching.
  // fromURL - (External) URL from which to download
  // toURL - (Internal) URL which to cache to
  public function saveFile($fromURL,$toURL) {
    $ch = curl_init($fromURL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $file = curl_exec($ch);
    curl_close($ch);

    $fp = fopen($toURL, 'w');
    fwrite($fp, $file);
    fclose($fp);
  }

    // FUNCTION getString(name,[replace])
  // -- Returns string. Optionally adds in variables.
  // name - Api URL to fetch from
  // [replace] - onedimensional array with variables
  function getString($n, $r = '') {
    $string = $this->strings->$n;
      if ($r){$string = vsprintf($string, $r);}
    return $string;
  }


    // FUNCTION str_rand($length)
  // -- Generates a random string
  // length - how long the string should be
function str_rand($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

    // FUNCTION parsyMcParseFace($q) 
  // -- Processes $q into a readable date format, a unix timestamp and a text query
  // q - Query to process
function parsyMcParseFace($q){

  // These words are not dates
  $dateExcluders = array('get','i','in','from','now','a','bot','until','x');

  // These words should be skipped for the text query
  $textExcluders = array("in","at","to",'next','about');

  // Extend strtotime() with some commonly used scenarios
  $specialTerms = array(
            "/^(tonight|evening|dinner(( )?time)?|dusk)$/" => "9pm",
            "/^(bedtime)$/" => "11pm",
            "/^(morning|sunrise|dawn)$/" => "7am",
            "/^(afternoon)$/" => "3pm",
            "/^min(s)?$/" => "minutes",
            "/^sec(s)?$/" => "seconds",
            "/^hr(s)?$/" => "hours",
            "/^(later|in a bit|soon)$/" => "3 hours"
    );


  $queryWords = explode(" ",$q);
  $timeWords = "";
  $textWords = "";
  $lastWord = "";

  // Loop through every word
  foreach($queryWords as $word){
    $word = trim(preg_replace(array_keys($specialTerms),array_values($specialTerms),$word));

    // Even though we loop per word, some words only make sense in groups
    $indicators = array('second','seconds','minute','minutes','hour','hours','day','days','week','weeks','month','months','year','years','am','pm');
    if (in_array(strtolower($word), $indicators)){
      $cheatWord = $lastWord." ".$word;
    }

    // If the word (or set of words) matches with strtotime(), it's a timeword
    

    if (((strtotime($word) && !in_array(strtolower($word),$dateExcluders)) ||  // Confirms it's a dateword and not in the list of excluders
        ($cheatWord && strtotime($cheatWord))) &&  // Same if it's a cheat word (group of words that mean nothing when standing alone)
        !preg_match('/'.strtolower($word).'/',strtolower($timeWords))){  // But, the second time we find a word we skip it, so it becomes a textword. Required for good McCree behaviour.
      if ($cheatWord){
        $timeWords .= " ".$cheatWord;
        $textWords = $this->str_lreplace($lastWord,'',$textWords);
      }else{
        $timeWords .= " ".$word;
      }
    }else{
    // Otherwise it's a textword
      if (!in_array(strtolower($word),$textExcluders)){
        $textWords .= " ".$word;
      }
    }
    $cheatWord = ''; 
    $lastWord = $word;
    }

    $timeWords = trim($timeWords);$textWords = trim($textWords);

    // No query given? Just remind them in an hour.
    if (!$timeWords){$timeWords="1 hour";}
    if (!$textWords){$textWords="*this*";}


    return array($timeWords,$textWords,strtotime($timeWords));
  }

// Replace last occurrence
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if($pos !== false)
    {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

// Pickdate - Generate an array of buttons
function dp_generatebuttons($pickerid,$count=false){
  $counter=0;
  $db = $this->db;
  $results = $db->query("SELECT * FROM DatePickerDates WHERE pickerid='{$pickerid}' ORDER BY datestamp ASC");
  $row=0;$pos=0; $opz = [[]]; // Change $row to 1 for accept all
  $opz[0][0] = array("text"=>"Accept all","callback_data"=>$pickerid.'|||acceptall');
  while ($possibleDate = $results->fetch_assoc()) {
    $counter++;
    $opz[$row][$pos] = array("text"=>"{$counter}. ".$possibleDate['title'],"callback_data"=>$possibleDate['pickerid']."|||".$possibleDate['id'].'|||'.$possibleDate['title']);
    $pos++;
    if ($pos==2){
      $pos=0;
      $row++;
    }
  }
  if (!$counter){
    return false;
  }
  if (!$count){
    return array("inline_keyboard"=>$opz);  
  }else{
    return $counter;
  }
  
}

// Pickdate - Find most popular options
function dp_findPopular($pickerid,$no=4){
  $str = "";
  $db = $this->db;
  /*$results = $db->query(" SELECT d.pickerid,d.title,a.pickerid,COUNT(DISTINCT uid) AS sno
                          FROM DatePickerDates as d
                          LEFT JOIN DatePickerDatesAvailability AS a ON d.pickerid=a.pickerid
                          WHERE d.pickerid='{$pickerid}' LIMIT {$no}");*/
$results = $db->query("SELECT title, COUNT(a.uid) as numberAvailable, GROUP_CONCAT(a.firstname SEPARATOR ', ') as nameList
FROM `DatePickerDates` AS d
LEFT JOIN `DatePickerDatesAvailability` AS a
ON a.dateid=d.id
WHERE a.pickerid='{$pickerid}'
GROUP BY a.dateid
ORDER BY numberAvailable DESC
LIMIT {$no}") or die($db->error);
$counter = 1;
  while ($popularDate = $results->fetch_assoc()) {
    $namelist = $this->str_lreplace(',',' and',$popularDate['nameList']);
    $str .= "〰 {$popularDate['title']} ({$namelist} available) \n";
    $counter++;
  }
  $str = substr($str,0,-1);
  if(!$str){$str = $this->getString(datepicker_mainpost_noyet);}
  return $str;
}
}
?>
