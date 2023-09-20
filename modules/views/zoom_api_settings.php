<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-5 left-column">
            <div class="panel_s">
               <div class="panel-body">
               <?php echo form_open('zoom/zoom/api_meeting_submit',array('id'=>'meeting-submit-form')); ?>
                 
                
               <div class="form-group  projects-wrapper">
                
            <div id="project_ajax_search_wrapper">
                  
               </div>
            </div>

            
            
            <?php 
            $email=$settings[0]['zoom_email'];
            echo render_input('zoom_email','zoom_email',$email,'text',array('required'=>'true')); ?>
            

         <div class="row">

               <div class="col-md-12">
               <?php 
                $api_key=$settings[0]['api_key'];
               echo render_input('api_key','zoom_api_key',$api_key,'text',array('required'=>'true')); ?>
               

               </div>
         </div>

         <div class="row">

               <div class="col-md-12">
               <?php 

               $api_secret=$settings[0]['api_secret'];
               echo render_input('api_secret','zoom_api_secret',$api_secret,'text',array('required'=>'true')); ?>
              

               </div>
         </div>
        

         <div class="btn-bottom-toolbar text-right">
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
   </div>
</div>



  
</div>
</div>
</div>
</div>

</div>
</div>
</div>
<?php init_tail(); ?>

</body>
</html>
