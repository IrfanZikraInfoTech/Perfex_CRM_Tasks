<?php 
    $this->load->view('header');
?>
<div id="wrapper">
    <!-- Monthly Dropdown -->
    <div class="container">
    <h2 class="text-center my-6 text-2xl"><?php echo "Monthly Section Salary"?></h2>
        <form class="form-inline" method="get" action="">
            <div class="form-group mb-2">
                <label for="month" class="mr-2">Month:</label>
                <select class="form-control mr-2" name="month" id="month">
                    <?php 
                        $selectedMonth = isset($_GET['month']) ? $_GET['month'] : null;
                        for ($m=1; $m<=12; ++$m) { 
                    ?>
                        <option value="<?php echo $m; ?>" <?php echo $m == $selectedMonth ? 'selected="selected"' : ''; ?>>
                            <?php echo date('F', mktime(0, 0, 0, $m, 1)); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group mb-2">
                <label for="year" class="mr-2">Year:</label>
                <select class="form-control mr-2" name="year" id="year">
                    <?php for($y=date('Y'); $y>=2000; $y--){ ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    
    <div class="container mt-4">
        <table class="min-w-full divide-y divide-gray-200 shadow-lg mt-4">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deduction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver Name</th>
                </tr>
            </thead>
            <tbody>
            <?php 
                $selectedMonth = isset($_GET['month']) ? $_GET['month'] : null;
                if(is_array($staffs)){
                foreach ($staffs as $staff) : 
                    $rowMonth = ($staff['month']);
                    // Only display the row if the selected month matches the row's month
                    if ($selectedMonth === null || $selectedMonth == $rowMonth) :
            ?>                    
                <tr>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['firstname']; ?></td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['salary']; ?></td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['bonus']; ?></td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['deduction']; ?></td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500" style="display: flex; align-items: center;">
                        <select name="approval_status" class="form-control approval-status" data-id="<?php echo $staff['id']; ?>" style="width: auto; margin-right: 10px;" disabled>
                            <option value="2"<?php if($staff['status'] == '2') echo 'selected'; ?>>Approved</option>
                            <option value="1"<?php if($staff['status'] == '1') echo 'selected'; ?>>Pending</option>
                            <option value="0"<?php if($staff['status'] == '0') echo 'selected'; ?>>Rejected</option>
                        </select>
                        <button type="button" class="btn btn-primary edit-save-btn" data-id="<?php echo $staff['id']; ?>"><i class="fas fa-edit"></i></button>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?php echo $staff['approver_name']; ?></td>
                </tr>
            <?php 
                    endif;
                endforeach; 
            }
            ?>
            </tbody>
        </table>
    </div>

</div>
<?php
    $this->load->view('footer');
?>

<script>
    $(document).ready(function() {
        $('.edit-save-btn').click(function() {
            var id = $(this).data('id');  // get the ID of the row
            var selectElement = $('.approval-status[data-id="' + id + '"]'); // get the select element of the row
            var btn = $(this);  // capture the reference to the button

            if (btn.hasClass('editing')) {
                // 'Save' was clicked
                var selectedStatus = selectElement.val();  // get the selected status

                // Send AJAX request to server to save the selected status
                $.ajax({
                    url: '<?php echo admin_url('payroll/update_approval_status'); ?>',
                    type: 'POST',
                    data: {
                        'status': selectedStatus,
                        'id': id,
                        'changedby': '<?php echo get_staff_user_id(); ?>'
                    },
                    success: function(response) {
                        // On successful save, disable the select element and change the button back to 'Edit'
                        selectElement.attr('disabled', true);
                        btn.removeClass('editing');
                        btn.html('<i class="fas fa-edit"></i>'); // Replace the Save icon with the Edit icon
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            } else {
                // 'Edit' was clicked
                // Enable the select element and change the button to 'Save'
                selectElement.attr('disabled', false);
                btn.addClass('editing');
                btn.html('<i class="fas fa-check"></i>'); // Replace the Edit icon with the Save icon
            }
        });
    });
</script>
