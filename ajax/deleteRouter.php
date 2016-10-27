<?php
include('../include/standard.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);
//$routers = $api->undelete(1267842,'RNV5000520');

if($_POST['action'] == "delete"){
  $routers = $api->delete($_POST['id'],$_POST['serial']);
} else if($_POST['action'] == "restore"){
  $routers = $api->undelete($_POST['id'],$_POST['serial']);
}
echo json_encode($routers);
?>
