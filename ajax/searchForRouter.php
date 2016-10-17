<?php
include('../include/standard.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);
$routers = $api->search($_POST['param']);
?>

<table class="table">
  <thead>
    <tr><th>Customer</th><th>Serial Number</th><th>MAC</th><th>Make</th><th>Model</th><th>Manage</th></tr>
  </thead>
  <tbody>
      <?php
      foreach ($routers as $router) {
          $line = '<tr style="height: 55px;">';
          $line .= "<td>" . $router->name . "</td>";
          $line .= "<td>" . $router->serial . "</td>";
          $line .= "<td>" . $router->mac . "</td>";
          $line .= "<td>" . $router->make . "</td>";
          $line .= "<td>$router->model</td>";
          $line .= "<td><a class='btn btn-default btn-sm addtomodal' href='" . $router->url . "' target='_blank'>View Router</a></td>";
          $line .= "</tr>";
          echo $line;
      }
      ?>
  </tbody>
</table>