<?php
include $_SERVER['DOCUMENT_ROOT'] . '/config.php';

// Autoload classes
spl_autoload_register(function($class){
	include $_SERVER['DOCUMENT_ROOT'] . '/classes/' . $class . '.php';
});

// Set up DB
$helper = new Helper();
$db = $helper->db;
$result = $db->query("SELECT * FROM DatePickers WHERE authcode2='".$db->escape_string($_GET['auth'])."' LIMIT 1");
$picker = $result->fetch_assoc();

$preloaddates = "";
$results = $db->query("SELECT * FROM DatePickerDates WHERE pickerid='{$picker['id']}'");
$row=0;$pos=0; $opz = [[]];
while ($possibleDate = $results->fetch_assoc()) {
  $preloaddates.="date.setFullYear(".date('Y',$possibleDate['datestamp']).",".(date('m',$possibleDate['datestamp'])-1).",".date('d',$possibleDate['datestamp'])."),";
}
if ($preloaddates){
  $preloaddates = "addDates: [".$preloaddates."],";
}

$result->free();
$db->close();
?>
<html>
	<head>
 		<title>Tellie Date Picker</title>

 		<!-- CSS -->
  		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/css/materialize.min.css">
  		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
  		<link rel="stylesheet" href="lib/mdp/css/mdp.css">
  		<style>
  			html,body{
  				margin:0;padding:0;
  			}

  			#datepicker{
  				margin:0 auto;
  			}

        #toast-container {
          right: auto;
          top: auto;
          left: 25px;
          bottom: 25px;
        }
          .toast {
            float: left;
          }
  		</style>

  		<!-- Javascript -->
  		<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script> 
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		<script src="lib/mdp/jquery-ui.multidatespicker.js"></script>
  		<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>

  		<script>
  			$(function() {
  				availableSpace=Math.floor($('.container').width()/266);
          date = new Date();
		    	$( "#datepicker" ).multiDatesPicker({
            firstDay: 1,
		    		maxPicks:18,
					numberOfMonths: [1,availableSpace],
          <?=$preloaddates;?>
					onSelect: function(dateText) {
						ds = String($('#datepicker').multiDatesPicker('getDates'));
						if(ds.match(dateText)){
							// selected
			  				$.get( "addDate.php",{ auth: "<?=$_GET['auth'];?>", theDateStamp: dateText}, function( data ) {	
								Materialize.toast(data,500);
							})
						}else{
							// unselected
			  				$.get( "removeDate.php",{ auth: "<?=$_GET['auth'];?>", theDateStamp: dateText}, function( data ) {	
								Materialize.toast(data,500);
							})
						}
					},
				});
  		   	});


  			function sendPoll(){
  				$.get( "publishToChat.php",{ auth: "<?=$_GET['auth'];?>"}, function( data ) {	
  					alert(data);
				});
  			}
  		</script>

  		<!-- Meta -->
  		<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
      <link rel="shortcut icon" href="tellie.ico" />
	</head>
	<body>
  		<nav class='blue darken-3'>
	    	<div class="nav-wrapper" style='padding-left:15px;'>
		      <a href="#" class="brand-logo"><?=ucfirst($picker['name']);?></a>
		      <ul id="nav-mobile" class="right hide-on-med-and-down">
		      </ul>
		    </div>
	  	</nav>

      <div class="container">
        <h1><?=$helper->getString(datepicker_admin_intro_header,$picker['user_firstname']);?></h1>
        <p><?=$helper->getString(datepicker_admin_intro,$picker['name']);?></p>
        <div id="datepicker"></div>
      </div>

  <a class="btn-floating btn-large waves-effect waves-light blue darken-3" href='javascript:sendPoll();' style='position:fixed;right:25px;bottom:25px;'><i class="material-icons">launch</i></a>

	</body>
</html>