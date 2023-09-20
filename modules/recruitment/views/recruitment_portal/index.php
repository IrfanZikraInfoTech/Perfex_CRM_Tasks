<?php defined('BASEPATH') or exit('No direct script access allowed');
echo theme_head_view();
get_template_part_rec_portal($navigationEnabled ? 'navigation' : '');
?>
<div id="wrapper">
   <div id="content">
      <div class="container">
         <div class="row">
            <?php get_template_part_rec_portal('alerts'); ?>
         </div>
      </div>
  
      <div class="container">
         <?php hooks()->do_action('customers_content_container_start'); ?>
         <div class="row">
           
            <?php echo theme_template_view(); ?>
         </div>
      </div>
   </div>
   <?php
   echo theme_footer_view();
   ?>
</div>
<?php
/* Always have app_customers_footer() just before the closing </body>  */
app_customers_footer();
   /**
   * Check for any alerts stored in session
   */
   app_js_alerts();
   ?>
</body>
</html>
