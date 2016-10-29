<?PHP
// -> /more returns another result by the last executed command
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

// Step 1: Get the last query and position in the results (defaults to 0, each /more increments it with one)
$db = $helper->db;
$chatInfo = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM Chats WHERE id='".$chat->id."' LIMIT 1"));
$moreCounter = $chatInfo['morecounter']+1;

// Include original command with original query
$message->query = $chatInfo['lastquery'];
include('commands/'.$chatInfo['lastcommand'].'.php');

// Update more counter
mysqli_query($db,"UPDATE Chats SET morecounter=morecounter+1 WHERE id='".$chat->id."' LIMIT 1")
?>