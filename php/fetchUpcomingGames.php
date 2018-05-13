<?php

include __DIR__ . '/../includes/application_top.php';

$headers = array('Accept' => 'application/json', 'Content-Type' => 'application/json');
$response = Requests::get('http://api.sparshith.com/nbapredictions/upcomingGames', $headers);

echo $response->body;