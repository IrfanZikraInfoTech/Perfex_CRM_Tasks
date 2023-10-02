<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col">

        <div class="bg-white flex flex-col gap-4 rounded">
            <h2 class="text-xl text-center py-2">All Staff Members and Google Chat User IDs</h2>
            <div class="align-middle inline-block min-w-full mt-4">
                <form id="google-chat-form">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.No </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff ID</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Google Chat User ID</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                    <?php foreach ($staff as $s): ?>
                    <tr class="hover:bg-gray-200/30 transition-all">
                    <td class="align-middle px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $s['staffid']; ?></td>
                    <td class="px-6 py-4  text-sm text-gray-500">
                        <div class="flex items-center">
                            <div class="">
                                <?= staff_profile_image($s['staffid'], ['border-2 border-solid object-cover w-12 h-12 staff-profile-image-thumb'], 'thumb'); ?>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo $s['firstname'] . ' ' . $s['lastname']; ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle px-6 py-4 whitespace-nowrap text-sm text-gray-500">

                        <input type="text" class="px-4 py-2 google-chat-user-id" id="google_chat_user_id_<?= $s['staffid'] ?>" name="google_chat_user_id[]" data-staff-id="<?= $s['staffid'] ?>" value="<?= $s['google_chat_user_id'] !== NULL ? $s['google_chat_user_id'] : '' ?>" placeholder="Enter Google Chat ID">

                    </td>
                    </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>
                <input class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded transition-all float-right my-10 mr-10" type="button" id="update-button" value="Update All">
                </form>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
    
$(document).ready(function() {
    $('#update-button').click(function(e) {
        e.preventDefault(); // Prevent form from being submitted normally

        var googleChatUserIds = $('.google-chat-user-id');
        var data = {};

        googleChatUserIds.each(function() {
            var staffId = $(this).data('staff-id');
            var googleChatUserId = $(this).val();
            data[staffId] = googleChatUserId;
        });

        $.ajax({
            url: `<?= admin_url("team_management/update_google_chat_id") ?>`, // Use the correct URL for your controller method
            method: 'POST',
            data: data,
            success: function(response) {
                alert('Data successfully updated');
                // Handle response here
            },
            error: function(response) {
                alert('An error occurred');
                // Handle error here
            }
        });
    });
});


</script>

</body>
</html>
