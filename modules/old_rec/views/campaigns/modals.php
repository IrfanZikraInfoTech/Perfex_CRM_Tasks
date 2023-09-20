
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
                        <label for="description" class="block text-gray-700 font-bold mb-2">Description</label>
                        <textarea name="description" id="description" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
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
                        <label for="edit_description" class="block text-gray-700 font-bold mb-2">Description</label>
                        <textarea name="description" id="edit_description" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
                    </div>
                    <div class="mb-6">
                        <label for="edit_start_date" class="block text-gray-700 font-bold mb-2">Start Date <span class="text-red-600">*</span></label>
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


