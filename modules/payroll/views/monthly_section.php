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
                    // Set the selected month to current month if none is set
                    $selectedMonth = isset($_GET['month']) ? $_GET['month'] : date('n');
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
                <?php 
                    // Set the selected year to current year if none is set
                    $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                    for($y=date('Y'); $y>=2000; $y--){
                ?>
                <option value="<?php echo $y; ?>" <?php echo $y == $selectedYear ? 'selected="selected"' : ''; ?>>
                    <?php echo $y; ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    </div>

    
    <div class="container mt-4">
        <table class="table table-responsive">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Deduction</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Other Allowances</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Deduction</th>         
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approver Name</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                    <td class="px-2 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['firstname']; ?></td>
                    <td class="px-2 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['currency'] . ' ' . $staff['salary']; ?></td>
                    <td class="px-2 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['currency'] . ' ' .$staff['bonus']; ?></td>
                    <td class="px-2 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['currency'] . ' ' . $staff['deduction']; ?></td>
                    <td class="px-2 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['currency'] . ' ' . $staff['allowances']; ?></td>
                    <td class="px-2 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['currency'] . ' ' . $staff['unpaid_leave_deduction']; ?></td>

                        <?php
                            // Calculate total as base salary + bonus - deduction
                            $totalAmount = $staff['total'] ;
                            ?>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['currency'] . ' ' . $totalAmount; ?></td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500" style="display: flex; align-items: center;">
                            <select name="approval_status" class="form-control approval-status" data-id="<?php echo $staff['id']; ?>" style="width: auto; margin-right: 10px;" disabled>
                                <option value="2"<?php if($staff['status'] == '2') echo 'selected'; ?>>Approved</option>
                                <option value="0"<?php if($staff['status'] == '0') echo 'selected'; ?>>Rejected</option>
                            </select>
                            <button type="button" class="btn btn-primary edit-save-btn" data-id="<?php echo $staff['id']; ?>"><i class="fas fa-edit"></i></button>
                        </td>
                        <td class="px-4 py-2 whitespace-nowrap text-sm text-center text-gray-500"><?php echo $staff['approver_name']; ?></td>
                                <!-- Add the delete button to each row -->
                                <!-- Inside your table body loop -->
                                <td class="flex px-4 py-2 whitespace-nowrap text-center text-sm text-gray-500">
                            <!-- Pay Button to Open Modal -->
                            <?php
                                // Show Pay button if the approver name is either "Ansar" or "Anwar"
                                $canShowPayButton = ($staff['approver_name'] === 'Ansar' || $staff['approver_name'] === 'Anwaar');
                            ?>
                            <button type="button" onclick="showAlertAndModal('<?php echo $staff['id']; ?>');" 
                                class="btn btn-success pay-button" 
                                data-staffId="<?php echo $staff['id']; ?>" 
                                data-totalAmount="<?php echo $totalAmount; ?>" 
                                data-approver="<?php echo $staff['approver_name']; ?>"
                                <?php echo $canShowPayButton ? '' : 'disabled'; ?> style="color:white; background-color:green">
                                Pay
                            </button>
                            <form action="<?php echo admin_url('payroll/delete_record'); ?>" method="post">
                                <input type="hidden" name="<?=$this->security->get_csrf_token_name();?>" value="<?=$this->security->get_csrf_hash();?>">
                                <input type="hidden" name="id_to_delete" value="<?php echo $staff['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="background-color:#DC2626">Delete</button>
                            </form>
                        </td>
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
<div class="modal fade" id="payModal" tabindex="-1" aria-labelledby="payModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="payModalLabel">Payment Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Confirm payment for <?php echo $staff['firstname']; ?>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="hidemodel();">Cancel</button>
                <button type="button" class="btn btn-primary confirm-pay" onclick="confirmPaymentAndShowSwal();" data-id="<?php echo $staff['id']; ?>">Success</button>
            </div>
        </div>
    </div>
</div>
<script>
   function showAlertAndModal(staffId) {
        // Get the corresponding Pay button
        var payButton = document.querySelector(`.pay-button[data-staffId="${staffId}"]`);

        // Check if the button is disabled
        if(payButton.disabled) {
            return;
        }

        var modalId = '#payModal';
        $(modalId).modal('show');
    }
    function hidemodel() {
        var modalId = '#payModal';
        $(modalId).modal('hide');
    }
    async function confirmPaymentAndShowSwal() {
        var modalId = '#payModal';
        $(modalId).modal('hide'); // Hide the existing Bootstrap modal
        
        // First, show a "Processing..." dialog
        Swal.fire({
            title: 'Processing...',
            html: 'Please wait while we confirm your payment.',
            timer: 2000, // time in milliseconds that the alert will be shown
            timerProgressBar: true,
            showConfirmButton: false,
            didOpen: () => {
            Swal.showLoading();
            }
        }).then((result) => {
            // After timer ends, show the "Success" dialog
            if (result.dismiss === Swal.DismissReason.timer) {
            Swal.fire({
                title: 'Success!',
                text: 'Payment confirmed.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            }
        });
    }
</script>
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
