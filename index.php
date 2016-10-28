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
      <div class="col-xs-10">
        <form class="form-inline router-search">
          <div class="form-group" style="width: 50%;">
            <label class="sr-only" for="search-str">Search for Router</label>
            <input type="text" class="form-control" id="search-str" placeholder="Name, MAC Address or Serial Number" style="width: 100%;">
          </div>
          <button class="btn btn-default" id="searchRouter">Search</button>
        </form>
      </div>
      <div class="col-xs-2">
        <div class="actions pull-right">
          <a id="refreshRouter" href="#"><span class="glyphicon glyphicon-refresh"></span> Refresh</a>
        </div>    
      </div>
      <div class="col-md-12" id="routerList">
        <table class="table">
          <thead>
            <tr><th>Customer</th><th>Serial Number</th><th>MAC</th><th>Make</th><th>Model</th><th>Manage</th></tr>
          </thead>
          <tbody>
            <?php
            foreach ($routers as $router) {
            $line = '<tr style="height: 55px;">';
            $line .= '<td id="name"onclick="changeToInput('.$router->id.',\''.$router->name.'\',\'Name\',\''.$router->serial.'\');" style="cursor: pointer;">'.$router->name.'</td>';
            $line .= '<td>'.$router->serial.'</td>';
            $line .= '<td onclick="changeToInput('.$router->id.',\''.$router->mac.'\',\'MAC\',\''.$router->serial.'\'); "style="cursor: pointer;">'.$router->mac.'</td>';
            $line .= '<td>'.$router->make.'</td>';
            $line .= '<td>'.$router->model.'</td>';
            $line .= '<td>';
            $line .= "<a class='btn btn-default btn-sm addtomodal' href='".$router->url."' target='_blank'>View Router</a>";
            $line .= '<button class="removeRouter btn btn-default btn-xs pull-right" onclick="deleteRouter('.$router->id.',\''.$router->serial.'\')"><i class="fa fa-times"></i></button>';
            $line .= '</td>';
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
      </div>
    </div>    
  </div>
  <!-- Modal for Edit-->
  <!-- Modal -->
  <div class="modal fade" id="EditModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
          <button type="button" class="close" 
                  data-dismiss="modal">
            <span aria-hidden="true">&times;</span>
            <span class="sr-only">Close</span>
          </button>
          <h4 class="modal-title" id="modalTitle">
          </h4>
        </div>

        <!-- Modal Body -->
        <div class="modal-body">
          <form role="form">
            <div class="form-group" id='editField'>
            </div>
          </form>
        </div>

        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" data-size="s" data-style="zoom-in" id='saveChanges'>Save changes</button>
        </div>
      </div>
    </div>
  </div> 
  <!-- End of Modal -->
  <!-- Delete Confirm Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <!-- Modal Body -->
        <div class="modal-body">
          Delete Router. Are you sure?
          <div id="deleteModalData"></div>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer">
          <button type="button" class="ladda-button" data-size="s" data-dismiss="modal"><span class="ladda-label">Cancel</span></button>
          <button type="button" class="ladda-button" id='confirmDelete' data-color="blue" data-size="s" data-style="zoom-in"><span class="ladda-label">Delete</span><span class="ladda-spinner"></span></button>
        </div>
      </div>
    </div>
  </div> <!-- End of Delete Modal -->
<script>
$(document).ready(function () {
    $(".nav li").removeClass("active");
    $('#view').addClass('active');
    $('#searchRouter').on('click', function(e){
      e.preventDefault();
      var searchStr = $('#search-str').val();
      getRouter(searchStr);
    });
    $('#refreshRouter').on('click', function(e){
      $("#search-str").val('');
      e.preventDefault();
      getRouter();
    });
    
    //Add Router
    $('#routerList').on('click','#addRouter', function(e){
      var name = $('#name').val();
      var serial = $('#serial').val();
      var mac = $('#mac').val();
      $.ajax({
        method: "POST",
        url: "ajax/addNewRouter.php",
        data: {
          name : name,
          serial : serial,
          mac : mac
        },
        success: function(resultStr) {
          var result = jQuery.parseJSON(resultStr);
          if(result.error){
            $.bootstrapGrowl(result.reason, {
              type: 'danger',
              align: 'right',
              width: 'auto'
            });   
          } else {
            $.bootstrapGrowl("Router Added", {
              type: 'success',
              align: 'right',
              width: 'auto'
            });
            getRouter();
          }
        }
      });
    });
    
    //Edit Router
    $('#saveChanges').on('click',function() {
      var l = Ladda.create(this);
      l.start();
      var editElement = $('#element').html();
      var routerId = $('#routerId').val();
      var serial = $('#serial').val();
      var newValue = $('#newValue').val().trim();
      var oldValue = $('#oldValue').val();
      if(newValue !== oldValue){
        $.ajax({
          method: "POST",
          url: "ajax/updateField.php",
          data: {
            update: editElement,
            id: routerId,
            value: newValue,
            serial: serial
          },
          success: function(result){
            if(result.error){
              l.stop();
              $.bootstrapGrowl(result.reason, {
                type: 'danger',
                align: 'right',
                width: 'auto'
              });
              $('#EditModal').modal('hide');
            } else {
              l.stop();
              $.bootstrapGrowl("Updated", {
                type: 'success',
                align: 'right',
                width: 'auto'
              });
              getRouter();
              $('#EditModal').modal('hide');
            }
          }
        });
      } else {
        l.stop();
        $('#EditModal').modal('hide');
      } 
    });
    
    //View Router
    $('.addtomodal').loadInModal(function(){
      $('#iframeinmodal').on('load',function () {
        $('#siteloader-content').hide();
      });
    });
    
    //Delete Router
    $('#confirmDelete').on('click', function() {
      var l = Ladda.create(this);
      l.start();
      var routerId = $('#delrouterId').val();
      var serial = $('#delserial').val();
      var restoreBtn = '<a href="#" class="restoreRouter btn btn-warning btn-xs pull-right" onclick="restoreRouter('+routerId+',\''+serial+'\')"><i class="fa fa-undo"></i></a>';
      $.ajax({
        method: "POST",
        url: "ajax/deleteRouter.php",
        data: {
          id: routerId,
          serial: serial,
          action: 'delete'
        }, 
        success: function(result) {
          l.stop();
          if(result.error) {
            $('#deleteModal').modal('hide');
            $.bootstrapGrowl(result.reason, {
              type: 'danger',
              align: 'right',
              width: 'auto'
            });
          } else {
            $('#deleteModal').modal('hide');
            $.bootstrapGrowl("Router Deleted", {
              type: 'success',
              align: 'right',
              width: 'auto'
            }); 
            $('table tr').filter(":contains("+serial+")").find('td').css('text-decoration','line-through');
            $('table tr').filter(":contains("+serial+")").find('td').eq(5).html(restoreBtn);
          }  
        }
      });
    });
});

function deleteRouter(routerId, serial) {
  $('#deleteModalData').html('<input type="hidden" class="form-control" id="delrouterId" value="'+routerId+'"/>');
  $('#deleteModalData').append('<input type="hidden" class="form-control" id="delserial" value="'+serial+'"/>');
  $('#deleteModal').modal('show');
}

function restoreRouter(routerId, serial) {
  $.ajax({
    method: "POST",
    url: "ajax/deleteRouter.php",
    data: {
      id: routerId,
      serial: serial,
      action: 'restore'
    }, 
    success: function(result) {
      if(result.error) {
        $.bootstrapGrowl(result.reason, {
          type: 'danger',
          align: 'right',
          width: 'auto'
        });
      } else {
        $.bootstrapGrowl("Router Restored", {
          type: 'success',
          align: 'right',
          width: 'auto'
        });
        getRouter();
      }
    }
  });    
}
    
function getRouter(param){
  $.ajax({
    method: "POST",
    url: "ajax/searchForRouter.php",
    data: {
      param : param
    },
    success: function(searchResult) {
      $('#routerList').html('');     
      $('#routerList').html(searchResult);     
    }
  });
}

function changeToInput(id, value, element, serial){
  $('#modalTitle').html('Edit '+element);
  $('#editField').html('<label for="editedItem" id="element">'+element+'</label>');
  $('#editField').append('<input type="text" class="form-control" id="newValue" value="'+value+'"/>');
  $('#editField').append('<input type="hidden" class="form-control" id="routerId" value="'+id+'"/>');
  $('#editField').append('<input type="hidden" class="form-control" id="oldValue" value="'+value+'"/>');
  $('#editField').append('<input type="hidden" class="form-control" id="serial" value="'+serial+'"/>');
  $('#EditModal').modal('show');
}
</script>
<?php include('include/footer.php');?>