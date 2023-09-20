<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head();
?>


<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-6">
                    <h2 id="custom-templates-heading">Templates</h2>
            </div>
            <div class="col-md-6">
                <div class="add-template-button">
                    <a href="<?php echo admin_url('ai_text/ai_form');?>">Add Template</a>
                </div>
            </div>
        </div>    
        <div class="row">
            <div class="col-md-12">
                <div class="template-filter">
                    <button class="filter-btn active" data-category="all">All</button>
                    <button class="filter-btn" data-category="blogs">Blog</button>
                    <button class="filter-btn" data-category="ecommerce">E-commerce</button>
                    <button class="filter-btn" data-category="development">Development</button>
                    <button class="filter-btn" data-category="socialmedia">Social Media</button>
                    <button class="filter-btn" data-category="advertisement">Advertisement</button>
                </div>
            </div>
        </div>    
        <div class="row">
            <?php foreach ($saved_data as $d): ?>
                <div class="col-md-6 template-card" data-category="<?php echo $d->template_category; ?>">
                    <a href="<?php echo admin_url('ai_text/generate_text/'.$d->id);?>">
                      <div class="card ">
                        <div class="card-body">
                          <div class="row"> 
                            <div class="col-sm-2">           
                                <div class="template-icon tw-rounded-full tw-w-10 tw-h-10" style="background-color: <?php echo $d->template_color; ?>;">
                                    <i class="<?php echo $d->template_icon;?>"></i>
                                </div>
                            </div>
                            <div class="col-sm-10">      
                                <h5 class="card-title"><?php echo $d->template_name; ?></h5>
                                <p class="card-text"><?php echo $d->template_description; ?></p>
                            </div>  
                          </div>      
                        </div>                        
                      </div> 
                    </a>       
                </div>        
            <?php endforeach; ?>  
        </div>
    </div>
</div>

<?php
init_tail();
?>
<script>
 $(document).ready(function() {
    // Filter templates based on category when clicking on a category button
    $('.filter-btn').click(function() {
        var category = $(this).data('category');
      
      // Add or remove the 'active' class from the category buttons
      $('.filter-btn').removeClass('active');
      $(this).addClass('active');
      
      // Show or hide the templates based on the selected category
      if (category === 'all') {
        $('.template-card').show();
      } else {
        $('.template-card').hide();
        $('.template-card[data-category="' + category + '"]').show();
      }
    });
  });
</script>

<style>
a
{
    color:#475569
}

.card {
    border-radius: 10px;
    box-shadow: 0px 2px 6px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s ease;
}

.card-body {
    padding: 20px;
    margin:10px;
    height:140px;

}

.card:hover {
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}

.card-title {
    font-size: 18px;
    font-weight: bold;
    margin-bottom: 10px;
}

.card-text {
    margin-bottom: 20px;
}

.template-icon {
    font-size: 15px;
    padding: 10px;
    color:black;
}

#custom-templates-heading {
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
.template-filter {
  text-align: center;
  margin-bottom: 20px;
}

.filter-btn {
  display: inline-block;
  padding: 10px 20px;
  margin: 0 5px;
  border: none;
  border-radius: 4px;
  background-color: #f2f2f2;
  color: #333;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.filter-btn.active {
  background-color: #4285f4;
  color: #fff;
}
</style>
