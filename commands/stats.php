<?php
	if(!$message->query) {
		$chat->send($helper->getString(stats_error));
		//todo: display stats about amount of commands and the total amount they've been used (not each, but together)
		//like "I know X commands, and together they've been used X times!"
	} else {
		$db = $helper->db;
		// $query = "SELECT COUNT(command), COUNT(DISTINCT chat_id), COUNT(DISTINCT sender_id) FROM log WHERE command = ?";
		// try {
		// 	if(!$stmt = $db->prepare($query)) throw new Exception("Database server error", 500);
		// 	if(!$stmt->bind_param('s', $message->queryNC)) throw new Exception("Database server error", 500);
		// 	if(!$stmt->execute()) throw new Exception("Database server error", 500);
		// 	if(!$stmt->bind_result($commandcount, $chatcount, $usercount)) throw new Exception("Database server error", 500);
		// 	if(!$stmt->fetch()) throw new Exception("Database server error", 500);

		// 	$chat->send($helper->getString(stats_response, array($message->queryNC, $commandcount, $chatcount, $usercount)));
		// } catch (Exception $e) {
		// 	$chat->send($e->getMessage());
		// }
	}
?>