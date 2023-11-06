<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="w-full mx-auto">
            <div class="bg-white shadow-md rounded-md p-6">
                <div class="mb-10 w-full flex flex-col">
                    <div class="w-full text-center">
                        <h4 class="text-2xl font-semibold">Color Setting</h4>
                    </div>
                    <div class="w-full flex justify-end mt-2 space-x-4">
                        <a href="<?= admin_url('Recruitment_portal/color'); ?>">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Add Color
                            </button>
                        </a>
                        <a href="<?= admin_url('Recruitment_portal/career'); ?>">
                            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                View Career Page
                            </button>
                        </a>
                    </div>
                </div>
                <table class="min-w-full table table-striped table-bordered">
                <thead>
                        <tr>
                            <th class="py-2 px-4 border-b border-gray-200">Seq No</th>
                            <th class="py-2 px-4 border-b border-gray-200">Background Color</th>
                            <th class="py-2 px-4 border-b border-gray-200">Button Color</th>
                            <th class="py-2 px-4 border-b border-gray-200">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $seqNo = 1;
                        foreach($allColors as $color): 
                        ?>
                            <tr>
                                <td class="py-2 px-4 border-b border-gray-200"><?= $seqNo++ ?></td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    <div class="h-4 w-4 rounded" style="background-color: <?= $color['background_color'] ?>;"></div>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200">
                                    <div class="h-4 w-4 rounded" style="background-color: <?= $color['button_color'] ?>;"></div>
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 flex items-center space-x-2">
                                <a href="<?= admin_url('recruitment_portal/activateColor/' . $color['id']); ?>" 
                                    class="bg-green-500 hover:bg-green-700 hover:text-white text-white font-bold py-1 px-2 rounded shadow-lg <?= $color['activate_color'] == 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                    <?= $color['activate_color'] == 1 ? 'disabled' : '' ?>
                                    >
                                        Activate
                                    </a>
                                    <a href="<?= admin_url('recruitment_portal/deleteColor/' . $color['id']) ?>" 
                                        class="bg-red-500 hover:bg-red-700 hover:text-white text-white font-bold py-1 px-2 rounded shadow-lg <?= $color['activate_color'] == 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                                        <?= $color['activate_color'] == 1 ? 'disabled' : '' ?>
                                        >
                                            Delete
                                        </a>
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