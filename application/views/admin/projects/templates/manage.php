<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">


    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="_buttons tw-mb-2 sm:tw-mb-4">

                <div class="flex flex-row justify-between">
                    <a href="<?php echo admin_url('projects/templates/add'); ?>"
                        class="btn btn-primary pull-left display-block mright5">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo _l('new_project'); ?> Template
                    </a>
                    <button onclick="window.location.href='<?= admin_url('projects') ?>'" class="bg-gray-500 text-white rounded-md px-7 py-2 transition-all hover:bg-gray-600 ease-in-out duration-300">Back</button>
                </div>
                    <div class="clearfix"></div>
                </div>

                <div class="panel_s tw-mt-2 sm:tw-mt-4">
                    <div class="panel-body">

                        <h2 class="text-lg font-semibold"> All Templates </h2>
                        
                        <hr class="hr-panel-separator" />
                        <div class="panel-table-full">
                        <table class="min-w-full bg-white border-collapse border border-slate-500" id="template_table">
                            <thead>
                                <tr class="w-full border-gray-300 border-b border-solid">
                                    <th class="px-4 py-2">Template Name</th>
                                    <th class="px-4 py-2">Date Created</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="p-2">
                                <?php foreach ($templates as $template): ?>
                                <tr class="">
                                    <td class="py-2 px-4 border border-solid border-gray-200"><?= htmlspecialchars($template->name) ?></td>
                                    <td class="py-2 px-4 border border-solid border-gray-200"><?= htmlspecialchars($template->created_at) ?></td>
                                    <td class="py-2 px-4 border border-solid border-gray-200 flex flex-row justify-end gap-4">
                                        <!-- Actions like edit, delete, etc. -->
                                        <button onclick="window.location.href='<?php echo admin_url('projects/templates/edit/'.$template->id); ?>'" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Edit</button>
                                        <button onclick="var result = confirm('Want to delete?');if(result){window.location.href='<?php echo admin_url('projects/templates/delete/'.$template->id); ?>'}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Delete</button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.dataTables_paginate {padding-top:15px !important;}
</style>

<?php init_tail(); ?>

<script>
$("#template_table").DataTable({
    initComplete: function() {
        $('#template_table_wrapper').removeClass('table-loading');
    },
});
</script>
</body>

</html>