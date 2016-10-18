<?php
include('../include/standard.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);

$serial = mysql_real_escape_string($_POST['serial']);
$mac = mysql_real_escape_string($_POST['mac']);
$name = mysql_real_escape_string($_POST['name']);

$result = $api->add($serial, $mac, $name);

echo json_encode($result);

?>


