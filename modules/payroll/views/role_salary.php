<?php 
    $this->load->view('header');
?>

<div id="wrapper">
<div class="container">
    <h2 class="text-center my-6 text-2xl">Employee Payroll Details</h2>
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Salary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffs as $staff) : ?>
                <tr>
                    <td><?php echo $staff['firstname']; ?></td>
                    <td><?php echo $staff['email']; ?></td>
                    <td><?php echo $staff['currency'] . ' ' . $staff['employee_salary']; ?></td>
                    <td>
                        <button type="button" class="rounded transition-all bg-emerald-500 text-white hover:bg-white hover:text-emerald-500 hover:border-emerald-500 border border-solid px-4 py-2" onclick="openPayModal(<?php echo $staff['staffid']; ?>, '<?php echo $staff['employee_salary']; ?>')">Update</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<div class="modal" tabindex="-1" id="payModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="month">Select Month:</label>
                    <select name="month" id="month" class="form-control">
                        <option value="1">Jan</option>
                        <option value="2">Feb</option>
                        <option value="3">Mar</option>
                        <option value="4">Apr</option>
                        <option value="5">May</option>
                        <option value="6">Jun</option>
                        <option value="7">Jul</option>
                        <option value="8">Aug</option>
                        <option value="9">Set</option>
                        <option value="10">Oct</option>
                        <option value="11">Nov</option>
                        <option value="12">Dec</option>
                    </select>
                </div>
                <p>Salary: <span id="salary"></span></p>
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
                    <label for="deductionInput">Deduction:</label>
                    <input id="deductionInput" type="number" placeholder="Enter Deduction" oninput="calculateTotal()" class="form-control">
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
    var salary = 0;
    var bonus = 0
    var deduction = 0;
    var total = 0;
    var staff_id=0;
    var payModal, bonusModal;

    function openPayModal(staffId, staffSalary) {
        salary = Number(staffSalary);
        staff_id = Number(staffId);
        document.getElementById('salary').innerText = salary;
        payModal = new bootstrap.Modal(document.getElementById('payModal'));
        payModal.show();
    }

    function calculateTotal() {
        bonus = Number(document.getElementById('bonusInput').value);
        deduction = Number(document.getElementById('deductionInput').value);
        total = salary + bonus - deduction;
        document.getElementById('total').innerText = total;
    }

    function resetData() {
        salary = 0;
        bonus = 0;
        deduction = 0;
        total = 0;
        document.getElementById('bonusInput').value = "";
        document.getElementById('deductionInput').value = "";
    }

    function closePayModal() {
        payModal.hide();
        resetData();
    }

    async function makePayment() {
    var month = document.getElementById('month').value;
    var bonus = Number(document.getElementById('bonusInput').value);
    var deduction = Number(document.getElementById('deductionInput').value);
    var total = salary + bonus - deduction;
    var currency = document.getElementById('currency').value;

    // Show Processing alert
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
                month: month,
                salary: salary,
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
