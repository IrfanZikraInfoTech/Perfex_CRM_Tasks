<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();

?>

<div id="wrapper">
    <div class="content">
        <div class="container mx-auto px-4 py-5">
            <h1 class="text-4xl font-bold mb-5">Campaigns</h1>
            <div class="flex flex-row flex-wrap justify-center">
                <?php foreach ($campaigns as $campaign): ?>
                    <div class="w-96 m-4 transform transition duration-500 ease-in-out hover:scale-105">
                        <a href="<?= admin_url() ?>recruitment_portal/submissions/<?= $campaign->id ?>"
                        class="block w-full h-full p-6 rounded-lg shadow-md hover:shadow-xl bg-white transition duration-200 ease-in-out transform hover:-translate-y-1">
                            <h2 class="text-2xl font-bold mb-2 text-center text-gray-800"><?= $campaign->title ?></h2>
                            <div class="grid grid-cols-2 gap-4 mt-4 text-center text-gray-600">
                                <div>
                                    <p class="text-lg"><?= $campaign->submissions_new ?></p>
                                    <p>New Submissions</p>
                                </div>
                                <div>
                                    <p class="text-lg"><?= $campaign->submissions_rejected ?></p>
                                    <p>Rejected Submissions</p>
                                </div>
                                <div>
                                    <p class="text-lg"><?= $campaign->submissions_invited ?></p>
                                    <p>Invited Submissions</p>
                                </div>
                                <div>
                                    <p class="text-lg"><?= $campaign->submissions_on_hold ?></p>
                                    <p>On Hold Submissions</p>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>


</body>
</html>