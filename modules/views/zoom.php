<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
        
        
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
               <table id="example" class="table table-bordered table-striped" style="width:100%">
                <thead>
                    <tr>
                        <th>Meeting</th>
                        <th>Start time</th>
                        <th>Duration (min)</th>
                        <th>Timezone</th>
                        <th>Agenda</th>
                        <th>Join Url</th>
                    </tr>
                </thead>
               
                <tbody>
                    <?php
                    
                    foreach($data as $dt) {?>
                        <tr>
                        <td><?php echo $dt['topic'];?></td>
                        <td><?php echo date("d-M-Y H:i:s",strtotime($dt['start_time']));?></td>
                        <td><?php echo $dt['duration'];?> </td>
                        <td> <?php echo $dt['timezone'];?></td>
                        <td><?php echo $dt['agenda'];?> </td>
                        <td><?php echo $dt['join_url'];?> </td>
                        </tr>
                    <?php } ?>    
                </tbody>
               
              </table>
               </div>
            </div>
         </div>
        <?php echo form_close(); ?>
      </div>
      <div class="btn-bottom-pusher"></div>
   </div>
</div>

<?php init_tail(); ?>

</body>
</html>

