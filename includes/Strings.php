<?PHP
// -> Contains re-usable strings for easy editing. Perhaps even multi-language support?
/****************************************\
|*                                      *|
|*          @TellieBot source           *|
|*    2016, Niels Bik & Vic van Cooten  *|
|*                                      *|
|*      Feel free to use this code      *| 
|*  to build and improve your own bot!  *|
|*                   👌                  *|
|*                                      *|
\****************************************/

$texts = [
                'error_global' => "Something went wrong. Sorry!",
                'error_noresults' => "Y U NO WORK",
                'error_debugmode' => "Sorry! I'm currently being flushed. It's not a pleasant experience, but it has to be done! ☹️ I'll be back!",

                'define_response1' => "Top result for *%s* (by %s):\n\n%s\n\n_%s_\n\n*Find more definitions at »* %s",
                'define_response2' => "Top result for *%s* (by %s):\n\n%s\n\n*Find more definitions at »* %s",

                'spotify_txt' => "You're listening to [%s](%s) by [%s](%s) (from [%s](%s))",

                'stats_error' => "Please provide me with a command you'd like to know the stats of",
                'stats_response' => "The *%s* command has been used *%s* times across *%s* different chats by *%s* unique users so far!"  
  ];


  // FUNCTION getString(name,[replace])
  // -- Returns string. Optionally adds in variables.
  // name - Api URL to fetch from
  // [replace] - onedimensional array with variables
  function getString($n, $r = '') {
    //$string = $strings['error_global'];
    //if (is_array($string)){$string=$exlaimers[rand(0,count($string)-1)];}
      //if ($r){$string = vsprintf($string, $r);}
    return $texts[1];
  }


function pickOne($a) { 
    $k=array_rand($a);
    return $a[$k];
} 

  //public $error_noresults = get_random(array("Whoops.","Sorry!","Oh dear.","Oh no!","Gosh.","Uck.","I'm sorry.","You won't believe this.","Wow. This hasn't happened to me before.","This is a little emberassing.","Yikes!"))." ".get_random(array("I was unable to find anything for *%s*","I tried, but couldn't find anything for *%s*","I've looked all over the interwebs, but *%s* was nowhere to be found."))." ".get_random(array("☹️","😱","😳","😩","😅","😶","😐","😑","😒","🙄🤔","😞","😟","😔","😕","🙁","☹️","😣","😖","😫","😩","😮","😱","😨","😰","😯","😦","😧","😢","😥","😪","😓","😭","😵","😲","🤐","💩","👎🏼","🕵"));


?>