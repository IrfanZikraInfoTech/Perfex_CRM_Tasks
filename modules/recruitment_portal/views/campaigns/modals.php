
<!-- Add Campaign Modal -->
<div class="modal fade" id="campaignAddModal" tabindex="-1" aria-labelledby="campaignAddLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="campaignAddForm">

            <div class="modal-header text-white py-3">
                <h5 class="modal-title text-xl" id="campaignAddLabel">Add campaign</h5>
            </div>
            <div class="modal-body">
                <div class="p-2">
                    <div class="mb-6">
                        <label for="title" class="block text-gray-700 font-bold mb-2">Title <span class="text-red-600">*</span></label>
                        <input required type="text" name="title" id="title" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label for="position" class="block text-gray-700 font-bold mb-2">Position <span class="text-red-600">*</span></label>
                        <input required type="text" name="position" id="position" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label for="job_type" class="block text-gray-700 font-bold mb-2">Type <span class="text-red-600">*</span></label>
                        <select name="job_type" id="job_type" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                            <option value="fulltime">Full Time</option>
                            <option value="parttime">Part Time</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                        <textarea name="description" id="description" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                    </div>
                    <div class="mb-6">
                        <label for="experience" class="block text-gray-700 font-bold mb-2">Experience</label>
                        <input type="text" name="experience" id="experience" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" />
                    </div>
                    <div class="mb-6">
                        <label for="skills_required" class="block text-gray-700 font-bold mb-2">Skills (seperate by comma)</label>
                        <input type="text" name="skills_required" id="skills_required" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"  />
                    </div>
                    <!-- tags -->
                    <div class="mb-6">
                        <label for="camp_tag" class="block text-gray-700 font-bold mb-2">Tags (seperate by comma)</label>
                        <input type="text" id="camp_tag" name="camp_tag" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"  />
                    </div>
                    <div class="mb-6">
                        <label for="start_date" class="block text-gray-700 font-bold mb-2">Start Date <span class="text-red-600">*</span></label>
                        <input required type="date" name="start_date" id="start_date" class="form-input border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label for="end_date" class="block text-gray-700 font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-input border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="status" class="block text-gray-700 font-bold mb-2">Status </label>
                        <select name="status" id="status" class="form-select border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="salary" class="block text-gray-700 font-bold mb-2">Salary</label>
                        <input type="text" name="salary" id="salary" class="form-input border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer flex justify-left">
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </div>

            </form>
            
        </div>
    </div>
</div>

<!-- Edit Campaign Modal -->
<div class="modal fade" id="campaignEditModal" tabindex="-1" aria-labelledby="campaignEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="campaignEditForm">

            <div class="modal-header text-white py-3">
                <h5 class="modal-title text-xl" id="campaignEditLabel">Edit campaign</h5>
            </div>
            <div class="modal-body">
                <div class="p-2">

                    <input required type="hidden" name="campaignId" id="edit_id">

                    <div class="mb-6">
                        <label for="edit_title" class="block text-gray-700 font-bold mb-2">Title <span class="text-red-600">*</span></label>
                        <input type="text" name="title" id="edit_title" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label for="edit_position" class="block text-gray-700 font-bold mb-2">Position <span class="text-red-600">*</span></label>
                        <input type="text" name="position" id="edit_position" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label for="edit_job_type" class="block text-gray-700 font-bold mb-2">Type <span class="text-red-600">*</span></label>
                        <select name="job_type" id="edit_job_type" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                            <option value="fulltime">Full Time</option>
                            <option value="parttime">Part Time</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                        <textarea name="description" id="edit_description" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                    </div>
                    <div class="mb-6">
                        <label for="experience" class="block text-gray-700 font-bold mb-2">Experience</label>
                        <input type="text" name="experience" id="edit_experience" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" />
                    </div>
                    <div class="mb-6">
                        <label for="skills_required" class="block text-gray-700 font-bold mb-2">Skills (seperate by comma)</label>
                        <input type="text" name="skills_required" id="edit_skills_required" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"  />
                    </div>
                    <!-- tags -->
                     <div class="mb-6">
                        <label for="camp_tag" class="block text-gray-700 font-bold mb-2">tags (seperate by comma)</label>
                        <input type="text" id="edit_camp_tag" name="camp_tag" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"  />   
                    </div>
                   
                    <div class="mb-6">
                        <label for="start_date" class="block text-gray-700 font-bold mb-2">Start Date <span class="text-red-600">*</span></label>
                        <input type="date" name="start_date" id="edit_start_date" class="form-input border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" required>
                    </div>
                    <div class="mb-6">
                        <label for="edit_end_date" class="block text-gray-700 font-bold mb-2">End Date</label>
                        <input type="date" name="end_date" id="edit_end_date" class="form-input border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="edit_status" class="block text-gray-700 font-bold mb-2">Status</label>
                        <select name="status" id="edit_status" class="form-select border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-6">
                    <label for="edit_salary" class="block text-gray-700 font-bold mb-2">Salary</label>
                        <input type="text" name="salary" id="edit_salary" class="form-input border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>
                </div>
            </div>
            <div class="modal-footer flex justify-left">
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>

            </form>
            
        </div>
    </div>
</div>

<!-- Edit Detail Modal -->
<div class="modal fade" id="campaignDetailModal" tabindex="-1" aria-labelledby="campaignDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="campaignDetailForm">

            <div class="modal-header text-white py-3">
                <h5 class="modal-title text-xl" id="campaignDetailLabel"></h5>
            </div>
            <div class="modal-body">
                <div class="p-2">

                    <input required type="hidden" name="campaignId" id="detail_edit_id">

                    <div>
                        <textarea id="detail_edit_content" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer flex justify-left">
                <div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>

            </form>
            
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="permissionsModal" tabindex="-1" role="document" aria-labelledby="permissionsModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="permissionsModalLabel">Set Campaign Permissions</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="staffTable" style="margin-top:0px;" class="my-0 table table-striped">
            <thead>
                <tr>
                    <th scope="col">Staff Name</th>
                    <th scope="col">View</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Act</th>
                </tr>
            </thead>
            <tbody>
            <input type="hidden" id="permission_campaign" />
                <?php 
                    foreach ($staff_members as $staff) {
                        $fullname = $staff['firstname'] . ' ' . $staff['lastname'];
                        echo '
                        <tr id="staff_'.$staff['staffid'].'" class="mb-2">
                            <td class="py-2">' . $fullname . '</td>
                            <td class="py-2"><label class="inline-flex items-center">
                                <input type="checkbox" id="'.$staff['staffid'].'_view" name="'.$staff['staffid'].'_view" class="form-checkbox h-5 w-5 text-green-600 permission_checkbox">
                            </label></td>
                            <td class="py-2"><label class="inline-flex items-center">
                                <input type="checkbox" id="'.$staff['staffid'].'_edit" name="'.$staff['staffid'].'_edit" class="form-checkbox h-5 w-5 text-yellow-600 permission_checkbox">
                            </label></td>
                            <td class="py-2"><label class="inline-flex items-center">
                                <input type="checkbox" id="'.$staff['staffid'].'_act" name="'.$staff['staffid'].'_act" class="form-checkbox h-5 w-5 text-red-600 permission_checkbox">
                            </label></td>
                        </tr>';
                    }
                ?>
            </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="rounded transition-all bg-blue-600 text-white hover:bg-white hover:text-blue-500 hover:border-blue-500 border border-solid px-4 py-2" onclick="save_permissions();">Save permissions</button>
        <button type="button" class="rounded transition-all bg-gray-200 text-black hover:bg-white border border-solid px-4 py-2" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




