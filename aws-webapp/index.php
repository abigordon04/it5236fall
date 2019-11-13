<?php
//make a call to the api
//build url for api
$url= "https://1gqqhmscqj.execute-api.us-east-1.amazonaws.com/default/tasks";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response  = curl_exec($ch); //body of response
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
	

?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="doIT">
		<meta name="author" content="Russell Thackston">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>doIT</title>

		<link rel="stylesheet" href="style/style.css">
		
		<link href="https://fonts.googleapis.com/css?family=Chilanka%7COrbitron&display=swap" rel="stylesheet">		

	</head>


	<body>
		<a href="index.php"><h1 id="siteName">doIT</h1></a>
		<hr>

			<?php if ($httpcode == 200) { ?>
				<?php foreach(json_decode($response, true)["Items"] as $item){ ?>
					<div class="list">
						<form method="POST" action="edit.php" style="display: inline-block">
							<input type="hidden" 	name="listID" value="<?php echo $item["listID"];?>" >
							<input type="checkbox"	name="fin" <?php if($item["completed"]){echo "checked='checked'";} ?> >
							<input type="text" 	name="listItem" size="50" value="<?php echo $item["taskName"];?>" maxlength="100" >
							<span>by:</span>
							<input type="date" 	name="finBy" value="<?php if($item['taskDate']){echo $item['taskDate'];} ?>" >
							<input type="submit" 	name="submitEdit" value="&check;" >
						</form>
						<form method="POST" action="delete.php" style="display: inline-block">
							<input type="hidden" name="listID" value="<?php echo $item["listID"];?>" >
							<input type="submit" name="submitDelete" value="&times;" >
						</form>
					</div>
					<?php } ?>
			<?php } ?>

			<div class="list">
				<form  method="POST" action="add.php">
					<input type="checkbox" name="fin" value="done">
					<input type="text" name="listItem" size="50">
					<span>by:</span>
					<input type="date" id="finDate" name="finBy">
					<input type="submit" value="&#43;">
				</form>
			</div>
			

			<?php if (array_key_exists('error', $_GET)) { ?>
				<?php if ($_GET['error'] == 'add') { ?>
				<div class="error">
					Uh oh! There was an error adding your to do item. Please try again later.
				</div>
				<?php } ?>
				<?php if ($_GET['error'] == 'delete') { ?>
				<div class="error">
					Uh oh! There was an error deleting your to do item. Please try again later.
				</div>
				<?php } ?>
				<?php if ($_GET['error'] == 'edit') { ?>
				<div class="error">
					Uh oh! There was an error updating your to do item. Please try again later.
				</div>
				<?php } ?>
			<?php } ?>

			<footer>
				<span class="hidden" id="greeting">Welcome!</span> You first visited this site from this computer on <span id= "time"></span>
				<button type = "button"> DoNotTrack</button>
			</footer>
			
		<script>
		//Get connections to the DOM
			var footer = document.querySelector("footer");
			var greeting = document.querySelector("footer #greeting");
			var timeVisited = document.querySelector("footer #time");
			var noTrack = document.querySelector("footer button");
			//local storage key names
			var STORAGE_KEY_TIME = "TimeVisited";
			var STORAGE_KEY_DONT = "DoNotTrack";
			//check to see if user wants to be tracked
			//access local storage
			if (!localStorage.getItem(STORAGE_KEY_DONT)) {
					
				//VISITOR has not come to site before
				if (!localStorage.getItem(STORAGE_KEY_TIME)){
					//get current date
					var presentDate = new Date();
					var dateFormat = presentDate.toDateString() + " " + presentDate.toLocaleTimeString("en-us");
					//add the date to local storage
					localStorage.setItem(STORAGE_KEY_TIME,dateFormat);
					greeting.classList.remove("hidden");
				}
				
				//display stored visit time
				var storedDate = localStorage.getItem(STORAGE_KEY_TIME);
				timeVisited.innerHTML = storedDate;
			}else{
				footer.classList.add("hidden");
			}
			
			//let visitor not be tracked
			noTrack.addEventListener("click", function(){
				localStorage.removeItem(STORAGE_KEY_TIME);
				localStorage.setItem(STORAGE_KEY_DONT, "TRUE");
			});
				
				
		</script>
	</body>
</html>
