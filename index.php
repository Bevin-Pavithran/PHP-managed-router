<?php 
include('include/standard.php');
include('include/header.php');

$api = new ManagedRouterAPI(API_URL, API_USER, API_PASS);
?>
 <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <h1>View Router</h1> 
        <table class="table">
          <thead> 
            <tr> 
              <th>#</th> <th>First Name</th>
              <th>Last Name</th> 
              <th>Username</th>
            </tr> 
          </thead> 
          <tbody>
            <tr>
              <th scope="row">1</th>
              <td>Mark</td>
              <td>Otto</td>

              <td>@mdo</td>
            </tr> 
          </tbody>
        </table>    
      </div>
    </div>    
  </div>
<script>
$(document).ready(function () {
    $(".nav li").removeClass("active");
    $('#view').addClass('active');
});
</script>
<?php include('footer.php');?>