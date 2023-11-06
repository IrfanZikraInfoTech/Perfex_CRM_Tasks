<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers</title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.js"></script> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.isotope/3.0.6/isotope.pkgd.min.js" integrity="sha512-Zq2BOxyhvnRFXu0+WE6ojpZLOU2jdnqbrM1hmVdGzyeCa1DgM3X5Q4A/Is9xA1IkbUeDd7755dNNI/PzSf2Pew==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<style>
    .active {
            transform: scale(1.1, 1.1) !important;
            background: #0284c7 !important;
            color: white !important;
        }
    .no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

</style>
</head>
<body class="bg-gray-100">
<?php
$default_bg_color = "#1da6e7";  // White color
$default_button_color = "#e9b328";  // Grey color

$bg_color = isset($colorScheme['background_color']) ? $colorScheme['background_color'] : $default_bg_color;
$button_color = isset($colorScheme['button_color']) ? $colorScheme['button_color'] : $default_button_color;
?>    
    <div class="container mx-auto md:px-4 md:py-8 p-2">
        <div class="text-center mb-12">

            <a href="https://zikrainfotech.com"><div id="logo" class="bg-layer-1 md:absolute mx-auto md:inset-x-auto inset-x-0 mb-2 xl:w-64 lg:w-48 w-48 md:pl-6 pt-2 z-10">
        <img src="<?= base_url('uploads/company/' . get_option('company_logo')) ?>" class="img-responsive" alt="Zikra Infotech LLC">
            </div></a>

            <h1 class="text-4xl font-semibold mt-6">Careers</h1>

            <div class="flex justify-center mt-6 mr-4">
                <form action="<?= admin_url('recruitment_portal/career') ?>" method="GET" class="flex items-center bg-white rounded-full shadow-lg">
                    <div class="p-2">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-6a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="filter_title" placeholder="Filter by Title" class="w-full py-2 pr-4 pl-2 rounded-full focus:outline-none focus:border-blue-300">
                    <button type="submit" style="background-color: <?= $bg_color ?>;" class="text-white py-2 px-4 rounded-full">
                        Filter
                    </button>
                </form>
            </div>
        </div>
        <?php if(empty($activeCampaigns)){?>
            <h2 class="text-center text-lg text-bold">Opportunities coming soon!</h2>
        <?php } else {?>
            <!-- tags button -->
        <div class="flex flex-wrap gap-4 mb-8 justify-center">
        <span class="inline-block capitalize w-32 text-center bg-white rounded-full px-4 py-2 text-md font-semibold cursor-pointer transition-all ease-in-out duration-300 transform hover:scale-105 tag-filter"
            data-tag="all"
            style="color: <?= $bg_color ?>; border: 1px solid <?= $bg_color ?>;"
            onmouseover="this.style.backgroundColor='<?= $bg_color ?>'; this.style.color='#ffffff';"
            onmouseout="this.style.backgroundColor=''; this.style.color='<?= $bg_color ?>'; this.style.border='1px solid <?= $bg_color ?>';">
            All
        </span>
        <?php foreach($uniqueTags as $tag): ?>
        <span class="inline-block capitalize text-center bg-white rounded-full px-4 py-2 text-md font-semibold cursor-pointer transition-all ease-in-out duration-300 transform hover:scale-105 tag-filter" 
                data-tag="<?= trim($tag) ?>"
                style="color: <?= $bg_color ?>; border: 1px solid <?= $bg_color ?>;"
                onmouseover="this.style.backgroundColor='<?= $bg_color ?>'; this.style.color='#ffffff';"
                onmouseout="this.style.backgroundColor=''; this.style.color='<?= $bg_color ?>'; this.style.border='1px solid <?= $bg_color ?>';">
            <?= trim($tag) ?>
        </span>
        <?php endforeach; ?>
        </div>
            <!-- cards -->
        <div id="isotope-grid" class="grid grid-cols-1 gap-8 md:p-8 p-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-2 2xl:grid-cols-3">
                <?php foreach ($activeCampaigns as $campaign): ?>
            <div class="flex rounded-lg shadow-lg overflow-hidden transform transition-transform duration-500 hover:scale-105 group card" 
                data-tags="<?= $campaign->camp_tag ?>" onmouseover="hoverEffect(this, '<?= $bg_color ?>');"  onmouseout="outEffect(this, '<?= $bg_color ?>');">
                    <a href="<?= base_url("career/view/".$campaign->id) ?>" 
                    class="leftSide w-2/5 p-4 rounded-l-lg transition-all ease-in-out" 
                    style="background-color: <?= $bg_color ?>; border: 1px solid <?= $bg_color ?>; color: #ffffff;"
                    onmouseover="hoverEffect(this.parentElement, '<?= $bg_color ?>');" 
                    onmouseout="outEffect(this.parentElement, '<?= $bg_color ?>');">
                        <h2 class="text-2xl font-bold transition-all ease-in-out">
                            <?php echo $campaign->title; ?>
                        </h2>
                        <p class="text-opacity-70 transition-all ease-in-out">
                            <?php echo $campaign->position; ?>
                        </p>
                    </a>
                <div class="rightSide flex flex-col w-full h-full bg-white">
                    <a href="<?= base_url("career/view/".$campaign->id) ?>" 
                    class="h-full w-3/5 px-6 pt-6 pb-3 bg-white flex flex-col transition-all ease-in-out w-full" 
                    onmouseover="this.style.backgroundColor='#ffffff';" 
                    onmouseout="this.style.backgroundColor='#ffffff';">
                    <?php if (isset($campaign->job_type)): ?>
                        <p class="text-gray-600 group-hover:text-gray-600 mb-2 transition-all ease-in-out"><strong>Type:</strong> <?php echo $campaign->job_type; ?></p>
                    <?php endif; ?>

                    <?php if (isset($campaign->experience)): ?>
                        <p class="text-gray-600 group-hover:text-gray-600 mb-2 transition-all ease-in-out"><strong>Experience:</strong> <?php echo $campaign->experience; ?></p>
                    <?php endif; ?>

                    <?php if (isset($campaign->skills_required)): ?>
                        <p class="text-gray-600 group-hover:text-gray-600 mb-2 transition-all ease-in-out">
                            <strong>Skills:</strong>
                            <?php
                                $skills = explode(',', $campaign->skills_required);
                                $count = count($skills);
                                if ($count > 3) {
                                    echo implode(',', array_slice($skills, 0, 3)) . '... and ' . ($count - 3) . ' more';
                                } else {
                                    echo $campaign->skills_required;
                                }
                            ?>
                        </p>
                    <?php endif; ?>


                    <?php if (isset($campaign->end_date)): ?>
                        <p class="text-gray-600 group-hover:text-gray-600 mb-2 transition-all ease-in-out"><strong>End Date:</strong> <?php echo $campaign->end_date; ?></p>
                    <?php endif; ?>

                    <?php if (isset($campaign->salary)): ?>
                        <p class="text-gray-600 group-hover:text-gray-600 mb-2 transition-all ease-in-out"><strong>Salary:</strong> <?php echo $campaign->salary; ?></p>
                    <?php endif; ?>

                    <?php if (isset($campaign->description)): ?>
                        <p class="text-gray-600 group-hover:text-gray-600 mb-4 transition-all ease-in-out"><?php echo $campaign->description; ?></p>
                    <?php endif; ?>
                    </a>
                    <div class="mt-auto p-4 bg-white">
                        <a href="<?php echo site_url('career/apply/'.$campaign->id); ?>" style="background-color: <?= $button_color ?>;" class="inline-block text-white font-semibold px-6 py-2 rounded transition-colors duration-200 w-full text-center transition-all ease-in-out">Apply</a>
                    </div>

                </div>
            </div>
                <?php endforeach; ?>
        </div>

            
        <?php } ?>
    </div>
 <!-- Begin Footer -->
 <footer class="bg-gray-200 text-gray-800 mt-16 py-2">

