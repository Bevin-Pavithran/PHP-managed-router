<?php
include('../include/standard.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);

$updateField = strtolower($_POST['update']);
$routerId = $_POST['id'];
$newValue = $_POST['value'];
$serial = $_POST['serial'];

$result = $api->update($routerId, $serial, $updateField, $newValue);

echo $result;




