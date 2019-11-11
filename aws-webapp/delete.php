<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	$listID = $_POST['listID'];
	
	//make a call to the api
	//build url for api
	$url = "http://3.230.57.46/api/task.php?listID=$listID";
		
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch); //body of response
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	// go back to the home page, showing any error message if we need to
	if ($httpcode === 204) {	
		header("Location: index.php");
	} else {
		header("Location: index.php?error=delete");
	}
}
