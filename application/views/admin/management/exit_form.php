<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full flex flex-col gap-4">

            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Employee Exit Form
                </h2>
            </div>
            <form class="space-y-4 bg-white p-10" method="post" action="<?php echo admin_url('team_management/save_exit'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">           

            <div class="form-row">
                <div class="form-group col-md-6 mb-4">
                    <input type="hidden" name="staff_id" value="<?php echo $GLOBALS['current_user']->staffid; ?>">

                    <label for="staffName">Staff Name</label>
                    <input type="text" class="form-control" id="staffName" required value="<?php echo $GLOBALS['current_user']->firstname; ?>" readonly>
                </div>


                <!-- Visible field for Department Name -->
                <div class="form-group col-md-6 mb-4">
                    <input type="hidden" name="department_id" value="<?php echo $GLOBALS['current_user']->department_id; ?>">

                    <label for="departmentName">Department</label>
                    <input type="text" class="form-control" id="departmentName" required value="<?php echo $GLOBALS['current_user']->department_name; ?>" readonly>
                </div>
            </div>



            <div class="form-row">
                <div class="form-group col-md-6 mb-4">
                    <label for="seperation_date">Seperation Date</label>
                    <input type="date" class="form-control" id="seperation_date" name="seperation_date" required>
                </div>

                <div class="form-group col-md-6 mb-4">
                    <label for="reason">Reason for Leaving</label>
                    <select class="form-control" id="reason" name="reason" required>
                        <option value="">Select</option>
                        <option value="BEC">Better Employment Conditions</option>
                        <option value="career">Career Prospect</option>
                        <option value="desertion">Desertion</option>
                        <option value="dismissed">Dismissed</option>
                        <option value="dissatisfaction">Dissatisfaction with the job</option>
                        <option value="emigrating">Emigrating</option>
                        <option value="health">Health</option>
                        <option value="pay">Higher Pay</option>
                        <option value="conflicts">Personality Conflicts</option>
                        <option value="retirement">Retirement</option>
                        <option value="retrenchment">Retrenchment</option>
                        <option value="death">Death</option>

                    </select>
                </div>

            </div>

            <div class="form-row">
                <div class="form-group col-md-12 my-4">
                    <label for="working_again">Working For this organization again</label>
                    <select class="form-control" id="working_again" name="working_again" required>
                        <option value="">Select</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>

            <div class="form-group  col-md-12 mb-4">
            <label for="likes_about_org">What did you like the most about the organization</label>
                <textarea class="form-control"  id="likes_about_org" name="likes_about_org" rows="3" required></textarea>
            </div>

            <div class="form-group  col-md-12 mb-4">
                <label for="improvement_suggestions">Things the organization do to improve the staff welfare</label>
                <textarea class="form-control" id="improvement_suggestions" name="improvement_suggestions" rows="3" required></textarea>
            </div>
            
            <div class="form-group  col-md-12  mb-4">
                <label for="additional_comments">Anything you wish to share with us</label>
                <textarea class="form-control" id="additional_comments" name="additional_comments" rows="3" required></textarea>
            </div>

            <div class="form-group col-md-12">
                <input type="submit" value="Submit" class="w-full px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-800 cursor-pointer">
            </div>
            </form>
          
        </div>

        <div class="w-full flex flex-col gap-4">

            <div class="w-full">
                <h2 class="text-3xl font-bold text-center">Employee Exit Details</h2>
            </div>

            <div class="align-middle inline-block p-2">
                <table id="all-applications" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seperation Date</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Working again</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Likes about Organization</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Things to improve</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comments</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($exit_data as $data): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['id'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['seperation_date'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['reason'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['working_again'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['likes_about_org'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['improvement_suggestions'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $data['additional_comments'];  ?>
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
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var separationDateInput = document.getElementById('seperation_date');

    // Calculate the date 8 days from today
    var today = new Date();
    var eightDaysFromNow = new Date(today.getFullYear(), today.getMonth(), today.getDate() + 8);
    var minDate = eightDaysFromNow.toISOString().split('T')[0];

    // Set the min attribute to 8 days from today's date
    separationDateInput.setAttribute('min', minDate);
});

</script>