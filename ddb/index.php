<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL ^ E_DEPRECATED);

require 'vendor/autoload.php';
require 'credentials.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

use Aws\DynamoDb\DynamoDbClient;

// Create a DynamoDB client
$dynamodb = DynamoDbClient::factory(array(
	'credentials' => array(
		'key' => $access_key,
		'secret' => $secret_key
    	),
	'region' => $region,
	'version' => 'latest'
));

$marshaler = new Marshaler();

$params = [
    'TableName' => 'tasksGordon',
    'ProjectionExpression' => 'listID, taskName, taskDate, completed'
];

$error_message = "";
$all_tasks = array();

try {
    while (true) {
        $result = $dynamodb->scan($params);

	foreach ($result['Items'] as $i) {
            $tasks = $marshaler->unmarshalItem($i);
			array_push($all_tasks, $tasks);
        }

        if (isset($result['LastEvaluatedKey'])) {
            $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
        } else {
            break;
        }
    }

} catch (DynamoDbException $e) {
    $error_message = $e->getMessage();
}

echo "<pre>";
foreach($all_tasks as $task) {
	$completed = false;
	if (isset($task['completed']) && $task['completed'] == 1) {
		$completed = true;
	}

	echo $task['listID'];
	echo PHP_EOL;
	echo $task['taskName'];
	echo PHP_EOL;
	echo $task['taskDate'];
	echo PHP_EOL;
	echo $completed ? 'true' : 'false';
	echo PHP_EOL;
	echo PHP_EOL;
}
echo "</pre>";

/****************************************************
HTML goes here. Use the code above (lines 66-83)
as an example for how to display the task data.
Remove lines 66-83 when the HTML is complete.
****************************************************/