<div class="flex flex-wrap justify-center">
        <div class="p-2">Careers | <a href="https://zikrainfotech.com">Zikra Infotech LLC</a></div>
</div>

</footer>
<!-- End Footer -->

<script>


        $(document).ready(function(){
    $('.tag-filter').on('click', function(){
        $('.tag-filter').removeClass('active');
        $(this).addClass('active');
        var selectedTag = $(this).data('tag');

        if (selectedTag === 'all') {
            // If 'All' is selected, show all cards with fade-in effect
            $('.card').fadeIn(400);
        } else {
            // Loop through each card and fade them out initially
            $('.card').each(function(){
                $(this).fadeOut(200);
            });
            
            // After a short delay, execute the tag filtering
            setTimeout(function() {
                $('.card').each(function(){
                    var cardTags = $(this).data('tags').split(',');

                    // Check if the card has the selected tag
                    if ($.inArray(selectedTag, cardTags) !== -1) {
                        // Show the card with a fade-in effect
                        $(this).fadeIn(400);
                    }
                });
            }, 200);  // 200ms delay to sync with fadeOut()
        }
    });
});
function hoverEffect(card, bgColor) {
    const leftSide = card.querySelector('.leftSide');
    leftSide.style.backgroundColor = '#ffffff';
    leftSide.style.color = bgColor;
   
}

function outEffect(card, bgColor) {
    const leftSide = card.querySelector('.leftSide');
    leftSide.style.backgroundColor = bgColor;
    leftSide.style.color = '#ffffff';
}



</script>
</body>
</html>


