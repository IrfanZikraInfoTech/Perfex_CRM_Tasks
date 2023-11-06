<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="w-full mx-auto">
            <div class="bg-white shadow-md rounded-md p-6">
                <div class="mb-10 w-full flex justify-between">
                    <h4 class="text-2xl font-semibold mb-4">Email Templates</h4>
                    <button data-toggle="modal" data-target="#templateAddModal" class="bg-blue-500 hover:bg-blue-700 text-white font-bold px-4 rounded">
                        Add New Template
                    </button>
                </div>
                <table id="templates_table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>
                                Name
                            </th>
                            <th>
                                Subject
                            </th>
                            <th>
                                Body
                            </th>
                            <th>
                                Actions
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<?php include('modals.php'); ?>
<?php include('script.php'); ?>

</body>
</html>
