<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper" class="bg-gray-200">
<div class="content">
    <div class="w-full max-w-md mx-auto p-8 rounded-lg bg-white shadow-2xl mt-10">
        <h1 class="text-2xl font-semibold mb-4 text-center text-gray-700">Add Colors</h1>
        <p class="text-center text-gray-500 italic mb-4">Customize the color scheme to match your style.</p>
        <form action="<?= admin_url('recruitment_portal/saveColor') ?>" method="post" class="space-y-6">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">           
            <div class="flex flex-col space-y-2">
                <label for="background_color" class="text-lg font-semibold text-gray-600">Background Color:</label>
                <input type="color" name="background_color" id="background_color" value="<?= isset($colorScheme['background_color']) ? $colorScheme['background_color'] : ' #1da6e7 ' ?>" class="w-full h-10 rounded-md focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="flex flex-col space-y-2">
                <label for="button_color" class="text-lg font-semibold text-gray-600">Button Color:</label>
                <input type="color" name="button_color" id="button_color" value="<?= isset($colorScheme['button_color']) ? $colorScheme['button_color'] : '#e9b328' ?>" class="w-full h-10 rounded-md focus:border-blue-400 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md transition ease-in-out duration-200">
                    Save
                </button>
            </div>
        </form>
    </div>
    <div class="w-full p-8 rounded-lg shadow-2xl mt-10">
    <h1 class="text-2xl font-semibold mb-4 text-center text-gray-700">Color History</h1>
    <table class="min-w-full table table-striped table-bordered">
    <thead class="bg-gray-100">
        <tr class="">
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
            <tr class="hover:bg-gray-50 transition ease-in-out duration-150">
                <td class="py-auto px-4 border-b border-gray-200"><?= $seqNo++ ?></td>
                <td class="py-auto px-4 border-b border-gray-200">
                    <div class="h-6 w-6 rounded-full border border-gray-300" style="background-color: <?= $color['background_color'] ?>;"></div>
                </td>
                <td class="py-auto px-4 border-b border-gray-200">
                    <div class="h-6 w-6 rounded-full border border-gray-300" style="background-color: <?= $color['button_color'] ?>;"></div>
                </td>
                <td class="py-auto px-4 border-b border-gray-200 flex items-center space-x-2">
                    <a href="<?= admin_url('recruitment_portal/activateColor/' . $color['id']); ?>" 
                       class="bg-green-500 hover:bg-green-600 text-white font-bold py-1 px-2 rounded-full transition ease-in-out duration-200 <?= $color['activate_color'] == 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
                       <?= $color['activate_color'] == 1 ? 'disabled' : '' ?>
                       >
                        Activate
                    </a>
                    <a href="<?= admin_url('recruitment_portal/deleteColor/' . $color['id']) ?>" 
                       class="bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-2 rounded-full transition ease-in-out duration-200 <?= $color['activate_color'] == 1 ? 'opacity-50 cursor-not-allowed' : '' ?>"
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