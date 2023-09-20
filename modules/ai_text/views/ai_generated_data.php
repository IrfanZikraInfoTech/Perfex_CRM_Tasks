<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head(); // This initializes the header
?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                    <h2 id="heading">Generated Data</h2>
            </div>
            <div class="col-md-6">
                <?php if (isset($_GET['message'])): ?>
                    <script>
                        alert('<?php echo $_GET['message']; ?>');
                    </script>
                <?php endif; ?>
            </div>
        </div> 

        <table class="table table-hovered table-bordered tw-text-center">
            <tr class="tw-pt-8">
                <th>S.No</th>
                <th>DATE</th>
                <th>TEMPLATE &nbsp;NAME</th>
                <th>DATA</th>
                <th>ACTION</th>

            </tr>


            <?php  $serialNum = 1;
                foreach($gen_data as $g): ?>
                <tr id="data<?php echo $g->id; ?>">
                    <td><?php echo $serialNum++; ?></td>
                    <td><?php echo $g->date?></td>
                    <td><?php echo $g->template_name?></td>
                    <td data-history="<?php echo $g->history; ?>"><button class="btn btn-primary view-btn" data-toggle="modal" data-target="#myModal">View</button></td>
                    <td><button onclick="deleteData(<?php echo $g->id;?>)" class="btn btn-warning dlt">Delete</button></td>
                </tr>
                <?php endforeach; ?>
        </table>

    </div>  
</div>    

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content -->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modalTitle"></h4>
            </div>
            <div class="modal-body">
                <div id="itemDetails"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
init_tail(); 
?>

<script>

$(document).ready(function() {
  $('.view-btn').click(function() {
    var row = $(this).closest('tr');
    var templateName = row.find('td:eq(2)').text();
    var history = row.find('td:eq(3)').data('history');

    // Remove HTML tags from history
    var sanitizedHistory = $('<div>').html(history).text();

    $('#myModal .modal-title').text(templateName);
    $('#myModal #itemDetails').text(sanitizedHistory);
  });
});

function deleteData(id) {
    $.ajax({
      url: '<?php echo admin_url('ai_text/delete_gen_Data')?>',
      type: 'POST',
      data: {id: id},
      success: function(response) {
        alert(response);
        $("#data" + id).fadeOut("fast", function() {
          $(this).remove(); 
        });
      },
      error: function(xhr) {
        console.error(xhr);
        alert('Error deleting data.');
      }
    });
}

</script>
<style>
    
/* Added styles */
th{
    font-weight:bold;
    background-color:#f2e9e4;
    text-align:center;
    font-size:700;
}
#heading {
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
}
tr{
    text-align:center;
}

.btn2{
border-radius:20px;
}
</style>
