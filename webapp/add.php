<?php
if ($_SERVER['REQUEST_METHOD'] == "POST") {
	if (array_key_exists('fin', $_POST)) {
		$complete = 1;
	} else {
		$complete = 0;
	}
	if (empty($_POST['finBy'])) {
		$finBy = null;
	} else {
		$finBy = $_POST['finBy'];
	}
	$listItem = $_POST['listItem'];
	
	$url= "http://3.230.57.46/api/task.php";
	
	//create Json string
	$data = array('completed'=>$complete,'taskName'=>$listItem, 'taskDate'=>$finBy);
	$data_json = json_encode($data);
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$response  = curl_exec($ch); //body of response
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	
	//status code 201
	if ($httpcode == 201){
		header("Location: index.php");
		exit();
	} else {
		header("Location: index.php?error=add");
		exit();
	}
}
