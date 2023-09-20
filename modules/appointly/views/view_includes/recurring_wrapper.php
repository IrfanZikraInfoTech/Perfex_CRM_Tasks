<div class="form-group select-placeholder">
    <label for="repeat_every" class="control-label"><?php echo _l('expense_repeat_every'); ?></label>
    <select
            name="repeat_every"
            id="repeat_every"
            class="selectpicker"
            data-width="100%"
            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
        <option value=""></option>
        <option value="1-week" <?php if (isset($appointment) && $appointment['repeat_every'] == 1 && $appointment['recurring_type'] == 'week') {
            echo 'selected';
        } ?>><?php echo _l('week'); ?></option>
        <option value="2-week" <?php if (isset($appointment) && $appointment['repeat_every'] == 2 && $appointment['recurring_type'] == 'week') {
            echo 'selected';
        } ?>>2 <?php echo _l('weeks'); ?></option>
        <option value="1-month" <?php if (isset($appointment) && $appointment['repeat_every'] == 1 && $appointment['recurring_type'] == 'month') {
            echo 'selected';
        } ?>>1 <?php echo _l('month'); ?></option>
        <option value="2-month" <?php if (isset($appointment) && $appointment['repeat_every'] == 2 && $appointment['recurring_type'] == 'month') {
            echo 'selected';
        } ?>>2 <?php echo _l('months'); ?></option>
        <option value="3-month" <?php if (isset($appointment) && $appointment['repeat_every'] == 3 && $appointment['recurring_type'] == 'month') {
            echo 'selected';
        } ?>>3 <?php echo _l('months'); ?></option>
        <option value="6-month" <?php if (isset($appointment) && $appointment['repeat_every'] == 6 && $appointment['recurring_type'] == 'month') {
            echo 'selected';
        } ?>>6 <?php echo _l('months'); ?></option>
        <option value="1-year" <?php if (isset($appointment) && $appointment['repeat_every'] == 1 && $appointment['recurring_type'] == 'year') {
            echo 'selected';
        } ?>>1 <?php echo _l('year'); ?></option>
        <option value="custom" <?php if (isset($appointment) && $appointment['custom_recurring'] == 1) {
            echo 'selected';
        } ?>><?php echo _l('recurring_custom'); ?></option>
    </select>
</div>
<div class="recurring_custom <?php if ((isset($appointment) && $appointment['custom_recurring'] != 1) || (!isset($appointment))) {
    echo 'hide';
} ?>">
    <div class="row">
        <div class="col-md-6">
            <?php $value = (isset($appointment) && $appointment['custom_recurring'] == 1 ? $appointment['repeat_every'] : 1); ?>
            <?php echo render_input('repeat_every_custom', '', $value, 'number', ['min' => 1]); ?>
        </div>
        <div class="col-md-6">
            <select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <option value="day" <?php if (isset($appointment) && $appointment['custom_recurring'] == 1 && $appointment['recurring_type'] == 'day') {
                    echo 'selected';
                } ?>><?php echo _l('expense_recurring_days'); ?></option>
                <option value="week" <?php if (isset($appointment) && $appointment['custom_recurring'] == 1 && $appointment['recurring_type'] == 'week') {
                    echo 'selected';
                } ?>><?php echo _l('expense_recurring_weeks'); ?></option>
                <option value="month" <?php if (isset($appointment) && $appointment['custom_recurring'] == 1 && $appointment['recurring_type'] == 'month') {
                    echo 'selected';
                } ?>><?php echo _l('expense_recurring_months'); ?></option>
                <option value="year" <?php if (isset($appointment) && $appointment['custom_recurring'] == 1 && $appointment['recurring_type'] == 'year') {
                    echo 'selected';
                } ?>><?php echo _l('expense_recurring_years'); ?></option>
            </select>
        </div>
    </div>
</div>
<div id="cycles_wrapper" class="<?php if (!isset($appointment) || (isset($appointment) && $appointment['recurring'] == 0)) {
    echo ' hide';
} ?>">
    <?php $value = (isset($appointment) ? $appointment['cycles'] : 0); ?>
    <div class="form-group recurring-cycles">
        <label for="cycles"><?php echo _l('recurring_total_cycles'); ?>
            <?php if (isset($appointment) && $appointment['total_cycles'] > 0) {
                echo '<small>' . _l('cycles_passed', $appointment['total_cycles']) . '</small>';
            }
            ?>
        </label>
        <div class="input-group">
            <input type="number" class="form-control"<?php if ($value == 0) {
                echo ' disabled';
            } ?> name="cycles" id="cycles" value="<?php echo $value; ?>" <?php if (isset($appointment) && $appointment['total_cycles'] > 0) {
                echo 'min="' . ($appointment['total_cycles']) . '"';
            } ?>>
            <div class="input-group-addon">
                <div class="checkbox">
                    <input type="checkbox"<?php if ($value == 0) {
                        echo ' checked';
                    } ?> id="unlimited_cycles">
                    <label for="unlimited_cycles"><?php echo _l('cycles_infinity'); ?></label>
                </div>
            </div>
        </div>
    </div>
</div>
