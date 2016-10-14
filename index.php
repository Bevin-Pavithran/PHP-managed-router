<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1); 
include('include/standard.php');
include('include/header.php');
$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);
$routers = $api->search();
?>
 <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12">
        <form class="form-inline router-search">
          <div class="form-group" style="width: 50%;">
            <label class="sr-only" for="search-str">Search for Router</label>
            <input type="text" class="form-control" id="search-str" placeholder="Name, MAC Address or Serial Number" style="width: 100%;">
          </div>
          <button class="btn btn-default" id="searchRouter">Search</button>
        </form>
      </div>
      <div class="col-md-12">
        <table class="table">
          <thead>
            <tr><th>Customer</th><th>Serial Number</th><th>MAC</th><th>Make</th><th>Model</th><th>Manage</th></tr>
          </thead>
          <tbody>
            <?php
            foreach ($routers as $router) {
            $line = '<tr style="height: 55px;">';
            $line .= "<td>".$router->name."</td>";
            $line .= "<td>".$router->serial."</td>";
            $line .= "<td>".$router->mac."</td>";
            $line .= "<td>".$router->make."</td>";
            $line .= "<td>$router->model</td>";
            $line .= "<td><a class='btn btn-default btn-sm addtomodal' href='".$router->url."' target='_blank'>View Router</a></td>";
            $line .= "</tr>";
            echo $line;
            } ?>
          </tbody>
        </table>    
      </div>
    </div>    
  </div>
<script>
$(document).ready(function () {
    $(".nav li").removeClass("active");
    $('#view').addClass('active');
    $('#searchRouter').on('click', function(e){
      e.preventDefault();
      var searchStr = $('#search-str').val();
      if(searchStr.length !== 0){
        $.ajax({
          method: "POST",
          url: "ajax/searchForRouter.php",
          data: {
            param : searchStr
          },
          success: function() {
                
          }
        });
      } else {
        return false;
      }
    });
});
</script>
<?php include('include/footer.php');?>