<?PHP
// -> Generates an image overview of everyone's availability
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
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Autoload classes
spl_autoload_register(function($class){
	include $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $class . '.php';
});

// Set up DB
$helper = new Helper();
$db = $helper->db;
$result = $db->query("SELECT * FROM DatePickers WHERE id='".$db->escape_string($_GET['id'])."' LIMIT 1");
$picker = $result->fetch_assoc();

// Create blank image
header("Content-Type: image/png");
$im = imagecreatetruecolor(1200, 850);
$bg = imagecreatefromgif("bg.gif");
imagecopyresampled($im, $bg, 0, 0, 0, 0, 1200, 850, 1200, 850);

// Vars
$offsetY = 200;

// Colours
$white = imagecolorallocate($im, 255, 255, 255);
$black = imagecolorallocate($im, 0, 0, 0);
$tellie_color = imagecolorallocatealpha($im, 106, 145, 177,40);

// Dates
$results = $db->query("SELECT GROUP_CONCAT(datestamp SEPARATOR '|||') as dates,GROUP_CONCAT(id SEPARATOR '|||') as ids FROM DatePickerDates WHERE pickerid='{$picker['id']}'");
$dates = $results->fetch_assoc();
$dateArray = explode('|||',$dates['dates']);
$dateIds = explode('|||',$dates['ids']);

// Size and quantity calculators
$rowCount = ceil(count($dateArray)/6);
$cellCount = count($dateArray); if($cellCount>6){$cellCount=6;}
$squareWidth = (1190 / $cellCount);
if ($squareWidth>500){$squareWidth=500;}
$squareHeight = (640 / $rowCount);
if ($squareHeight>500){$squareHeight=500;}
// Loop through all the dates to emulate bounding boxes and find the smallest font size that will fit all headers
$headerSize=28;$absNo=1;
foreach($dateArray as $date){
	$bounding_box = imagettfbbox(18, 0, "../fonts/roboto/RobotoCondensed-Bold.ttf", $absNo.". ".date("F j (D)",$date));
	$fontWidthRadio = floor((18/(abs($bounding_box[4] - $bounding_box[0])+30))*$squareWidth); // Calculate the font size, based on the bounding box
	if ($fontWidthRadio>36){$fontWidthRadio=36;}
	if($fontWidthRadio<$headerSize){$headerSize=$fontWidthRadio;}
	$absNo++;
}

// Some text
$introText = $helper->getString(datepicker_overview_intro,$picker['name']);
imagettftext ($im, 12 , 0, 5, 100 , $black, "../fonts/SpaceComics.ttf" , strtoupper($introText));
$explanation = "Reply with pick date + a number from below to make a date permanent.";
imagettftext ($im, 12 , 0, 330, 188 , $black, "../fonts/roboto/Roboto-Regular.ttf" , $explanation);

// Draw rectange per date.
$no=0;$row=0;$absNo=1;
foreach($dateArray as $date){ 		
	if ($no==6){
		$no=0;
		$row++;
	}
	$x = ($no*($squareWidth));
	$y = ($row*($squareHeight))+$offsetY;

	// Available people
	$results = $db->query("SELECT GROUP_CONCAT(username SEPARATOR '|||') as names FROM DatePickerDatesAvailability WHERE pickerid='{$picker['id']}' AND dateid='{$dateIds[$absNo-1]}'");

	$av = $results->fetch_assoc();
	$available = explode('|||',$av['names']);
	if ($av['names']){$noAv = count($available);}else{$noAv=0;}
	

	// Drol rekt
	imagefilledrectangle($im, $x+5, $y+5, (($no+1)*$squareWidth), $squareHeight+$y, $tellie_color);

	// Title
	imagettftext ($im, $headerSize , 0, $x+15, $y+($headerSize*2) , $white, "../fonts/roboto/RobotoCondensed-Bold.ttf" , $absNo.". ".date("F j (D)",$date));
	// X Available
	imagettftext ($im, $headerSize , 0, $x+15, $y+($headerSize*3.5) , $white, "../fonts/roboto/RobotoCondensed-Light.ttf" , $noAv." available");

	// Repeat for each available person, according to available bounding box
	$minFontSize = 80;$rowHeight=500;
	// Loop to find lowest common font size that fits
	foreach ($available as $a){
		$bounding_box = imagettfbbox(18, 0, "../fonts/roboto/Roboto-Regular.ttf", "@".$a);
		$fontWidthRadio = floor((18/(abs($bounding_box[4] - $bounding_box[0])+30))*$squareWidth); // Calculate the font size, based on the bounding box
		$rHeight=abs($bounding_box[5] - $bounding_box[1]);
		if ($fontWidthRadio>50){$fontWidthRadio=50;}
		if ($rHeight<$rowHeight){$rowHeight=$rHeight;}
		if ($fontWidthRadio<$minFontSize){
			$minFontSize=$fontWidthRadio;
		}
	}

	// Calculate how people we can show in a block before running out of space
	$rowsMax = Floor(($squareHeight-($headerSize*5))/$rowHeight);

	$avNo=0;
	// Loop to draw all the lines
	foreach ($available as $a){
		$avNo++;
		$bounding_box = imagettfbbox($minFontSize, 0, "../fonts/roboto/Roboto-Regular.ttf", "@".$a);
		$txtHeight = abs($bounding_box[5] - $bounding_box[1])*1.1;
		if ($avNo==$rowsMax){
			imagettftext ($im, $minFontSize , 0, $x+15, $y+($headerSize*4)+($avNo*$txtHeight), $white, "../fonts/roboto/Roboto-Regular.ttf" , $helper->getString(datepicker_overview_overflow,(count($available)-$rowsMax)+1));
		}else if ($avNo>$rowsMax){}else{
			if ($a){imagettftext ($im, $minFontSize , 0, $x+15, $y+($headerSize*4)+($avNo*$txtHeight), $white, "../fonts/roboto/Roboto-Regular.ttf" , "@".$a);}	
		}
		
	}

	$no++;$absNo++;
}


imagepng($im);
imagedestroy($im);


?>