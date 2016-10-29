<?php
	// To do: make more better.
	$result = eval('return '.$message->query.';');
	$chat->send($result,$message->id);
?>