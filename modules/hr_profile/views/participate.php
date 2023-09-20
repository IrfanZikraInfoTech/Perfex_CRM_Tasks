<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-8 col-md-offset-2 survey">
	<div class="row">
		<?php echo form_open($this->uri->uri_string(),array('id'=>'survey_form')); ?>
		<div class="col-md-12">
			<div id="company-logo">
				<?php get_company_logo(); ?>
			</div>
			<h2 class="bold"><?php echo html_entity_decode($training->subject); ?></h2>
			<hr />
			<p><?php echo html_entity_decode($training->viewdescription); ?></p>
			<?php if(count($training->questions) > 0){
				$question_area = '<ul class="list-unstyled mtop25">';
				foreach($training->questions as $question){
					$question_area .= '<li>';
					$question_area .= '<div class="form-group">';
					$question_area .= '<label class="control-label" for="'.$question['questionid'].'">'.$question['question'].'</label>';
					if($question['boxtype'] == 'textarea'){
						$question_area .= '<textarea class="form-control" rows="6" name="question['.$question['questionid'].'][]" data-for="'.$question['questionid'].'" id="'.$question['questionid'].'" data-required="'.$question['required'].'"></textarea>';
					} else if($question['boxtype'] == 'checkbox' || $question['boxtype'] == 'radio'){
						$question_area .= '<div class="row box chk" data-boxid="'.$question['boxid'].'">';
						foreach($question['box_descriptions'] as $box_description){
							$question_area .= '<div class="col-md-12">';
							$question_area .= '<div class="'.$question['boxtype'].' '.$question['boxtype'].'-default">';
							$question_area .=
							'<input type="'.$question['boxtype'].'" data-for="'.$question['questionid'].'"
							name="selectable['.$question['boxid'].']['.$question['questionid'].'][]" value="'.$box_description['questionboxdescriptionid'].'" data-required="'.$question['required'].'" id="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'"/>';
							$question_area .= '
							<label for="chk_'.$question['boxtype'].'_'.$box_description['questionboxdescriptionid'].'">
							'.$box_description['description'].'
							</label>';
							$question_area .= '</div>';
							$question_area .= '</div>';
						}
						 // end box row
						$question_area .= '</div>';
					} else {
						$question_area .= '<input type="text" data-for="'.$question['questionid'].'" class="form-control" name="question['.$question['questionid'].'][]" id="'.$question['questionid'].'" data-required="'.$question['required'].'">';
					}
					$question_area .= '</div>';
					$question_area .= '<hr /></li>';
				}
				$question_area .= '</ul>';
				echo html_entity_decode($question_area); ?>
				<div class="row">
					<div class="col-md-12 text-center">
						<button type="submit" class="btn btn-success" id="submit"><?php echo _l('hr_survey_submit'); ?></button>
					</div>
				</div>
			<?php } else { ?>
				<p class="no-margin text-center bold mtop20"><?php echo _l('hr_survey_no_questions'); ?></p>
			<?php } ?>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
</div>

<?php 
require('modules/hr_profile/assets/js/training/participate_js.php');
?>