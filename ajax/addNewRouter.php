<?php
include('../include/standard.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);

$serial = $_POST['serial'];
$mac = $_POST['mac'];
$name = $_POST['name'];

$result = $api->add($serial, $mac, $name);

echo json_encode($result);

?>


