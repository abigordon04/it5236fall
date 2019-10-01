<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
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
		//if($complete==1){
			
		//}
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
		$taskDate = DateTime::createFromFormat('m/d/Y', $task['taskDate'])->format('Y-m-d');
	} else {
		//return 4xx error
		http_response_code(400);
		echo "missing task date";
		exit();
	}

	//add the other two fields here


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

			
		} catch (PDOException $e) {
		
		http_response_code(504); //Gateway Timeout
		echo "database exception";
		var_dump($e);
		exit();
			
		}
	}		
	
} else if ($_SERVER['REQUEST_METHOD'] == "POST") {
	
	$task = json_decode(file_get_contents('php://input'), true);
	//ensure fields are in json
	if (array_key_exists('completed', $task)) {
		$complete = $task["completed"];
		//if($complete==1){
			
		//}
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
		$taskDate = DateTime::createFromFormat('m/d/Y', $task['taskDate'])->format('Y-m-d');
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
	
} else if ($_SERVER['REQUEST_METHOD'] == "GET") {
	if(array_key_exists('listID',$_GET)){
		$listID = $_GET['listID'];
	} else{
		http_response_code(400);
		echo "missing taskID";
		exit();
	}
	
	if (!$dbconnecterror) {
		try {
			$sql = "SELECT * FROM doList WHERE listID=:listID";
			$stmt = $dbh->prepare($sql);			
			$stmt->bindParam(":listID", $listID);
			$response = $stmt->execute();
			$result= $stmt->fetch(PDO::FETCH_ASSOC);
			
			if(!is_array($result)) {
				http_response_code(404);
				exit();
			}
			
			http_response_code(200); 
			echo json_encode($result);
			exit();

			
		} catch (PDOException $e) {
		
		http_response_code(504); //Gateway Timeout
		echo "database exception";
		var_dump($e);
		exit();
			
		}
	}		
	
} else{
	http_response_code(405); //method not allowed
	echo "unsupported http verb";
	exit();
}//PUT
