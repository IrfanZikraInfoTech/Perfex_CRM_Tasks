<?php 
    $this->load->view('header');
?>

<div id="wrapper">
<div class="container">
    <h2 class="text-3xl font-bold my-6 text-center">
    Employee Payroll Details
    </h2>
    <div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="bg-gray-100">
        <tr>
            <th class="px-4 py-2 font-seminbold">Staff Code</th>
            <th class="px-4 py-2 font-seminbold">Name</th>
            <th class="px-4 py-2 font-seminbold">Title</th>
            <th class="px-4 py-2 font-seminbold">Department</th>
            <th class="px-4 py-2 font-seminbold">Email Address</th>
            <th class="px-4 py-2 font-seminbold">Bank Name</th>
            <th class="px-4 py-2 font-seminbold">Bank Acc No.</th>
            <th class="px-4 py-2 font-seminbold">Salary</th>
            <th class="px-4 py-2 font-seminbold">Action</th>
        </tr>
        </thead>
        <tbody>

            <?php 
            $custom_prefix = get_option('custom_prefix'); 
            foreach ($staffs as $staff) :
            $prefixed_staff_id = $custom_prefix . $staff['staffid'];
            ?>
                <tr>
                    <td><?php echo $prefixed_staff_id; ?></td> 
                    <td><?php echo $staff['firstname']; ?></td>
                    <td><?php echo $staff['staff_title']; ?></td>
                    <td><?php echo $staff['department_name']; ?></td>
                    <td><?php echo $staff['email']; ?></td>
                    <td><?php echo $staff['bank_name']; ?></td>
                    <td><?php echo $staff['bank_acc_no']; ?></td>
                    <td><?php echo $staff['currency'] . ' ' . $staff['employee_salary']; ?></td>
                    <td>
                        <button type="button" class="rounded transition-all bg-emerald-500 text-white hover:bg-white hover:text-emerald-500 hover:border-emerald-500 border border-solid px-3 py-1" onclick="openPayModal(<?php echo $staff['staffid']; ?>, '<?php echo $staff['employee_salary']; ?>', '<?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?>')"> <i class="fas fa-edit"></i></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>

    
    </table>
    </div>
</div>

</div>

<div class="modal" tabindex="-1" id="payModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header ">
                <h5 class="modal-title">
                <span id="employeeName"></span>'s Payment Details
                </h5>
                

            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="fromDate">From Date:</label>
                    <input type="date" id="fromDate" class="form-control" name="fromDate">
                </div>
                <div class="form-group">
                    <label for="toDate">To Date:</label>
                    <input type="date" id="toDate" class="form-control" name="toDate">
                </div>
                <p>Total Days: <span id="totalDays"></span></p>
                <p>Days Present: <span id="daysPresent"></span></p>
                <p>Unpaid Leaves: <span id="unpaidleaves"></span></p>
                <p>Leaves: <span id="leaves"></span></p>
                <p>base Salary: <span id="salary"></span></p>
                <div class="form-group">
                    <label for="currency">Select Currency:</label>
                    <select name="currency" id="currency" class="form-control">
                        <option value="INR">INR</option>
                        <option value="PKR">PKR</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bonusInput">Bonus:</label>
                    <input id="bonusInput" type="number" placeholder="Enter Bonus" oninput="calculateTotal()" class="form-control">
                </div>
                <div class="form-group">
                    <label for="allowancesInput">Other Allowances:</label>
                    <input id="allowancesInput" type="number" placeholder="Enter Allowances" oninput="calculateTotal()" class="form-control">
                </div>
                <div class="form-group">
                    <label for="deductionInput">Deduction:</label>
                    <input id="deductionInput" type="number" placeholder="Enter Deduction" oninput="calculateTotal()" class="form-control">
                </div>
                <div class="form-group">
                    <label for="unpaid_leave_deduction">Unpaid Leave Deduction:</label>
                    <input id="unpaid_leave_deduction" type="number" placeholder="Enter Unpaid Deduction" oninput="calculateTotal()" class="form-control">
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks</label>
                    <input id="remarks" type="text" placeholder="Enter Remarks"class="form-control">
                </div>
                <p>Total: <span id="total"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="closePayModal()">Close</button>
                <button type="button" class="btn btn-primary" onclick="makePayment()">Update Payment</button>
            </div>
        </div>
    </div>
</div>

<?php
    $this->load->view('footer');
