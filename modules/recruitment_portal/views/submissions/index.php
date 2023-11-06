<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head();

?>

<div id="wrapper">
    <div class="content">
        <div class="container mx-auto px-4 py-5">
            <h1 class="text-4xl font-bold mb-5 text-center">Campaigns</h1>

            <div class="w-full flex justify-center gap-4 mb-4">

                <div class="w-1/5 flex flex-col">
                    <label for="submission_name" class="mb-1 text-sm">Name:</label>
                    <input id="submission_name" type="text" class="rounded transition-all border-blue-500 focus:ring-blue-500 hover:bg-white border border-solid px-2 py-2" value="" />
                </div>

                <div class="w-1/5 flex flex-col">
                    <label for="submission_email" class="mb-1 text-sm">Email:</label>
                    <input id="submission_email" type="email" class="rounded transition-all border-blue-500 focus:ring-blue-500 hover:bg-white border border-solid px-2 py-2" value="" />
                </div>

                <button onclick="applyFilters();" class="rounded transition-all bg-blue-600 text-white hover:bg-white hover:text-blue-500 hover:border-blue-500 border border-solid px-4 py-2 w-[10%] text-center h-full mt-auto">Go!</button>

            </div>


            <div class="flex flex-row flex-wrap justify-center">
                <?php if(empty($campaigns)){?>
                    <div class="w-96 m-4 transform transition duration-500 ease-in-out hover:scale-105">
                        <a href="#"
                        class="block w-full h-full p-6 rounded-lg shadow-md hover:shadow-xl bg-white transition duration-200 ease-in-out transform hover:-translate-y-1">
                            <h2 class="text-2xl font-bold text-center text-gray-800">No Campaign Found</h2>
                        </a>
                    </div>
                <?php } else {?>
                <?php foreach ($campaigns as $campaign): ?>
                    <div class="w-96 m-4 transform transition duration-500 ease-in-out hover:scale-105">
                        <a href="<?= admin_url() ?>recruitment_portal/submissions/<?= $campaign->id ?>"
                        class="block w-full h-full p-6 rounded-lg shadow-md hover:shadow-xl bg-white transition duration-200 ease-in-out transform hover:-translate-y-1">
                            <h2 class="text-2xl font-bold mb-2 text-center text-gray-800"><?= $campaign->title ?></h2>
                            <div class="grid grid-cols-2 gap-4 mt-4 text-center text-gray-600">
                                <div>
                                    <p class="text-lg"><?= $campaign->unviewed_submissions ?></p>
                                    <p>Unviewed Submissions</p>
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

                <?php endforeach;} ?>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>

<script>

function applyFilters() {

    var submission_name = document.getElementById('submission_name').value; 
    var submission_email = document.getElementById('submission_email').value; 
    query = '?name='+submission_name+'&email='+submission_email ;

    // Reload the page with the new query string
    window.location.href = '<?= admin_url('recruitment_portal/submissions/0') ?>' + query;
}

</script>

</body>
</html>