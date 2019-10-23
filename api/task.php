<?php
// Declare the credentials to the database
$dbconnecterror = FALSE;
$dbh = NULL;
require_once 'credentials.php';
try{
	
	$conn_string = "mysql:host=".$dbserver.";dbname=".$db;
	
	$dbh= new PDO($conn_string, $dbusername, $dbpassword);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
}catch(Exception $e){
	//$database issues were encountered
	http_response_code(504);
	echo "Database issues were encountered";
	exit();
}
if ($_SERVER['REQUEST_METHOD'] == "PUT") {
	if(array_key_exists('listID',$_GET)){
		$listID = $_GET['listID'];
	} else{
		http_response_code(400);
		echo "missing taskID";
		exit();
	}
	//decoding the json body from the request
	
	$task = json_decode(file_get_contents('php://input'), true);
	
	//ensure fields are in json
	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"];
	} else {
		//return 4xx error
		http_response_code(400);
		echo 'complete missing';
		exit();
	}
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		//return 4xx error
		http_response_code(400);
		echo "Missing task name";
		exit();
	}

	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task['taskDate'];
	} else {
		//return 4xx error
		http_response_code(400);
		echo "missing task date";
		exit();
	}

	if (!$dbconnecterror) {
		try {
			$sql = "UPDATE doList SET complete=:complete, listItem=:taskName, finishDate=:taskDate WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":complete", $complete);
			$stmt->bindParam(":taskName", $taskName);
			$stmt->bindParam(":taskDate", $taskDate);
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();	
			http_response_code(204); //no content
			exit();

		} catch (PDOException $e) {
			http_response_code(504); //Gateway Timeout
			echo "database exception";
			exit();	
		}	
	} else {
		http_response_code(504); //Gateway Timeout
		echo "Database error";
		exit();
	}

} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
	if(array_key_exists('listID',$_GET)){
		$listID = $_GET['listID'];
	} else{
		http_response_code(400);
		echo "missing taskID";
		exit();
	}
	
	if (!$dbconnecterror) {
		try {
			$sql = "DELETE FROM doList WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();	
			http_response_code(204); //no content
			exit();

			
		} catch (PDOException $e) {
		
		http_response_code(504); //Gateway Timeout
		echo "database exception";
		exit();
		}
	}		
	
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$task = json_decode(file_get_contents('php://input'), true);
	//ensure fields are in json
	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"];
	} else {
		//return 4xx error
		http_response_code(400);
		echo 'complete missing';
		exit();
	}
	if (array_key_exists('taskName', $task)) {
		$taskName = $task["taskName"];
	} else {
		//return 4xx error
		http_response_code(400);
		echo "Missing task name";
		exit();
	}

	if (array_key_exists('taskDate', $task)) {
		$taskDate = $task['taskDate'];
	} else {
		//return 4xx error
		http_response_code(400);
		echo "missing task date";
		exit();
	}
	if (!$dbconnecterror) {
		try {
			$sql = "INSERT INTO doList(complete,listItem,finishDate) VALUES (:complete, :taskName, :taskDate)";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":complete", $complete);
			$stmt->bindParam(":taskName", $taskName);
			$stmt->bindParam(":taskDate", $taskDate);
			$response = $stmt->execute();	
			
			$taskID = $dbh->lastInsertId(); //gets ID that's created
			$fullTask = [
			"listID" => $taskID, "complete" => $complete, "listItem" => $taskName, "finishDate" => $taskDate]; //list that contains all task details
			http_response_code(201); //created
			echo json_encode($fullTask);
			exit();

			
		} catch (PDOException $e) {
		
		http_response_code(504); //Gateway Timeout
		echo "database exception";
		exit();
			
		}
	}
	
} else{
	http_response_code(405); //method not allowed
	echo "unsupported http verb";
	exit();
}//PUT
