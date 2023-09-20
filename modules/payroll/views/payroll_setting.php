<?php 
    $this->load->view('header');
?>

<div id="wrapper">
<div class="container">
    <h2 class="text-center my-6 text-2xl">Payroll Setting</h2>
</div>

<div class="container">
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Salary</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffs as $staff) : ?>
                <tr id="row-<?php echo $staff['staffid']; ?>">
                    <td><?php echo $staff['firstname']; ?></td>
                    <td><?php echo $staff['email']; ?></td>
                    <td id="salary-<?php echo $staff['staffid']; ?>"><?php echo $staff['employee_salary']; ?></td>
                    <td>
                        <button type="button" class="rounded transition-all bg-blue-500 text-white hover:bg-white hover:text-blue-500 hover:border-blue-500 border border-solid px-4 py-2" data-toggle="modal" data-target="#addSalaryModal" onclick="open_salary_model(<?php echo $staff['staffid'] ?>)">Edit Salary</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addSalaryModal" tabindex="-1" role="dialog" aria-labelledby="addSalaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSalaryModalLabel">Edit Salary</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="employee_id" value="" id="employee_id">
                <div class="form-group">
                    <label for="salary">Salary:</label>
                    <input type="number" id="salary" name="salary" step="0.01" required class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitSalary">Submit</button>
            </div>
        </div>
    </div>
</div>


    
<?php
    $this->load->view('footer');
?>

<script>
    function open_salary_model(staffid) {
        document.getElementById('employee_id').value = staffid;
    }

    $(document).ready(function() {
        $('#submitSalary').click(function() {
            var employeeId = $('#employee_id').val();
            var salary = $('#salary').val();

            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url("payroll/add_salary"); ?>',
                data: {
                    employee_id: employeeId,
                    salary: salary
                },
                success: function(response) {
                    // Update the entire row in the table with the updated salary
                    $('#row-' + employeeId + ' #salary-' + employeeId).text(salary);
                    $('#addSalaryModal').modal('hide'); // Hide the modal
                },
                error: function(xhr, status, error) {
                    // Handle the error if the AJAX request fails
                    console.log(error);
                }
            });
        });

        $('#addSalaryModal').on('shown.bs.modal', function() {
            $('#salary').val(''); // Clear the input field when the modal is shown
        });
    });
</script>

</div>
