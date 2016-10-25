<?php
include('../include/standard.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);
$routers = $api->delete($_POST['id'],$_POST['serial']);
//$routers = $api->undelete(1267770,'RNV5000511');

echo json_encode($routers);
?>