?>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script>
    document.getElementById('fromDate').addEventListener('change', () => fetchAttendanceData(currentStaffId));
    document.getElementById('toDate').addEventListener('change', () => fetchAttendanceData(currentStaffId));
        

    var bonus = 0
    var deduction = 0;
    var total = 0;
    var staff_id=0;
    var payModal, bonusModal;

    function openPayModal(staffId, staffSalary, staffName) {
    salary = Number(staffSalary);
    staff_id = Number(staffId);
    document.getElementById('salary').innerText = salary;
    document.getElementById('employeeName').innerText = staffName;

    // Define fromDate and toDate based on the input values
    const fromDate = document.getElementById('fromDate').value;
    const toDate = document.getElementById('toDate').value;

    // Fetch initial attendance data
    fetchAttendanceData(staffId);
    // Fetch unpaid leaves data
    fetchUnpaidLeaves(staffId, fromDate, toDate);
    fetchleaves(staffId, fromDate, toDate);


    payModal = new bootstrap.Modal(document.getElementById('payModal'));
    payModal.show();
}

    $(document).ready(function() {
        $('#fromDate, #toDate').change(function() {
            // Ensure staff_id is defined and is the correct ID
            fetchAttendanceData(staff_id);
            fetchUnpaidLeaves(staff_id, $('#fromDate').val(), $('#toDate').val());
            fetchleaves(staff_id, $('#fromDate').val(), $('#toDate').val());

        });
    });
    function fetchAttendanceData(staffId) {
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;

        $.ajax({
            url: '<?php echo admin_url('payroll/getAttendanceData') ?>', 
            type: 'POST',
            data: {staffId: staffId, fromDate: fromDate, toDate: toDate},
            dataType: 'json', 
            success: function(response) {
                $('#totalDays').text(response.total_days);
                $('#daysPresent').text(response.days_present);
            },
            error: function(xhr, status, error) {
                console.error("Error: ", status, error);
            }
        });
    }
    function fetchUnpaidLeaves(staffId, fromDate, toDate) {
        $.ajax({
            url: '<?php echo admin_url('payroll/getUnpaidLeavesData') ?>',
            type: 'POST',
            data: {staffId: staffId, fromDate: fromDate, toDate: toDate},
            dataType: 'json',
            success: function(response) {
                // Parse as float and convert to fixed decimal places if needed
                var unpaidLeaves = parseFloat(response.unpaidLeaves).toFixed(0); // toFixed(0) will round to nearest whole number
                $('#unpaidleaves').text(unpaidLeaves); // Make sure this is within the success callback
            },
            error: function(xhr, status, error) {
                console.error("Error: ", status, error);
            }
        });
    }
    function fetchleaves(staffId, fromDate, toDate) {
        $.ajax({
            url: '<?php echo admin_url('payroll/getallLeaves') ?>',
            type: 'POST',
            data: {staffId: staffId, fromDate: fromDate, toDate: toDate},
            dataType: 'json',
            success: function(response) {
                // Parse as float and convert to fixed decimal places if needed
                if (response.leaves && Array.isArray(response.leaves)) {
                // If you want to display the number of leaves
                $('#leaves').text(response.leaves.length);
            } else {
                // Handle cases where no leaves are returned
                $('#leaves').text(0);
            }
        },
            error: function(xhr, status, error) {
                console.error("Error: ", status, error);
            }
        });
    }

    function calculateTotal() {
        bonus = Number(document.getElementById('bonusInput').value);
        deduction = Number(document.getElementById('deductionInput').value);
        unpaid_leave_deduction = Number(document.getElementById('unpaid_leave_deduction').value);
        allowances = Number(document.getElementById('allowancesInput').value);
        total = salary + bonus + allowances - deduction - unpaid_leave_deduction;
        document.getElementById('total').innerText = total;
    }

    function resetData() {
        salary = 0;
        bonus = 0;
        deduction = 0;
        total = 0;
        document.getElementById('bonusInput').value = "";
        document.getElementById('deductionInput').value = "";
        document.getElementById('unpaid_leave_deduction').value = "";
        document.getElementById('allowancesInput').value = "";
        document.getElementById('remarks').value = "";

    }

    function closePayModal() {
        payModal.hide();
        resetData();
    }

    async function makePayment() {
    var bonus = Number(document.getElementById('bonusInput').value);
    var deduction = Number(document.getElementById('deductionInput').value);
    var allowances = Number(document.getElementById('allowancesInput').value);
    var remarks = document.getElementById('remarks').value;
    var unpaid_leave_deduction = Number(document.getElementById('unpaid_leave_deduction').value);
    var total = salary + bonus + allowances - unpaid_leave_deduction - deduction;
    var currency = document.getElementById('currency').value;
    var fromDate = document.getElementById('fromDate').value;
    var toDate = document.getElementById('toDate').value;


    Swal.fire({
        title: 'Processing...',
        allowOutsideClick: false,
        showConfirmButton: false,
        onOpen: () => {
            Swal.showLoading();
        }
    });

    try {
        const response = await $.ajax({
            type: 'POST',
            url: '<?php echo admin_url("payroll/add_payment"); ?>',
            data: {
                staffId: staff_id,
                bonus: bonus,
                deduction: deduction,
                total: total,
                salary: salary,
                allowances: allowances,
                unpaid_leave_deduction: unpaid_leave_deduction,
                remarks: remarks,
                fromDate: fromDate,
                toDate: toDate,
                currency: currency
            }
        });

        const res = JSON.parse(response);

        if (res.status === 'success') {
            payModal.hide();  // Close the modal
            resetData();     // Reset the form data
            Swal.fire('Done','data update successfully', 'success');
        } else {
            payModal.hide();  // Close the modal
            resetData();     // Reset the form data
            Swal.fire('Error', 'There was a problem storing the data', 'error');
        }
    } catch (error) {
        payModal.hide();  // Close the modal
        resetData();     // Reset the form data
        Swal.fire('Error', 'There was an error with the AJAX request', 'error');
    }


}
</script>
