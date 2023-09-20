<!-- Action Template Modal -->
<div class="modal fade" id="templateActionModal" tabindex="-1" aria-labelledby="templateActionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="templateActionForm">

            <div class="modal-header text-white py-3">
                <h5 class="modal-title text-xl" id="templateActionLabel"></h5>
            </div>
            <div class="modal-body">
                <div class="p-2">

                    <div class="mb-6">
                        <label for="Action_campaign_ids" class="block text-gray-700 font-bold mb-2">Templates</label>
                        <select id="Action_campaign_ids" class="form-input border border-gray-400 w-full py-2 px-3 rounded-lg transition duration-500 ease-in-out focus:outline-none focus:shadow-outline focus:border-blue-500" onchange="fillEmailDetails(this.value)">
                        <option disabled selected>Select Template</option>
                        <?php foreach($templates as $template): ?>
                            <option value="<?php echo $template->template_id; ?>"><?php echo $template->template_name; ?></option>
                        <?php endforeach; ?>
                        </select>
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
                    <button type="button" id="templateActionSubmit" onclick="changeStatus()" class="btn btn-primary">Action</button>
                </div>
            </div>

            </form>
            
        </div>
    </div>
</div>
