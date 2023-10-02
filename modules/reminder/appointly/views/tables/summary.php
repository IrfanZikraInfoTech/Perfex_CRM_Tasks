<?php defined('BASEPATH') or exit('No direct script access allowed');

$data = get_appointments_summary();
$total_appointments_in_database = $data['total_appointments'];
unset($data['total_appointments']);

?>
<h4 class="mbot20"><?= _l('appointments_summary'); ?></h4>
<div class="row appointment_summary_parent">
    <div class="col-md-2 col-xs-6 border-right">
        <h3 class="bold no-mtop"><?= $total_appointments_in_database ?></h3>
        <p class="font-medium-xs no-mbot">
            <a href="" onclick="_toggleSummaryFilter('all'); return false;"><?= _l('appointments_total_found'); ?></a>
        </p>
    </div>
    <?php foreach ($data as $key => $summary) { ?>
        <div class="col-md-2 col-xs-6 border-right appoinment_summary">
            <h3 class="bold no-mtop"><?= $data[$key]['total']; ?></h3>
            <a style="color:<?= ($summary['color']) ? $summary['color'] : ''; ?>" href="" onclick="_toggleSummaryFilter('<?= $key ?>'); return false;"><?= (!empty($summary['name']) ? $summary['name'] : ''); ?></a>
        </div>
    <?php } ?>
</div>
<script>
    const _toggleSummaryFilter = (filter) => {
        const filtersDropdown = $('._filter_data ul.dropdown-menu li');
        $('._filters._hidden_inputs input').val('');
        filtersDropdown.removeClass('active');
        filtersDropdown.find(`a[data-cview='${filter}']`).trigger('click');
    }
</script>
<hr class="hr-panel-heading" />
