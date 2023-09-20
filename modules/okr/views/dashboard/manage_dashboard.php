<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
<div class="content">
<div class="panel_s">
<div class="panel-body">
		<div class="row">
		  	<div class="column">
				<div class="card-dashboard">
			    <div class="col-md-12 dashboard__1">
					<h4><?php echo _l('progress_this_week'); ?></h4>
					<div class="col-md-7" class="border_gray">
						<div class="circle circle--mojito">
						  <div class="circle__content">
						  	<?php echo _l('okr_progressing_well'); ?>
						  </div>
						</div>
						<div class="circle circle--sunrise">
						  <div class="circle__content">
						  	<?php echo _l('okr_developing'); ?>
						  </div>
						</div>
						<div class="circle circle--timber">
						  <div class="circle__content">
						  	<?php echo _l('okr_risk'); ?>
						  </div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="circle mcus-3">
							+<?php echo html_entity_decode($progress_good);?>
						</div>
						<div class="circle mcus-3">
							+<?php echo html_entity_decode($progress_develope);?>
						</div>
						<div class="circle mcus-3">
							+<?php echo html_entity_decode($progress_risk);?>
						</div>
					</div>
			    </div>
			  </div>
			</div>
		  	<div class="column" class="border_gray">
		  		<figure class="highcharts-figure">
				    <div id="container_ck"></div>
				</figure>
			</div>
  		</div>
</div>
</div>

<div class="panel_s">
<div class="panel-body">
	<div class="col-md-6">
	  	<h3 class="title-card">
	  		<i class="fa fa-group"></i> <?php echo _l('okrs_company'); ?>
	  	</h3>
		<article class="card_">
			  <div class="card__right">
			  	  <div class="card__sale-flag"><?php echo html_entity_decode($okrs_company['okrs_count']); ?></div>
			      <div class="card__sale-flag"><?php echo html_entity_decode($okrs_company['okrs_progress']); ?></div>
			      <div class="card__sale-flag"><?php echo html_entity_decode($okrs_company['html']); ?></div>
			      <div class="card__sale-flag"><?php echo html_entity_decode($okrs_company['okrs_keyres']); ?></div>
			  </div>
			  <div class="card__left"> 
			    <div class="card__image">
			      <div class="card__sale-flag"><?php echo _l('objective'); ?> : </div>
			      <div class="card__sale-flag"><?php echo _l('progress'); ?> : </div>
			      <div class="card__sale-flag"><?php echo _l('confidence_level'); ?> : </div>
			      <div class="card__sale-flag"><?php echo _l('key_results'); ?> : </div>
			    </div>
			  </div>
			</article>
	</div>
	<div class="col-md-6">
	     <h3 class="title-card_user">
		  	<i class="fa fa-user-circle"></i> <?php echo _l('my_okrs'); ?>
		  </h3>
		<article class="card_">
		  <div class="card__right">
			  <div class="card__sale-flag"><?php echo html_entity_decode($okrs_user['okrs_count']); ?></div>
		      <div class="card__sale-flag"><?php echo html_entity_decode($okrs_user['okrs_progress']); ?></div>
		      <div class="card__sale-flag"><?php echo html_entity_decode($okrs_user['html']); ?></div>
		      <div class="card__sale-flag"><?php echo html_entity_decode($okrs_user['okrs_keyres']); ?></div>
		  </div>
		  <div class="card__left"> 
		    <div class="card__image">
		      <div class="card__sale-flag"><?php echo _l('objective'); ?> : </div>
		      <div class="card__sale-flag"><?php echo _l('progress'); ?> : </div>
		      <div class="card__sale-flag"><?php echo _l('confidence_level'); ?> : </div>
		      <div class="card__sale-flag"><?php echo _l('key_results'); ?> : </div>
		    </div>
		  </div>
		</article>
	</div>
</div>
</div>
</div>
</div>


<?php init_tail(); ?>
<?php require 'modules/okr/assets/js/dashboard_js.php';?>
</body>
</html>
