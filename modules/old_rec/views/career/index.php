<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Careers</title>
    <!-- Include Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <!-- Replace 'your_logo.png' with your company's logo file -->
            <img src="https://i.ibb.co/RhSFc27/Zikra-Infotec-for-web-png.png" alt="Company Logo" class="mx-auto mb-4 w-64">
            <h1 class="text-4xl font-semibold">Careers</h1>
        </div>
        
        <div class="grid grid-cols-1 gap-8 p-8 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-2 2xl:grid-cols-3">
            <?php foreach ($activeCampaigns as $campaign): ?>
                <div class="flex rounded-lg shadow-lg overflow-hidden transform transition-transform duration-500 hover:scale-105">
                    <div class="bg-sky-600 w-2/5 p-4">
                        <h2 class="text-2xl text-white font-bold"><?php echo $campaign->title; ?></h2>
                        <p class="text-white text-opacity-70"><?php echo $campaign->position; ?></p>
                    </div>
                    <div class="w-3/5 px-6 pt-6 pb-3 bg-white flex flex-col">
                        <p class="text-gray-600 mb-2"><strong>Start Date:</strong> <?php echo $campaign->start_date; ?></p>
                        <p class="text-gray-600 mb-2"><strong>End Date:</strong> <?php echo $campaign->end_date; ?></p>
                        <?php if (isset($campaign->salary)): ?>
                            <p class="text-gray-600 mb-2"><strong>Salary:</strong> <?php echo $campaign->salary; ?></p>
                        <?php endif; ?>
                        <p class="text-gray-600 mb-4"><?php echo $campaign->description; ?></p>
                        <div class="mt-auto"> <a href="<?php echo site_url('career/apply/'.$campaign->id); ?>" class="inline-block bg-yellow-500 text-white font-semibold px-6 py-2 rounded transition-colors duration-200 hover:bg-yellow-600 w-full text-center">Apply</a></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>





    </div>
</body>
</html>
