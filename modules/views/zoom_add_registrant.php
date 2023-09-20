<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-5 left-column">
            <div class="panel_s">
               <div class="panel-body">
               <?php echo form_open('zoom/zoom/submit_registrant',array('id'=>'meeting-submit-form')); ?>
                 
                
               <div class="form-group  projects-wrapper">
                
            <div id="project_ajax_search_wrapper">
                  
               </div>
            </div>

            
            
            <?php 
         
            echo render_input('zoom_registrant_email','zoom_registrant_email','','text',array('required'=>'true')); ?>

         <div class="row">

               <div class="col-md-6">
               <?php 
               
               echo render_input('zoom_registrant_meetid','zoom_registrant_meetid','','text',array('required'=>'true')); ?>
               </div>   
         </div>
         <div class="row">

               <div class="col-md-6">
               <?php 
               
               echo render_input('zoom_registrant_fname','zoom_registrant_fname','','text',array('required'=>'true')); ?>
               

               </div>
               <div class="col-md-6">
               <?php 

              
               echo render_input('zoom_registrant_lname','zoom_registrant_lname','','text',''); ?>
              

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
