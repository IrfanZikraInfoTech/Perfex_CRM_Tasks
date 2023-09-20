<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<table class="table">
	<?php 
	$job_position_name ='';
	if($candidate['rec_campaign'] != '' && isset($job_positions[$candidate['rec_campaign']])){
		$job_position_name .= $job_positions[$candidate['rec_campaign']];
	}

	?>
	<tbody>
		<tr>
			<td width="27%"  class="text_align_center candidate_name_widt_27">
				<h2 class="text_align_center  candidate_name_color"><?php echo html_entity_decode($candidate['candidate_name'].' '.$candidate['last_name']) ?></h2><br>
				<span class="position_color"><?php echo html_entity_decode($job_position_name) ?></span><br>
				<span class="candidate_code_color"><?php echo html_entity_decode($candidate['candidate_code']) ?></span>
			</td>
			<td width="73%" class="align_right logo_with" ><?php echo html_entity_decode(re_pdf_logo_url()) ?></td>
		</tr>
	</tbody>
</table>

<table class="table">
	<tbody>
		<tr>
			<td width="30%"  class="align_left avatar_width_30">
				<?php 

				if(isset($cadidate_avatar) && $cadidate_avatar != ''){
					$url = str_replace('(', '%28', base_url(RECRUITMENT_PATH . 'candidate/avartar/' . $cadidate_avatar['rel_id'] . '/' . $cadidate_avatar['file_name']));
					$url = str_replace(')', '%29', $url);
					$url = str_replace(' ', '%20', $url);

					if (file_exists(CANDIDATE_IMAGE_UPLOAD . $cadidate_avatar['rel_id'] . '/' . $cadidate_avatar['file_name'])) { ?>

						<img class="avartar_w_h" src="<?php echo html_entity_decode($url) ;?>" alt="<?php echo html_entity_decode($cadidate_avatar['file_name']) ?>" >
					<?php }else{ ?> 

						<img class="avartar_w_h" src="<?php echo base_url(RECRUITMENT_PATH . 'none_avatar.jpg') ?>" alt="nul_image.jpg">
					<?php }
				}else{?> 
						<img class="avartar_w_h" src="<?php echo base_url(RECRUITMENT_PATH . 'none_avatar.jpg') ?>" alt="nul_image.jpg">

				<?php }?>
				
			</td>

			<td width="70%" class="align_left text_align_justify qualifications_width_70" >
				<h3 class="card-title  bold summary_title"><?php echo _l('summary_of_qualifications') ?></h3>

				<p class="introduce_yourself"><?php echo html_entity_decode($candidate['introduce_yourself']) ?></p><br>

				<h3 class="card-title  bold summary_title"><?php echo _l('skill_title') ?></h3>

				<div class="card">
					<div class="card-body">

						<?php if(isset($candidate['skill']) && strlen($candidate['skill']) > 0){ 
							$arr_skill = explode(',', $candidate['skill']);
							?>

							<?php foreach ($arr_skill as $value) {
								$skill_name='';
								if(isset($rec_skill[$value])){
									$skill_name .= $rec_skill[$value];
								}

								?>
								<span class="skill_name"><?php echo html_entity_decode($skill_name) ?>, </span>

							<?php } ?>

						<?php } ?>

					</div>
				</div>
			</td>
		</tr>
	</tbody>
</table>


<br>

<h3 class="card-title  bold employment_record_title"><?php echo _l('employment_record') ?></h3>

<?php if( $temp_candidate_experience != '' && count($temp_candidate_experience) > 0){ ?>
	<?php foreach ($temp_candidate_experience as $candidate_experience) { ?>
		<div class="experience_boder">
			<table class="table border_table">
				<tbody>
					<tr>
						<td width="100%" class="align_left td_width_100">
						</td>
					</tr>
					<tr>
						<td width="2%"  class="align_left td_width_2">
						</td>
						<td width="15%"  class="align_left td_width_15">
							<span class="record_title"><?php echo _l('from_date') ?></span><br><br>
							<span class="record_content"><?php echo html_entity_decode(_d($candidate_experience['from_date'])) ?></span>
						</td>
						<td width="15%"  class="align_left td_width_15">
							<span class="record_title"><?php echo _l('to_date') ?></span><br><br>
							<span class="record_content"><?php echo html_entity_decode(_d($candidate_experience['to_date'])) ?></span>
						</td>
						<td width="38%"  class="align_left td_width_38">
							<span class="record_title"><?php echo _l('company') ?></span><br><br>
							<span class="record_content"><?php echo html_entity_decode($candidate_experience['company']) ?></span>
						</td>
						<td width="27%"  class="align_left td_width_27">
							<span class="record_title"><?php echo _l('position') ?></span><br><br>
							<span class="record_content"><?php echo html_entity_decode($candidate_experience['position']) ?></span>
						</td>
						<td width="2%"  class="align_left td_width_2">
						</td>

					</tr>
					<br>
					<tr>
						<td width="2%"  class="align_left td_width_2">
						</td>
						<td width="96%"  class="text_align_justify td_width_96">
							<span class="record_title"><?php echo _l('job_description') ?>: </span>
							<p class="record_content"><?php echo html_entity_decode($candidate_experience['job_description']) ?></p>
						</td>
						<td width="2%"  class="align_left td_width_2">
						</td>
					</tr>
					<tr>
						<td width="100%"  class="align_left td_width_100">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>
<?php } ?>

<h3 class="card-title  bold employment_literacy_title"><?php echo _l('employment_literacy') ?></h3>

<?php if($temp_candidate_literacy != '' && count($temp_candidate_literacy) > 0){ ?>
	<?php foreach ($temp_candidate_literacy as $literacy) { ?>
		<div class="experience_boder">
			<table class="table border_table">
				<tbody>
					<tr>
						<td width="100%" class="align_left td_width_100">
						</td>
					</tr>
					<tr>
						<td width="2%"  class="align_left td_width_2">
						</td>
						<td width="15%"  class="align_left td_width_15">
							<span class="literacy_title"><?php echo _l('from_date') ?></span><br><br>
							<span class="literacy_content"><?php echo html_entity_decode(_d($literacy['literacy_from_date'])) ?></span>
						</td>
						<td width="15%"  class="align_left td_width_15">
							<span class="literacy_title"><?php echo _l('to_date') ?></span><br><br>
							<span class="literacy_content"><?php echo html_entity_decode(_d($literacy['literacy_to_date'])) ?></span>
						</td>
						<td width="38%"  class="align_left td_width_15">
							<span class="literacy_title"><?php echo  html_entity_decode($literacy['diploma']) ?></span><br><br>
							<span class="literacy_content"><?php echo html_entity_decode($literacy['training_places']) ?></span>
						</td>
						<td width="27%"  class="align_left td_width_27">
							<span class="literacy_title"><?php echo _l('re_degree') ?></span><br><br>
							<span class="literacy_content"><?php echo html_entity_decode($literacy['specialized']) ?></span>
						</td>
						<td width="2%"  class="align_left td_width_2">
						</td>
					</tr>

					<tr>
						<td width="100%"  class="align_left td_width_100">
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php } ?>
<?php } ?>


