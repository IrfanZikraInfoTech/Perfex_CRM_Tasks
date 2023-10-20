<?php
if(isset($campaign) && $campaign->status == 0){
    header("Location: ".base_url("career"));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $campaign->title ?></title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php
$default_bg_color = "#1da6e7";  // White color
$default_button_color = "#e9b328";  // Grey color

$bg_color = isset($colorScheme['background_color']) ? $colorScheme['background_color'] : $default_bg_color;
$button_color = isset($colorScheme['button_color']) ? $colorScheme['button_color'] : $default_button_color;

?> 
    <div class="container mx-auto px-4 pb-8 pt-4">
        <div class="text-center mb-12">
            <!-- Replace 'your_logo.png' with your company's logo file -->
            <a href="https://zikrainfotech.com"><div id="logo" class="bg-layer-1 md:absolute mx-auto md:inset-x-auto inset-x-0 mb-2 xl:w-64 lg:w-48 w-48 md:pl-6 pt-2 z-10">
        <img src="<?= base_url('uploads/company/' . get_option('company_logo')) ?>" class="img-responsive" alt="Zikra Infotech LLC">
            </div></a>
            <!-- <a href="<?= base_url("career") ?>"><img src="https://ansara23.sg-host.com/wp-content/uploads/2023/07/zikra-infotech-logo-2-2048x706.png" alt="Company Logo" class="mx-auto mb-4 w-64"></a> -->
            <h1 class="text-3xl font-semibold mb-2 mt-4"><?php echo isset($campaign->title) ? $campaign->title : 'Job Title'; ?></h1>
            <h3 class="text-lg text-gray-600 mb-2"><?php echo isset($campaign->position) ? $campaign->position : 'Job Position'; ?></h3>
            <p class="text-gray-400 text-sm mt-3 capitalize"><?php echo isset($campaign->job_type) ? str_replace('_', ' ', $campaign->job_type) : 'Job Type'; ?></p>
        </div>

        <div class="flex flex-col md:flex-row md:space-x-6">
            <!-- Left Side -->
            <div class="w-full md:w-1/3 h-64 sticky top-4">
                <!-- Job Information -->
                <div class="bg-white rounded-lg p-6  shadow-lg">
                    <h3 class="text-lg text-gray-900 mb-3">Job Information</h3>
                    <p class="text-md text-gray-700 mb-2"><strong>Start Date:</strong> <?php echo isset($campaign->start_date) ? date('jS F, Y', strtotime($campaign->start_date)) : 'Not Specified'; ?></p>
                    <p class="text-md text-gray-700 mb-2"><strong>End Date:</strong> <?php echo (isset($campaign->end_date) && $campaign->end_date != '0000-00-00') ? date('jS F, Y', strtotime($campaign->end_date)) : 'Not Specified'; ?></p>
                    <p class="text-md text-gray-700 mb-2"><strong>Salary:</strong> <?php echo isset($campaign->salary) ? $campaign->salary : 'Not Specified'; ?></p>
                    <p class="text-md text-gray-700 mb-2"><strong>Experience:</strong> <?php echo isset($campaign->experience) ? $campaign->experience : 'Not Specified'; ?></p>
                    <!-- Apply Now Button -->
                    <div class="mt-6">
                        <a href="<?php echo site_url('career/apply/'.(isset($campaign->id) ? $campaign->id : '')); ?>" class="inline-block text-white font-semibold px-6 py-3 rounded transition-colors duration-200 w-full text-center" style="background-color: <?php echo $button_color; ?>; hover:bg-<?php echo $button_color; ?>">Apply Now</a>
                    </div>
                </div>
            </div>
            <!-- Right Side -->
            <div class="w-full md:w-2/3">
                <!-- Job Description -->
                <div class="bg-white rounded-lg p-6 shadow-sm mb-6 mt-10 md:mt-0">
                    <h3 class="text-lg text-gray-900 mb-3">Job Description</h3>
                    <p class="text-md leading-relaxed text-gray-700"><?php echo isset($campaign->detailed_description) ? $campaign->detailed_description : 'No job description provided.'; ?></p>
                </div>
                <!-- Skills Required -->
                <div class="bg-white rounded-lg p-6 shadow-sm">
                    <h3 class="text-lg text-gray-900 mb-3">Skills Required</h3>
                    <p class="text-md leading-relaxed text-gray-700"><?php echo isset($campaign->skills_required) ? $campaign->skills_required : 'No specific skills required.'; ?></p>
                </div>
            </div>
        </div>
    </div>
</body>


</html>
