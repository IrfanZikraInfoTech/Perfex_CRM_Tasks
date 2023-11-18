<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full flex flex-col gap-4">
            <div class="w-full mb-4">
                
                <h2 class="text-3xl font-bold text-center">All Exit</h2>

            </div>


            <div class="align-middle inline-block p-2">
                    <table id="all-exit" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff Member</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seperation Date</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($all_forms as $form): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $form['id'];  ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $form['staff_name'];  ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $form['department_name'];  ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $form['seperation_date']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $form['reason']; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <form method="post">
                                        <select id="statusSelect-<?php echo $form['id']; ?>" class="statusSelect form-control border p-2 rounded shadow-sm focus:ring focus:ring-opacity-50">
                                            <option class="bg-white border-green-500" value="Pending" <?php echo $form['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option class="bg-white border-blue-500" value="Approved" <?php echo $form['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option class="bg-white border-red-500" value="Disapproved" <?php echo $form['status'] == 'Disapproved' ? 'selected' : ''; ?>>Disapproved</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
            </div>
        </div>

    </div>
</div>



<?php init_tail(); ?>
<script>
$(document).ready(function() {
    $('.statusSelect').on('change', function() {
        var form_id = this.id.split('-')[1];
        var status = $(this).val();
        console.log(form_id, status); // Add this to debug

        $.ajax({
            url: '<?php echo admin_url('team_management/update_exit_form_status'); ?>',
            type: 'POST',
            data: {
                'form_id': form_id,
                'status': status,
            },
            success: function(response) {
                var jsonResponse = (typeof response === 'object') ? response : JSON.parse(response);

                if(jsonResponse.status) {
                    Swal.fire({
                        title: 'Updated!',
                        text: jsonResponse.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: jsonResponse.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
                },
        });
    });
});

</script>


</body>
</html>