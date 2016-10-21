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
      $line .= '<td id="name"onclick="changeToInput('.$router->id.',\''.$router->name.'\',\'Name\','.$router->serial.');">'.$router->name.'</td>';
      $line .= '<td>'.$router->serial.'</td>';
      $line .= '<td onclick="changeToInput('.$router->id.',\''.$router->mac.'\',\'MAC\','.$router->serial.');">'.$router->mac.'</td>';
      $line .= '<td>'.$router->make.'</td>';
      $line .= '<td>'.$router->model.'</td>';
      $line .= "<td><a class='btn btn-default btn-sm addtomodal' href='".$router->url."' target='_blank'>View Router</a></td>";
      $line .= '</tr>';
      echo $line;
    } ?>
  </tbody>
  <tfoot>
    <tr>
      <td>
        <input type="text" class="form-control" id="name" placeholder="Name">
      </td>
      <td>
        <input type="text" class="form-control" id="serial" placeholder="Serial Number RNV...">
      </td>
      <td>
        <input type="text" class="form-control" id="mac" placeholder="MAC 0019F...">
      </td>
      <td></td>
      <td></td>
      <td>
        <button id="addRouter" class="btn btn-default">Add</button>
      </td>
    </tr>    
    </tfoot>
</table>