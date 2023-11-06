<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full flex flex-col gap-4">
            <div class="w-full mb-4">
                
                <h2 class="text-3xl font-bold text-center">All Requisitions</h2>

            </div>

            <!-- <div class="self-center flex flex-row gap-4">
                <button onclick="refreshDataTable('Pending')" class="btn-primary p-2 px-4 text-white">Pending</button>
                <button onclick="refreshDataTable('Approved')" class="btn-primary p-2 px-4 text-white">Approved</button>
                <button onclick="refreshDataTable('Disapproved')" class="btn-primary p-2 px-4 text-white">Disapproved</button>
            </div> -->


            <div class="align-middle inline-block p-2">
                    <table id="all-requisitions" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff member</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department </th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title</th>
                                <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($allRequisitions as $requisition): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $requisition['id'];  ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $requisition['staff_name'];  ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $requisition['department_name'];  ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="triggerModal cursor-pointer" data-requisition='<?php echo htmlspecialchars(json_encode($requisition), ENT_QUOTES, 'UTF-8'); ?>'>
                                        <?php echo $requisition['job_title']; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <form method="post">
                                        <select id="statusSelect-<?php echo $requisition['id']; ?>" class="statusSelect form-control border p-2 rounded shadow-sm focus:ring focus:ring-opacity-50">
                                            <option class="bg-white border-green-500" value="Pending" <?php echo $requisition['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option class="bg-white border-blue-500" value="Approved" <?php echo $requisition['status'] == 'Approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option class="bg-white border-red-500" value="Disapproved" <?php echo $requisition['status'] == 'Disapproved' ? 'selected' : ''; ?>>Disapproved</option>
                                            <option class="bg-white border-yellow-500" value="Hired" <?php echo $requisition['status'] == 'Hired' ? 'selected' : ''; ?>>Hired</option>
                                        </select>
                                    </form>
                                </td>
                                <!-- More TDs for other fields if needed -->
                            </tr>
                            <?php endforeach; ?>
                        </tbody>

                    </table>
            </div>
        </div>

    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="requisitionModal" tabindex="-1" role="dialog" aria-labelledby="requisitionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-lg font-bold" id="requisitionModalLabel">Requisition Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
  <div class="p-4">
    <div class="mb-4">
      <div class="mb-2">
        <strong class="font-semibold">Job Title:</strong> <span id="modalJobTitle" class="text-gray-700"></span>
      </div>
    </div>
    <div class="flex flex-wrap -mx-2 mb-4">
      <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
        <strong class="font-semibold">Position Type:</strong> <span id="modalPositionType" class="text-gray-700"></span>
      </div>
      <div class="w-full md:w-1/2 px-2">
        <strong class="font-semibold">Employment Type:</strong> <span id="modalEmploymentType" class="text-gray-700"></span>
      </div>
    </div>
    <div class="flex flex-wrap -mx-2 mb-4">
      <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
        <strong class="font-semibold">Expected Start Date:</strong> <span id="modalExpectedStartDate" class="text-gray-700"></span>
      </div>
      <div class="w-full md:w-1/2 px-2">
        <strong class="font-semibold">Experience:</strong> <span id="modalExperience" class="text-gray-700"></span>
      </div>
    </div>
    <div class="mb-4">
      <strong class="font-semibold">Reason for Requisition:</strong>
      <p id="modalReasonForRequisition" class="text-gray-700 mt-1"></p>
    </div>
    <div class="mb-4">
      <strong class="font-semibold">Duties and Responsibilities:</strong>
      <p id="modalDuties" class="text-gray-700 mt-1"></p>
    </div>
    <div class="mb-4">
      <strong class="font-semibold">Qualifications:</strong>
      <p id="modalQualifications" class="text-gray-700 mt-1"></p>
    </div>
    <div class="flex flex-wrap -mx-2">
      <div class="w-full md:w-1/2 px-2 mb-4 md:mb-0">
        <strong class="font-semibold">Work Schedule:</strong> <span id="modalWorkSchedule" class="text-gray-700"></span>
      </div>
      <div class="w-full md:w-1/2 px-2">
        <strong class="font-semibold">Salary:</strong> <span id="modalSalary" class="text-gray-700"></span>
      </div>
    </div>
  </div>
</div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
$(document).ready(function() {
    $('.statusSelect').on('change', function() {
        var requisitionId = this.id.split('-')[1];
        var status = $(this).val();
        console.log(requisitionId, status); // Add this to debug

        $.ajax({
            url: '<?php echo admin_url('Recruitment_portal/update_requisition_status'); ?>',
            type: 'POST',
            data: {
                'requisition_id': requisitionId,
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
$(document).ready(function() {
    $('.triggerModal').on('click', function() {
        var requisitionData = $(this).data('requisition');
        console.log(requisitionData);

        $('#modalJobTitle').text(requisitionData.job_title);
        $('#modalPositionType').text(requisitionData.position_type);
        $('#modalEmploymentType').text(requisitionData.employment_type);
        $('#modalExpectedStartDate').text(requisitionData.expected_start_date);
        $('#modalExperience').text(requisitionData.experience);
        $('#modalReasonForRequisition').text(requisitionData.reason_for_requisition);
        $('#modalDuties').text(requisitionData.duties_and_responsibilities);
        $('#modalQualifications').text(requisitionData.qualifications);
        $('#modalWorkSchedule').text(requisitionData.work_schedule);
        $('#modalSalary').text(requisitionData.salary);

        $('#requisitionModal').modal('show');
    });
});
</script>


</body>
</html>