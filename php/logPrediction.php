<?php

include __DIR__ . '/../includes/application_top.php';

$confidence = isset($_POST['confidence']) &&  $_POST['confidence'] ? $_POST['confidence'] : NULL;
$teamId = isset($_POST['teamId']) &&  $_POST['teamId'] ? $_POST['teamId'] : NULL;
$gameId = isset($_POST['gameId']) &&  $_POST['gameId'] ? $_POST['gameId'] : NULL;
$userHash = isset($_POST['userHash']) &&  $_POST['userHash'] ? $_POST['userHash'] : NULL;

$headers = array('Accept' => 'application/json', 'Content-Type' => 'application/json');
$data = array('confidence' => $confidence, 'teamId' => $teamId, 'gameId' => $gameId, 'userHash' => $userHash);

$response = Requests::post('http://api.sparshith.com/nbapredictions/logPrediction', array(), $data);
echo $response->body;