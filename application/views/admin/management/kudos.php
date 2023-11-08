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
        <h1 class="text-3xl font-bold text-center p-10">Kudos</h1>
        <div class="container mx-auto">
            <!-- Grid -->
            <div class="flex lg:flex-row flex-col  gap-6">

                <div class="lg:w-2/3 w-full flex flex-col gap-5">

                    <!-- Left 2/3 Section -->
                    <div class="w-full lg:p-8 p-2 bg-<?= get_option('management_theme_background')?> rounded-[50px] shadow-lg">
                        <div class="mb-4 bg-<?= get_option('management_theme_background')?> text-center py-2 rounded-[40px]">
                            <h2 class="text-xl font-bold">KUDOS LEFT: <?= $remaining_kudos ?>/5</h2>
                        </div>
                        <div class="space-y-4">
                            <form id="kudosform">
                                <div class="form-group select-placeholder">
                                    <label class="block text-lg font-medium bg-<?= get_option('management_theme_background')?>" for="kudosType">Type of Kudos:</label>
                                    <select class="selectpicker bg-white rounded-[20px]" data-width="100%" name="kudosType" id="kudosType">
                                        <option value="advice">Advice</option>
                                        <option value="kudos">Kudos</option>
                                    </select>
                                </div>  
                                <div class="form-group select-placeholder">
                                    <label class="block text-lg font-medium bg-<?= get_option('management_theme_background')?>" for="to_">To:</label>
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
                                    <label class="block text-lg font-medium bg-<?= get_option('management_theme_background')?>" for="principles">Select Principles (one or more):</label>
                                    <select class="selectpicker bg-white rounded-[20px]" data-width="100%" name="principles[]" id="principles" multiple>    
                                        <?= 
                                        $principles = explode(',',get_option('company_principles'));
                                        foreach($principles as $principle){
                                            echo '<option value="'.htmlspecialchars($principle).'">'.htmlspecialchars($principle).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-5">
                                    <label class="block text-lg font-medium bg-<?= get_option('management_theme_background')?>">Remarks:</label>
                                    <textarea id="remarks" rows="4" class="rounded-[20px] p-4 w-full border rounded focus:outline-none focus:border-blue-500"></textarea>
                                </div>
                                <button type="submit" id="btnform" class="rounded-[20px] w-full bg-gradient-to-r from-blue-700 to-blue-500 text-white font-semibold px-6 py-3 shadow-md hover:from-blue-600 hover:to-indigo-700 hover:shadow-lg transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:scale-95">
                                    Submit
                                </button>                
                            </form>
                        </div>
                    </div>

                    <div class="w-full lg:p-8 p-2 mb-8 bg-<?= get_option('management_theme_background')?> rounded-[50px] shadow-lg">
                        <!-- Additional Div for filters and kudos feed -->
                        <div class="lg:p-8 p-4 py-4 m-2 bg-gray-100 rounded-[40px] shadow-inner">
                            
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
                            <div id="kudosfeed" class="space-y-6 data-block overflow-y-auto scrollbar-hide h-[800px] rounded-[30px]">
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

                                            if($kudos['seen_by']){
                                                $seen_staff_ids = array_unique(explode(',', $kudos['seen_by']));
                                                if(!is_numeric($seen_staff_ids[0])){
                                                    unset($seen_staff_ids[0]);
                                                }
                                            }else{
                                                $seen_staff_ids = [];
                                            }

                                            
                                            ?>
                                            <div class="lg:p-8 p-4 bg-white rounded-[30px] shadow-lg mb-5"   data-type="<?php 
                                                if($kudos['staff_id'] == $this->session->userdata('staff_user_id')) {
                                                    echo 'given'; 
                                                } else if(in_array($this->session->userdata('staff_user_id'), explode(',', $kudos['to_']))) {
                                                    echo 'received';
                                                }
                                                ?>">       
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
                                                            <?php echo $timeString; ?>, <?= 'Seen by '.(count($seen_staff_ids)); ?>
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

                                                <p class="mb-5"><?php echo $kudos['remarks']; ?></p>

                                                <div class="flex items-center justify-between space-x-2 mt-4">
                                            <div class="">
                                                <button class="focus:outline-none like-btn" data-kudos-id="<?php echo $kudos['id']; // Assuming 'id' is the column name for kudos unique ID ?>">
                                                    <svg class="h-6 w-6 heart-icon" fill="<?php echo $liked_by_current_user ? '#CF3333' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="flex profile-images overflow-x-auto scrollbar-hide gap-x-2"> <!-- adjust width as required -->
                                                <!-- Images of people who liked this kudos -->
                                                <?php foreach($liked_staff_ids as $staff_id): 
                                                    if(!empty($staff_id)): ?>
                                                            <?= staff_profile_image($staff_id, ['w-10 h-10 rounded-full border border-none'], 'thumb') ?>      
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="lg:w-1/3 w-full flex flex-col gap-5">

                    <!-- Right 1/3 Section -->
                    <div class="lg:p-5 p-2 bg-white rounded-[50px] shadow-lg space-y-6">
                        <!-- Receivers -->
                        <div>
                            <h2 class="text-xl font-semibold mb-4 text-center border-b p-3 overflow-x">Leaderboard - Recievers</h2>
                            <?php 
                            $colors = ["bg-yellow-400", "bg-yellow-300", "bg-yellow-200", "bg-yellow-100", "bg-yellow-100/50", "bg-white"];
                            
                            $borders = ["border-yellow-400 hover:border-yellow-400", "border-yellow-300 hover:border-yellow-400", "border-yellow-200 hover:border-yellow-400", "border-yellow-100 hover:border-yellow-400", "border-yellow-100/50 hover:border-yellow-400"];

                            ?>

                            <ul class="flex flex-col gap-2 pb-4 mt-2 overflow-y-auto scrollbar-hide h-[480px] rounded-[20px]">
                                <?php 
                                $counter = 0;
                                foreach($top_receivers as $receiver):
                                    

                                    $staff = $this->staff_model->get($receiver['to_']); // Fetch the staff data. Modify this according to your setup.

                                    if(!$staff){
                                        continue;
                                    }

                                    $color = isset($colors[$counter]) ? $colors[$counter] : end($colors); 
                                    $border = isset($borders[$counter]) ? $borders[$counter] : end($borders);
                                ?>
                                <li class="flex items-center justify-between transition-all <?= $color ?> hover:bg-white border-2 border-solid <?= $border ?> p-4 px-6 text-lg text-gray-800 rounded-[35px] shadow-md points">
                                    <div class="flex flex-row gap-4 items-center">
                                        
                                        <button class="rounded-full bg-blue-100 w-10 h-10" title="<?= $staff->full_name ?>" data-toggle="tooltip" data-placement="top">
                                            <?= staff_profile_image($receiver['to_'], ['w-10 h-10 rounded-full'], 'thumb') ?>
                                        </button>
                                        <div class="xl:!block hidden">
                                            <?= $staff->full_name ?>
                                        </div>
                                    </div>
                                    <span><?= $receiver['total_received'] ?> recieved</span>
                                </li>
                                <?php 
                                $counter++;
                                endforeach; 
                                ?>
                            </ul>
                        </div>
                    </div>

                    <div class="lg:p-5 p-2 bg-white rounded-[50px] shadow-lg space-y-6 s">
                        <!-- Givers -->
                        <div>
                            <h2 class="text-xl font-semibold text-center border p-3">Leaderboard - Givers</h2>
                            <?php 

                            $colors = ["bg-yellow-400", "bg-yellow-300", "bg-yellow-200", "bg-yellow-100", "bg-yellow-100/50", "bg-white"];
                            
                            $borders = ["border-yellow-400 hover:border-yellow-400", "border-yellow-300 hover:border-yellow-400", "border-yellow-200 hover:border-yellow-400", "border-yellow-100 hover:border-yellow-400", "border-yellow-100/50 hover:border-yellow-400"];

                            ?>
                            <ul class="space-y-2 mt-2 overflow-y-auto scrollbar-hide h-[480px] rounded-[20px]">
                                <?php 
                                $counter = 0;
                                foreach($top_givers as $giver): 
                                    $staff = $this->staff_model->get($giver['staff_id']); 
                                    $color = isset($colors[$counter]) ? $colors[$counter] : end($colors);
                                    $border = isset($borders[$counter]) ? $borders[$counter] : end($borders);
                                ?>
                                <li class="flex items-center justify-between transition-all <?= $color ?> hover:bg-white border-2 border-solid <?= $border ?> p-4 px-6 text-xl text-gray-800 rounded-[35px] shadow-md points">

                                    <div class="flex flex-row gap-4 items-center">
                                        
                                        <button class="rounded-full bg-blue-100 w-10 h-10" title="<?= $staff->full_name ?>" data-toggle="tooltip" data-placement="top">
                                            <?= staff_profile_image($giver['staff_id'], ['w-10 h-10 rounded-full'], 'thumb') ?>
                                        </button>
                                        <div class="xl:!block hidden">
                                            <?= $staff->full_name ?>
                                        </div>
                                    </div>
                                    <span><?= $giver['total_kudos'] ?> given</span>
                                </li>
                                <?php $counter++;
                                endforeach; ?>
                            </ul>
                        </div>
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
// remainingKudos = 3;
        if (remainingKudos <= 0) {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'You have no kudos left to give!'
            });
            return; // This will exit the function, preventing any further code from executing
        }

        var editorContent = tinyMCE.get('remarks').getContent();
        if (editorContent == '')
        {
            Swal.fire({
                icon: 'info',
                title: 'Info',
                text: 'Give some remarks please!'
            });
            return; // This will exit the function, preventing any further code from executing
        }
        if ($("#principles").val() == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Forgetting something?',
                text: 'Make sure to select atleast one principle!'
            });
            return; // This will exit the function, preventing any further code from executing
        }

        var postData = {
            type: $("#kudosType").val(),
            to_: $("#to_").val(),
            principles: $("#principles").val(),
            remarks: tinymce.get('remarks').getContent()
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
                    }).then((result) => {
                        location.reload();
                    });

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

function getContent(){
    console.log(tinymce.get("remarks").getContent());
}

tinymce.init({
  selector: '#remarks',
  plugins: "textcolor",
  toolbar: "forecolor backcolor preview ",
  height: '300px'
});

</script>