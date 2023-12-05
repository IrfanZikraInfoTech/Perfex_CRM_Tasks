<?php 
    $this->load->view('header');
?>
<div id="wrapper">
    <!-- Monthly Dropdown -->
    <div class="container">
    <h2 class="text-center my-6 text-2xl">Salary Slip</h2> 
        <!-- Adding Month and Year Dropdown -->
        <form method="get" action="" class="form-inline">
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
            <div class="form-group mx-sm-3 mb-2">
                <label for="year" class="mr-2">Year:</label>
                <select name="year" id="year" class="form-control">
                    <?php for($y=date('Y'); $y>=2000; $y--){ ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
        <!-- End of Dropdown -->
    </div>

    
    <div class="container">
        <table class="table table-responsive">
            <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Approve Salary</th>
                    <th scope="col">Issue Name</th>
                    <th scope="col">Payment Mode</th>
                    <th scope="col">Reference Number</th>
                    <th scope="col">Remark</th>
                    <?php if(is_admin()){?>
                    <th scope="col">Save</th>
                    <?php } ?>
                    <th scope="col">Generate Slip</th>
                    <th scope="col">View</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                    $selectedMonth = isset($_GET['month']) ? str_pad($_GET['month'], 2, '0', STR_PAD_LEFT) : null;
                    $selectedYear = isset($_GET['year']) ? $_GET['year'] : date('Y');
                    if(is_array($staffs)){
                        foreach ($staffs as $staff) :
                            if($staff['staff_id'] == $this->session->userdata('staff_user_id') || is_admin()) :
                                // Convert fromDate and toDate to DateTime objects
                                $fromDate = new DateTime($staff['fromDate']);
                                $toDate = new DateTime($staff['toDate']);

                                // Extract month and year from fromDate
                                $rowMonth = $fromDate->format('m');
                                $rowYear = $fromDate->format('Y');

                                // Check if the row's month and year match the selected month and year
                                if (($selectedMonth === null || $selectedMonth == $rowMonth) && $selectedYear == $rowYear) :
                ?>
       
                <tr data-month="<?php echo $rowMonth; ?>">
                    <td><?php echo $staff['firstname']; ?></td>
                    <td><?php echo $staff['currency'] . ' ' . $staff['salary'] + $staff['bonus']+$staff['allowances'] - $staff['unpaid_leave_deduction'] - $staff['deduction']; ?></td>
                    <td><?php echo $staff['approver_name']; ?></td>
                    <td>
                        <select class="form-control payment-mode" data-id="<?php echo $staff['id']; ?>" disabled>
                            <option value="Bank" <?php if($staff['payment_mode'] == 'Bank') echo 'selected'; ?>>Bank</option>
                            <option value="Cash" <?php if($staff['payment_mode'] == 'Cash') echo 'selected'; ?>>Cash</option>
                            <option value="UPI" <?php if($staff['payment_mode'] == 'UPI') echo 'selected'; ?>>UPI</option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control reference-number" data-id="<?php echo $staff['id']; ?>" value="<?php echo $staff['refrence_number']; ?>" disabled /></td>
                    <td><input type="text" class="form-control remark" data-id="<?php echo $staff['id']; ?>" value="<?php echo $staff['remark']; ?>" disabled /></td>
                
                    <?php if(is_admin()){?>
                    <td><button class="btn btn-secondary edit-save-btn" data-id="<?php echo $staff['id']; ?>" data-editing="false">Edit</button></td>
                    <?php } ?>

                    <td><a href="<?php echo admin_url("payroll/view_payslip")."/".$staff['id']; ?>" class="btn btn-primary view-slip" data-id="<?php echo $staff['id']; ?>">Download Slip</a></td>
                    <td><a target="_blank" href="<?php echo admin_url("payroll/view_payslip")."/".$staff['id']; ?>" class="btn btn-primary view-slip" data-id="<?php echo $staff['id']; ?>">View </a></td>

                    
                </tr>
            <?php 
                    endif;
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
        var updateUrl = '<?php echo base_url('payroll/save_payment_mode'); ?>';
        $('#month, #year').change(function() {
            var selectedMonth = $('#month').val();
            var selectedYear = $('#year').val();

            $('table tbody tr').each(function() {
                var rowMonth = $(this).data('month');
                var rowYear = $(this).data('year');

                if(rowMonth == selectedMonth && rowYear == selectedYear) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
        $('.edit-save-btn').click(function() {
            var id = $(this).data('id');
            var isEditing = $(this).data('editing');

            if(isEditing) {
                // 'Save' was clicked
                var selectedPayment_mode = $('.payment-mode[data-id="'+ id +'"]').val();
                var referenceNumber = $('.reference-number[data-id="' + id + '"]').val();
                var remark = $('.remark[data-id="' + id + '"]').val();

                // Send AJAX request to save the data
                $.ajax({
                    url: '<?php echo base_url('payroll/save_reference_number'); ?>',
                    type: 'POST',
                    data: {
                        'id': id,
                        'reference_number': referenceNumber,
                        'remark': remark,
                        'payment_mode': selectedPayment_mode
                    },
                    success: function(response) {
                        // Data was saved, now disable the fields
                        $('.payment-mode[data-id="'+ id +'"], .reference-number[data-id="'+ id +'"],.remark[data-id="' + id + '"]').prop('disabled', true);
                        $('.edit-save-btn[data-id="'+ id +'"]').data('editing', false).text('Edit');
                    },
                    error: function(response) {
                        console.log(response);
                    }
                });
            } else {
                // 'Edit' was clicked, enable the fields and change button text
                $('.payment-mode[data-id="'+ id +'"], .reference-number[data-id="'+ id +'"],.remark[data-id="' + id + '"]').prop('disabled', false);
                $(this).data('editing', true).text('Save');
            }
        });        
    });
</script>
