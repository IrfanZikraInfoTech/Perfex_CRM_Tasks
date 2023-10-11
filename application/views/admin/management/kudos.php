<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
    .bootstrap-select .btn{
        border:none
    }
    .bootstrap-select .btn:hover{
        border:none;
        border-radius:20px;
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    /* Hide scrollbar for IE and Edge */
    .scrollbar-hide {
        -ms-overflow-style: none;
    }
    .points{
        margin:5px
    }

</style>

<div id="wrapper">


    <body class="bg-gray-100 py-20">
        <!-- Main Heading -->
        <h1 class="text-3xl font-bold text-center p-10">Recognize and Appreciate Your Team Members</h1>
        <div class="container mx-auto">
            <!-- Grid -->
            <div class="grid grid-cols-3 gap-6">
                <!-- Left 2/3 Section -->
                <div class="col-span-2 p-8 bg-yellow-200 rounded-[50px] shadow-lg m-3">
                    <div class="mb-4 bg-yellow-200 text-center py-2 rounded-[40px]">
                        <h2 class="text-xl font-bold">KUDOS LEFT: <?= $remaining_kudos ?>/5</h2>
                    </div>
                    <div class="space-y-4">
                        <form id="kudosform">
                            <div class="form-group select-placeholder">
                                <label class="block text-lg font-medium bg-yellow-200" for="kudosType">Type of Kudos:</label>
                                <select class="selectpicker bg-white rounded-[20px]" data-width="100%" name="kudosType" id="kudosType">
                                    <option value="advice">Advice</option>
                                    <option value="kudos">Kudos</option>
                                </select>
                            </div>  
                            <div class="form-group select-placeholder">
                                <label class="block text-lg font-medium bg-yellow-200" for="to_">To:</label>
                                <select class="selectpicker bg-white rounded-[20px]"
                                        data-live-search="true"
                                        data-none-selected-text="<?php echo _l('system_default_string'); ?>"
                                        data-width="100%"
                                        name="to_"
                                        id="to_">
                                    <?php foreach($staff_members as $staff): ?>
                                        <option value="<?php echo $staff['staffid']; ?>">
                                            <?php echo $staff['firstname'] . ' ' . $staff['lastname']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mt-4 select-placeholder">
                                <label class="block text-lg font-medium bg-yellow-200" for="principles">Select Principles (one or more):</label>
                                <select class="selectpicker bg-white rounded-[20px]" data-width="100%" name="principles[]" id="principles" multiple>
                                    <option value="integrity">Integrity</option>
                                    <option value="teamwork">Teamwork</option>
                                    <option value="innovation">Innovation</option>
                                    <option value="customerFocus">Customer Focus</option>
                                    <option value="accountability">Accountability</option>
                                    <option value="excellence">Excellence</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-lg font-medium bg-yellow-200">Remarks:</label>
                                <textarea id="remarks" rows="4" class=" rounded-[20px] mt-1 mb-5 p-2 w-full border rounded focus:outline-none focus:border-blue-500"></textarea>
                            </div>
                            <button type="submit" id="btnform" class="rounded-[20px] w-full bg-gradient-to-r from-blue-700 to-blue-500 text-white font-semibold px-6 py-3 shadow-md hover:from-blue-600 hover:to-indigo-700 hover:shadow-lg transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:scale-95">
                                Submit
                            </button>                
                        </form>
                    </div>
                </div>


                <!-- Right 1/3 Section -->
                <div class="col-span-1 row-span-1 p-5 bg-white rounded-[50px] shadow-lg space-y-6 my-3 mr-3">
                    <!-- Receivers -->
                    <div>
                        <h2 class="text-xl font-semibold mb-4 text-center border-b p-3 overflow-x">Leaderboard - Receivers</h2>
                        <?php 
                        $colors = ["bg-blue-600", "bg-blue-500", "bg-blue-400", "bg-blue-300", "bg-blue-200", "bg-blue-100"];
                        ?>

                        <ul class="space-y-2 mt-2 overflow-y-auto scrollbar-hide h-[480px] rounded-[20px]">
                            <?php 
                            $counter = 0;
                            foreach($top_receivers as $receiver): 
                                $staff = $this->staff_model->get($receiver['to_']); // Fetch the staff data. Modify this according to your setup.
                                $color = isset($colors[$counter]) ? $colors[$counter] : end($colors); // Use a default color if our counter exceeds the colors array length
                            ?>
                            <li class="flex items-center justify-between <?= $color ?> p-4 px-6 text-xl text-white rounded-[35px] shadow-md points">
                                <div class="rounded-full bg-blue-100 w-10 h-10">
                                    <?= staff_profile_image($receiver['to_'], ['w-10 h-10 rounded-full'], 'thumb') ?>
                                </div>
                                <span><?= $receiver['total_received'] ?> Points</span>
                            </li>
                            <?php 
                            $counter++;
                            endforeach; 
                            ?>
                        </ul>
                    </div>
                </div>


                <div class="col-span-2 m-3 mt-5 p-6 bg-yellow-200 rounded-[50px] shadow-lg">
                    <!-- Additional Div for filters and kudos feed -->
                    <div class="p-8 m-2 bg-gray-100 rounded-[40px] shadow-inner">
                        
                        <!-- Filters/Sorting -->
                        <div class="mb-6 flex justify-between items-center">
                            <h1 class="text-xl font-bold pl-4">All Post</h1>
                            <div class="relative inline-flex">
                                <svg class="w-2 h-2 absolute top-0 right-0 m-4 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 412 232"><path d="M206 171.144L42.678 7.822c-9.763-9.763-25.592-9.763-35.355 0C-2.09 17.585-2.09 33.404 7.323 42.678L193.677 229.09c9.763 9.763 25.592 9.763 35.355 0L404.677 42.678c9.763-9.763 9.763-25.592 0-35.355s-25.592-9.763-35.355 0L206 171.144z" fill="#648299" fill-rule="nonzero"/></svg>
                                <select id="filterselect" class=" border border-gray-300 rounded-[20px] text-gray-600 h-10 pl-5 pr-10 bg-white hover:border-gray-400 focus:outline-none appearance-none">
                                    <option>All</option>
                                    <option>Given</option>
                                    <option>Received</option>
                                </select>
                            </div>
                        </div>
                                                
                        <!-- Kudos Feed (Example) -->
                        <div id="kudosfeed" class="space-y-6 data-block overflow-y-auto scrollbar-hide h-[400px] rounded-[30px]">
                            <!-- Single Kudos -->
                            <?php foreach($kudos_data as $kudos): 
                                  $liked_by_current_user = in_array($this->session->userdata('staff_user_id'), explode(',', $kudos['kudos_like']));
                                  $liked_staff_ids = explode(',', $kudos['kudos_like']);
                                  $currentDateTime = new DateTime();
                                  $postDateTime = new DateTime($kudos["created_at"]);
                                  $interval = $currentDateTime->diff($postDateTime);
                                        
                                        $timeString = '';
                                        if ($interval->y > 0) {
                                            $timeString = $interval->y . ' year' . ($interval->y > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->m > 0) {
                                            $timeString = $interval->m . ' month' . ($interval->m > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->d > 0) {
                                            $timeString = $interval->d . ' day' . ($interval->d > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->h > 0) {
                                            $timeString = $interval->h . ' hour' . ($interval->h > 1 ? 's' : '') . ' ago';
                                        } elseif ($interval->i > 0) {
                                            $timeString = $interval->i . ' minute' . ($interval->i > 1 ? 's' : '') . ' ago';
                                        } else {
                                            $timeString = 'just now';
                                        }
                                        ?>
                                        <div class="p-8 bg-white rounded-[30px] shadow-lg mb-5" data-type="<?php // ... ?>">
                                            <div class="flex items-center justify-between mb-2">
                                                <div class="flex items-center space-x-3">
                                                    <div class="w-10 h-10 rounded-full bg-gray-300">
                                                        <?= staff_profile_image($kudos['staff_id'], ['w-10 h-10 rounded-full'], 'thumb') ?>
                                                    </div>
                                                    <div>
                                                        <span class="font-bold block">
                                                            <?php echo $kudos['firstname'] . ' ' . $kudos['lastname']; ?>
                                                        </span>
                                                        <span class="text-sm">
                                                            gave <?php echo $kudos['type'] ?> to <b><?php echo $staff_id_name[$kudos['to_']] ?></b>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="font-medium text-xs text-gray-600">
                                                        <?php echo $timeString; ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <h2 class="mt-8 mb-2 text-md"><b>Principles:</b>
                                                <?php 
                                                    $principles = explode(',', $kudos['principles']);
                                                    $capitalizedPrinciples = array_map('ucwords', $principles);
                                                    echo implode(', ', $capitalizedPrinciples);
                                                ?>
                                            </h2>

                                            <p class="mb-5"><b>Remarks:</b> <?php echo $kudos['remarks']; ?></p>

                                            <div class="flex items-center justify-between space-x-2">
                                        <div>
                                            <button class="focus:outline-none like-btn" data-kudos-id="<?php echo $kudos['id']; // Assuming 'id' is the column name for kudos unique ID ?>">
                                                <svg class="h-6 w-6 heart-icon" fill="<?php echo $liked_by_current_user ? '#CF3333' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="flex profile-images" style="width:100px;"> <!-- adjust width as required -->
                                            <!-- Images of people who liked this kudos -->
                                            <?php foreach($liked_staff_ids as $staff_id): 
                                                if(!empty($staff_id)): ?>
                                                    <div class="w-6 h-6 rounded-full overflow-x-auto scrollbar-hide">
                                                        <?= staff_profile_image($staff_id, ['w-6 h-6 rounded-full'], 'thumb') ?>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-span-1 my-3 mr-3 row-span-1 p-5 bg-white rounded-[50px] shadow-lg space-y-6 s">
                    <!-- Givers -->
                    <div>
                        <h2 class="text-xl font-semibold text-center border p-3">Leaderboard - Givers</h2>
                        <?php 
                        $colors = ["bg-blue-600", "bg-blue-500", "bg-blue-400", "bg-blue-300", "bg-blue-200", "bg-blue-100"];
                        ?>
                        <ul class="space-y-2 mt-2 overflow-y-auto scrollbar-hide h-[480px] rounded-[20px]">
                            <?php 
                            $counter = 0;
                            foreach($top_givers as $giver): 
                                $staff = $this->staff_model->get($giver['staff_id']); 
                                $color = isset($colors[$counter]) ? $colors[$counter] : end($colors);
                            ?>
                            <li class="flex items-center justify-between <?= $color ?>  p-4 px-6 text-xl text-white rounded-[35px] shadow-md points">
                                <div class="rounded-full bg-blue-100 w-10 h-10">
                                    <?= staff_profile_image($giver['staff_id'], ['w-10 h-10 rounded-full'], 'thumb') ?>
                                </div>
                                <span><?= $giver['total_kudos'] ?> Points</span>
                            </li>
                            <?php $counter++;
                             endforeach; ?>
                        </ul>
                    </div>
                </div>
    </div>    
</div>


<?php init_tail(); 
?>

<script>
$("#kudosform").on('submit', function(event){
    event.preventDefault();

    var remainingKudos = parseInt($(".text-xl").text().split(':')[1].trim(), 10); // Extract the remaining kudos from the text

    if (remainingKudos <= 0) {
        Swal.fire({
            icon: 'info',
            title: 'Info',
            text: 'You have no kudos left to give!'
        });
        return; // This will exit the function, preventing any further code from executing
    }

    var postData = {
        type: $("#kudosType").val(),
        to_: $("#to_").val(),
        principles: $("#principles").val(),
        remarks: $("#remarks").val()
    };

    $.ajax({
        url: 'save_kudos_data',
        method: 'POST',
        dataType: 'json',
        data: postData,
        success: function(response) {
            if (response.success) {
                var currentKudos = parseInt($('#kudosCount').text(), 10);
                $('#kudosCount').text(currentKudos - 1);
                if (currentKudos === 1) { // Check if after decrementing it becomes 0
                    $('#btnform').prop('disabled', true);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Kudos saved successfully!'
                });
                addKudosToFeed(response);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.message // Display the message returned from server
                });
            }
        }
    });
});

    function addKudosToFeed(data) {
    let kudosBlock = `
    <div class="p-8 bg-white rounded-[30px] shadow-lg mb-5">
        <div class="flex items-start justify-between mb-2">
            <!-- Left side (profile image, name, kudos info) -->
            <div class="flex items-start space-x-3">
                <div class="w-10 h-10 rounded-full bg-gray-300">
                    <img src="${data.image_url}" alt="${data.name}" class="w-10 h-10 rounded-full">
                </div>
                <div>
                    <span class="font-bold block">${data.name}</span>
                    <span class="text-sm">gave ${data.kudosType} to <b>${data.to_name}</b></span>
                </div>
            </div>
            
            <!-- Right side (timestamp) -->
            <div class="text-right self-start">
                <span class="font-medium text-xs text-gray-600">${data.timestamp}</span>
            </div>
        </div>

        <!-- Principles -->
        <h2 class="mt-8 mb-2 text-md"><b>Principles:</b> ${data.principles}</h2>

        <!-- Remarks -->
        <p class="mb-5"><b>Remarks:</b> ${data.remarks}</p>

        <!-- Like button -->
        <div class="flex items-center justify-start">
            <button class="focus:outline-none like-btn">
                <svg class="h-6 w-6 heart-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>
        </div>
    </div>
`;

    // Prepend the new kudos block to the feed (so it appears at the top)
    $('#kudosfeed').prepend(kudosBlock);
    }

    $(document).on('click', '.like-btn', function() {
    var kudosId = $(this).data('kudos-id');
    var btn = $(this);
    var icon = btn.find('.heart-icon');
    var profileImagesDiv = btn.closest('div').next('.profile-images'); // closest profile images div to our like button

    $.ajax({
        url: 'like_kudos', 
        method: 'POST',
        dataType: 'json',
        data: {kudos_id: kudosId},
        success: function(response) {
            if(response.success) {
                if(response.action === 'liked') {
                    icon.css({
                        'fill': '#CF3333',
                        'stroke': '#CF3333'
                    });
                    var newImageHtml = `<div class="w-6 h-6 rounded-full overflow-x-auto scrollbar-hide">
                                            <img src="${response.image_url}" class="w-6 h-6 rounded-full">
                                        </div>`;
                    profileImagesDiv.append(newImageHtml);
                } else if(response.action === 'unliked') {
                    icon.css({
                        'fill': 'none',
                        'stroke': 'currentColor'
                    });
                    // Assuming each div inside profile-images corresponds to a staff's image
                    profileImagesDiv.find('div').each(function() {
                        var imageUrl = $(this).find('img').attr('src');
                        if(imageUrl === response.image_url) {
                            $(this).remove();
                            return false; // Break out of the each loop once the image is removed
                        }
                    });
                }
            } else {
                alert('Something went wrong!');
            }
        }
    });
});


    $(document).ready(function() {
    $("#filterselect").on("change", function() {
        var filterType = $(this).val().toLowerCase();

        if (filterType === "all") {
            $(".data-block > div").show(); // Show all posts
        } else {
            $(".data-block > div").each(function() {
                if ($(this).data('type') === filterType) {
                    $(this).show(); // Show the post that matches the filter
                } else {
                    $(this).hide(); // Hide the posts that don't match the filter
                }
            });
        }
    });
});

</script>