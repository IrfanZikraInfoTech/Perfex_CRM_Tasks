
<!-- Add Template Modal -->
<div class="modal fade" id="templateAddModal" tabindex="-1" aria-labelledby="templateAddLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="templateAddForm">

            <div class="modal-header text-white py-3">
                <h5 class="modal-title text-xl" id="templateAddLabel">Add Template</h5>
            </div>
            <div class="modal-body">
                <div class="p-2">
                <div class="mb-6">
                        <label for="name" class="block text-gray-700 font-bold mb-2">Name <span class="text-red-600">*</span></label>
                        <input required type="text" name="name" id="name" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label for="add_campaign_ids" class="block text-gray-700 font-bold mb-2">Campaigns</label>
                        <select id="add_campaign_ids" name="campaign_ids[]" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" multiple>
                        <?php foreach($campaigns as $campaign): ?>
                            <option value="<?php echo $campaign->id; ?>"><?php echo $campaign->title; ?></option>
                        <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Note: Following placeholders are active: {candidate_name} {campaign_name} {candidate_email} {position_name} {submission_date}</label>
                    </div>
                    
                    <div class="mb-6">
                        <label for="subject" class="block text-gray-700 font-bold mb-2">Subject <span class="text-red-600">*</span></label>
                        <input required type="text" name="subject" id="subject" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="body" class="block text-gray-700 font-bold mb-2">Body</label>
                        <textarea id="body" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
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


<!-- Edit Template Modal -->
<div class="modal fade" id="templateEditModal" tabindex="-1" aria-labelledby="templateEditLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="templateEditForm">

            <div class="modal-header text-white py-3">
                <h5 class="modal-title text-xl" id="templateEditLabel">Edit Template</h5>
            </div>
            <div class="modal-body">
                <div class="p-2">

                    <input required type="hidden" name="templateId" id="edit_id">

                    <div class="mb-6">
                        <label for="edit_name" class="block text-gray-700 font-bold mb-2">Name <span class="text-red-600">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>

                    <div class="mb-6">
                        <label for="edit_campaign_ids" class="block text-gray-700 font-bold mb-2">Campaigns</label>
                        <select id="edit_campaign_ids" name="campaign_ids[]" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" multiple>
                            <?php foreach($campaigns as $campaign): ?>
                                <option value="<?php echo $campaign->id; ?>"><?php echo $campaign->title; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-6">
                        <label for="edit_subject" class="block text-gray-700 font-bold mb-2">Subject <span class="text-red-600">*</span></label>
                        <input type="text" name="subject" id="edit_subject" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500">
                    </div>
                    <div class="mb-6">
                        <label for="edit_body" class="block text-gray-700 font-bold mb-2">Body</label>
                        <textarea id="edit_body" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500"></textarea>
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
