<?php
$dbh = NULL;

require_once 'credentials.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {	
	try{
		
		$conn_string = "mysql:host=".$dbserver.";dbname=".$db;
		
		$dbh= new PDO($conn_string, $dbusername, $dbpassword);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(Exception $e){
		http_response_code(500);
		echo "cant connect to db";
		exit();
	}

	try {
		$sql = "SELECT * FROM doList";
		$stmt = $dbh->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
		echo json_encode($result);
		exit();
		
	} catch (PDOException $e) {
		http_response_code(500);
		echo "cant connect to db";
		exit();
	}
} else {
	http_response_code(405);
	echo "method not allowed";
	exit();
}

