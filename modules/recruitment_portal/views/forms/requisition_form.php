<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full flex flex-col gap-4">

            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Employee Requisition Form
                </h2>
            </div>
            <form class="space-y-4 bg-white p-10" method="post" action="<?php echo admin_url('recruitment_portal/saveRequisition'); ?>">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">           

            <div class="form-row">
                <div class="form-group col-md-6 mb-4">
                    <!-- Hidden field for Staff ID -->
                    <input type="hidden" name="staff_id" value="<?php echo $GLOBALS['current_user']->staffid; ?>">

                    <label for="staffName">Staff Name</label>
                    <input type="text" class="form-control" id="staffName" required value="<?php echo $GLOBALS['current_user']->firstname; ?>" readonly>
                </div>


                <!-- Visible field for Department Name -->
                <div class="form-group col-md-6 mb-4">
                    <!-- Hidden field for Department ID -->
                    <input type="hidden" name="department_id" value="<?php echo $GLOBALS['current_user']->department_id; ?>">

                    <label for="departmentName">Department</label>
                    <input type="text" class="form-control" id="departmentName" required value="<?php echo $GLOBALS['current_user']->department_name; ?>" readonly>
                </div>
            </div>



            <div class="form-row">
                <!-- Job Title -->
                <div class="form-group col-md-6 mb-4">
                    <label for="jobTitle">Position Job Title</label>
                    <input type="text" class="form-control" id="jobTitle" name="job_title" required>
                </div>

                <!-- Job Type -->
                <div class="form-group col-md-6 mb-4">
                    <label for="jobType">Job Type</label>
                    <select class="form-control" id="jobType" name="job_type" required>
                        <option value="">Select</option>
                        <option value="New">New</option>
                        <option value="Replacement">Replacement</option>
                        <option value="Temporary">Temporary</option>
                    </select>
                </div>

            </div>

            <div class="form-row">
                <!-- Type of Employment -->
                <div class="form-group col-md-6 mb-4">
                    <label for="employmentType">Type of Employment</label>
                    <select class="form-control" id="employmentType" name="employment_type" required>
                        <option value="">Select</option>
                        <option value="full_time">Full Time</option>
                        <option value="part_time">Part Time</option>
                        <option value="contract">Contract</option>
                        <option value="temporary">Temporary</option>
                        <option value="internship">Internship</option>
                    </select>
                </div>

                <!-- Expected Start Date -->
                <div class="form-group col-md-6 mb-4">
                    <label for="expectedStartDate">Expected Start Date</label>
                    <input type="date" class="form-control" id="expectedStartDate" name="expected_start_date" required>
                </div>
            </div>
            <div class="form-group col-md-6 mb-4">
                <label for="experience">Experience</label>
                    <select class="form-control" id="experience" name="experience" required>
                        <option value="">Select</option>
                        <option value="0-1_years">0-1 years</option>
                        <option value="2_years">2 years</option>
                        <option value="3_years">3 years</option>
                        <option value="4_years">4 years</option>
                        <option value="5_years">5 years</option>
                        <option value="more_then_5_years">More than 5 years</option>
                    </select>          
            </div>
            <div class="form-group  col-md-6 mb-4">
                <label for="salary">Salary
                <span class="text-gray-400 text-sm">
                    - [Mention in PKR or INR]
                </span>
                </label>
                <input type="text" class="form-control" id="salary" name="salary" required>
            </div>

            <div class="form-group  col-md-12 mb-4">
            <label for="reasonForRequisition">Reason for Requisition <span class="text-gray-400 text-sm">- [Brief explanation of why the new position is needed]</span></label>
                <textarea class="form-control"  id="reasonForRequisition" name="reason_for_requisition" rows="3" required></textarea>
            </div>

            <div class="form-group  col-md-12 mb-4">
                <label for="dutiesResponsibilities"> Duties and Responsibilities
                    <span class="text-gray-400 text-sm"> 
                    - [List of essential duties and responsibilities of the position, as well as the required skills and experience]
                    </span>
                </label>
                <textarea class="form-control" id="dutiesResponsibilities" name="duties_and_responsibilities" rows="3" required></textarea>
            </div>
            
            <div class="form-group  col-md-12  mb-4">
                <label for="qualifications">Qualifications
                    <span class="text-gray-400 text-sm">
                    - [List of desired qualifications for the position, such as education, experience, and skills]
                    </span>
                </label>
                <textarea class="form-control" id="qualifications" name="qualifications" rows="3" required></textarea>
            </div>

            <div class="form-group col-md-12 mb-4 ">
                <label for="workSchedule">Work Schedule
                    <span class="text-gray-400 text-sm">
                    - What will be the position's normal work schedule (days of week, shift, starting time, ending time, etc.)?  
                    </span> 
                </label>
                <input type="text" class="form-control" id="workSchedule" name="work_schedule" required>
            </div>
            <div class="form-group  col-md-12 mb-4">
                <label for="additional_info"> Additional Information
                    <span class="text-gray-400 text-sm"> 
                    - [Write any additional information you feel is important to the review of this request]
                    </span>
                </label>
                <textarea class="form-control" id="additional_info" name="additional_info" rows="3" required></textarea>
            </div>
           
            <div class="form-group  col-md-12  ">
                <input type="submit" value="Submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-800 cursor-pointer">
            </div>
            </form>
          
        </div>

        <div class="w-full flex flex-col gap-4">

            <div class="w-full mb-4">
                <h2 class="text-3xl font-bold text-center">Employee Requisition Form</h2>
            </div>

            <div class="align-middle inline-block p-2">
                <table id="all-applications" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Title </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Job Position </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Type</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($myRequisition as $requisition): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $requisition['id'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $requisition['job_title'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $requisition['position_type'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $requisition['employment_type'];  ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo $requisition['status'];  ?>
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

<?php init_tail(); ?>
</body>
</html>