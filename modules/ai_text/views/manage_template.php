<?php
init_head(); 
?>


<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                    <h2 id="manage-templates-heading">Manage Templates</h2>
            </div>
            <div class="col-md-6">
                <div class="add-template-button">
                    <a href="<?php echo admin_url('ai_text/ai_form');?>">Add Template</a>
                </div>
            </div>
        </div> 

        <table class="table table-hovered table-bordered text-center">
            <tr>
                <th>S.No</th>
                <th>TEMPLATE CATEGORY</th>
                <th>TEMPLATE NAME</th>
                <th>TEMPLATE DESCRIPTION</th>
                <th>ACTIONS</th>
            </tr>

            <?php  $serialNum = 1;
            foreach($saved_data as $d): ?>
            <tr id="data<?php echo $d->id; ?>">
                <td><?php echo $serialNum++; ?></td>
                <td><b><?php echo $d->template_category?></b></td>
                <td><b><?php echo $d->template_name?></b></td>
                <td class="text-left"><?php echo $d->template_description?></td>
                <td>
                    <div class="button-group">
                        <div class="btn btn-warning tw-mr-1">
                        <a href="<?php echo admin_url('ai_text/edit/'.$d->id);?>">Edit</a>
                    </div>
                    <button onclick="deletetemplate(<?php echo $d->id;?>)" class="btn btn-danger dlt">Delete</button>
                    </div>
                </td> 
            </tr>
            <?php endforeach; ?>
        </table>

    </div>  
</div>    


<?php
init_tail();
?>

<script>
function deletetemplate(id) {
    $.ajax({
      url: '<?php echo admin_url('ai_text/delete_template')?>',
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
a
{
    color:white;
}
a:hover{
    color:white;
}
th{
    font-weight: 600;
    background-color:#f2e9e4;
    text-align:center
}
#manage-templates-heading {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 20px;
}
.add-template-button {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-bottom: 20px;
}

.add-template-button a {
    display: inline-block;
    padding: 10px 20px;
    background-color: #4285f4;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.add-template-button a:hover {
    background-color: #3367d6;
}
.button-group {
  display: flex;
}

.button-group button {
  margin-right: 10px; /* Adjust the margin as needed */
}
.btn2{
border-radius:20px;
}
</style>

</body>
</html>