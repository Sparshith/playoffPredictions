<?php

include __DIR__ . '/../includes/application_top.php';

$username = isset($_POST['username']) &&  $_POST['username'] ? $_POST['username'] : NULL;
$password = isset($_POST['password']) &&  $_POST['password'] ? $_POST['password'] : NULL;

$headers = array('Accept' => 'application/json', 'Content-Type' => 'application/json');
$data = array('username' => $username, 'password' => $password);

$response = Requests::post('http://api.sparshith.com/nbapredictions/loginUser', array(), $data);
// var_dump($response->body);
echo $response->body;