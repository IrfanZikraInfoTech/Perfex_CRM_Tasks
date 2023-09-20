<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div>
	<div class="_buttons">
		<?php if(is_admin() || has_permission('hrm_setting','','create')){ ?>
			
			<a href="<?php echo admin_url('hr_profile/contract_template'); ?>" class="btn btn-info pull-left display-block mright5"><?php echo _l('new_contract_template'); ?></a>

		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<br>
	<table class="table dt-table">
		<thead>
			<th width="30%"><?php echo _l('hr_contract_name'); ?></th>
			<th><?php echo _l('hr_hr_job_position'); ?></th>
			<th><?php echo _l('options'); ?></th>
		</thead>
		<tbody>
			<?php foreach($contract_templates as $c){ ?>

				<?php 
				$jobpositionOutput = '';
				$job_positions       = explode(",", $c['job_position']);


				$list_jobposition = '';
				$exportjob_positions = '';
				if($job_positions != null){
					foreach ($job_positions as $key => $position_id) {
						$position_name   = hr_profile_get_job_position_name($position_id);

						$list_jobposition .= '<li class="text-success mbot10 mtop"><a href="#"  style="text-align: left;">'.$position_name. '</a></li>';

					}
				}

				if($job_positions != null){
					$jobpositionOutput .= '<span class="avatar bg-secondary brround avatar-none">+'. (count($job_positions) ) .'</span>';
				}


				$jobpositionOutput1 = '<div class="task-info task-watched task-info-watched">
				<h5>
				<div class="btn-group">
				<span class="task-single-menu task-menu-watched">
				<div class="avatar-list avatar-list-stacked" data-toggle="dropdown">'.$jobpositionOutput.'</div>
				<ul class="dropdown-menu list-staff" role="menu">
				<li class="dropdown-plus-title">
				'. _l('job_position') .'
				</li>
				'.$list_jobposition.'
				</ul>
				</span>
				</div>
				</h5>
				</div>';

				$data_position_names = $jobpositionOutput1;

				?>

				<tr>
					
					<td><?php echo html_entity_decode($c['name']); ?></td>
					<td><?php echo html_entity_decode($data_position_names); ?></td>
					<td>
						<?php if(is_admin() || has_permission('hrm_setting','','edit')){ ?>
							<a href="<?php echo admin_url('hr_profile/contract_template/'.$c['id']); ?>"  class="btn btn-default btn-icon"><i class="fa fa-pencil-square-o"></i></a>
						<?php } ?>

						<?php if(is_admin() || has_permission('hrm_setting','','delete')){ ?>
							<a href="<?php echo admin_url('hr_profile/delete_contract_template_/'.$c['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
						<?php } ?>

					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>       
	
</div>
</body>
</html>
