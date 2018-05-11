<?php

include __DIR__ . '/../includes/application_top.php';

$username = isset($_POST['username']) &&  $_POST['username'] ? $_POST['username'] : NULL;
$headers = array('Accept' => 'application/json', 'Content-Type' => 'application/json');
$response = Requests::get('http://api.sparshith.com/nbapredictions/checkUsername?username='.$username, $headers);

echo $response->body;