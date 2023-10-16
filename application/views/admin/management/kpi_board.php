<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 
?>
<style>
    .collapsible-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s;
    }
</style>
<div id="wrapper" class="wrapper">
    <div class="content flex flex-col gap-8">

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">
            <div class="flex lg:flex-row flex-col gap-4 items-stretch justify-between">
                <!-- User Information Section -->
                <div class="flex flex-col lg:w-1/3 w-full bg-sky-100 p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px] min-h-full">
                    <div class="h-full flex justify-center items-center">
                        <h2 class="text-3xl font-bold text-center text-gray-700">KPI Board</h2>
                    </div>
                </div>
                <div class="flex flex-col lg:w-1/3 w-full bg-sky-100 p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px]">
                    <!-- Input boxes for FROM and TO -->
                    <div class="flex flex-col gap-2 my-2 mx-3 w-90">

                        <input type="date" id="from" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="From" value="<?= $from ?>">

                        <input type="date" id="to" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="To" value="<?= $to ?>">

                        <button class="px-4 py-2 bg-sky-100 border border-blue-600 rounded-[50px] text-blue-600 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 hover:text-white focus:ring-opacity-50 transition-all duration-300 mt-2" onclick="window.location.href=admin_url+'team_management/kpi_board/'+document.getElementById('from').value + '/' + document.getElementById('to').value">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </div>

                        
                </div>
            </div>
        </div>

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all flex flex-col">


            <!-- Select box for sorting -->
            <div class="w-full mb-6">
                <label for="sortSelect" class="mr-2">Sort by:</label>
                <select id="sortSelect" class="border rounded-md p-2 w-64">
                    <option value="name">Name</option>
                    <option value="ops">OPS</option>
                </select>
            </div>

            <div class="w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-sky-100 shadow-inner overflow-hidden grid grid-cols-3 p-4 gap-4" id="staffGrid">
                <?php foreach($staff_ops_data as $staff_id => $staff): ?>
                    <div class="flex flex-col justify-center items-center gap-2 shadow-inner bg-yellow-300 rounded-[40px] min-h-[100px] shadow-inner hover:shadow-xl shadow-none transition-all staff-box" data-name="<?= $staff['name'] ?>" data-ops="<?= $staff['ops'] ?>">
                        <h3 class="text-xl font-bold"><?= $staff['name'] ?></h3>
                        <h4 class="text-2xl font-bold"><?= round($staff['ops']/10,2) ?>/10</h4>
                    </div>
                <?php endforeach; ?>
            </div>


        </div>

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all flex flex-col">  

            <div class="w-full transition-all ease-in-out rounded-[40px] bg-yellow-300 font-bold flex flex-row text-xl overflow-hidden sticky top-4 z-10 mb-4">
                <div class="w-[20%] hover:bg-yellow-200 transition-all py-2 text-center">
                    Staff
                </div>
                <button title="Attendance Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    AR
                </button>
                <button title="Punctuality Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    PR
                </button>
                <button title="Task Completion Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    TCR
                </button>
                <button title="Task Efficiency Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    TER
                </button>
                <button title="Task Time Adherence" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    TTR
                </button>

                <button title="Summary Adherence Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    SAR
                </button>
                <button title="AFK Adherence Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    ADR
                </button>
                <button title="Shift Productivity Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    SPR
                </button>
                <button title="Overall Performance Score" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-yellow-200 transition-all py-2">
                    OPS
                </button>
                
            </div>

            <?php 
            $isFirst = true; 
            foreach ($kpi_data as $date): 
                $firstClass = $isFirst ? 'first-date' : '';
                $isFirst = false;
            ?>

            <div class="flex flex-row justify-between items-center gap-4 px-4 py-2 cursor-pointer text-lg text-gray-600 transition-all bg-gray-100 rounded-[40px]" data-date="<?= $date ?>" onclick="toggleCollapse(this, event, '<?= $date ?>')">
                <div class="opacity-0 transform transition-transform duration-300"></div>
                <div><?= date("jS F, l", strtotime($date)) ?></div>
                <div class="fas fa-angle-down rotate-[90deg] transform transition-transform duration-300"></div>
            </div>

            <!-- This div will be populated with the staff KPI data for the clicked date using AJAX -->
            <div class="collapsible-content w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-sky-100 shadow-inner overflow-hidden mb-5 <?= $firstClass ?>" id="<?= $date ?>"></div>

            <?php endforeach; ?>


            
        </div>


<?php init_tail(); ?>

<script>

function toggleCollapse(button, event, elementId) {
    if (event.target.tagName.toLowerCase() === 'button' || event.target.tagName.toLowerCase() === 'input') {
        return;
    }

    var content = document.getElementById(elementId);
    var arrow = button.querySelector('.fa-angle-down');


    var hasData = $(button).next().find('.flex.flex-row.text-base.transition-all').length > 0;

    if (!hasData) {
        // Fetch data using AJAX and only then expand the content
        fetchKpiDataForDate(button, function() {
            // Once AJAX is done, expand the content
            content.classList.add("expanded");
            updateMaxHeight(content, true);
            arrow.classList.add('rotate-[90deg]');
            button.classList.toggle('bg-amber-200/75');
            button.classList.toggle('py-3');
        });
    } else {
        var isExpanding = !content.classList.contains("expanded");
        content.classList.toggle("expanded");
        updateMaxHeight(content, isExpanding);
        arrow.classList.toggle('rotate-[90deg]');
        button.classList.toggle('py-3');
        button.classList.toggle('bg-amber-200/75');
    }
}


function updateMaxHeight(collapsibleContent, isExpanding) {
    if (isExpanding) {
        // Set max-height to scrollHeight when expanding
        var maxHeight = (collapsibleContent.scrollHeight + 40) + 'px';
        collapsibleContent.style.maxHeight = maxHeight;
    } else {
        // Reset max-height to 0 when collapsing
        collapsibleContent.style.maxHeight = '0';
    }
}

function fetchKpiDataForDate(dateDiv, callback) {
    var date = $(dateDiv).data('date'); // Fetch the date from the data attribute

    $.ajax({
        url: '<?= admin_url("team_management/fetch_kpi_for_date") ?>',
        type: 'POST',
        data: { date: date },
        dataType: 'json',
        success: function(staffStats) {
            var content = '';
            
            $.each(staffStats, function(staff_id, stats) {
            content += `
            <div class="flex flex-row text-base transition-all hover:bg-sky-200/75 staff-row" data-ar="${Math.round(stats['punctuality_rate']['present_percentage'] )}" data-pr="${Math.round(stats['punctuality_rate']['on_time_percentage'] )}" data-tcr="${Math.round(stats['task_rates']['completion_rate'] )}" data-ter="${Math.round(stats['task_rates']['efficiency_rate'] )}" data-ttr="${Math.round(stats['task_rates']['timer_adherence_rate'] )}" data-sar="${Math.round(stats['summary_adherence_rate']['percentage'] )}" data-adr="${Math.round(stats['afk_adherence_rate']['percentage'] )}" data-spr="${Math.round(stats['shift_productivity_rate']['percentage'] )}" data-ops="${Math.round(stats['ops']) / 10}" >

                <div class="w-[20%] hover:bg-yellow-200 transition-all py-2 text-center">
                ${stats['name']}
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['punctuality_rate']['present_percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['punctuality_rate']['on_time_percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['task_rates']['completion_rate'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['task_rates']['efficiency_rate'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['task_rates']['timer_adherence_rate'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['summary_adherence_rate']['percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['afk_adherence_rate']['percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['shift_productivity_rate']['percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-yellow-200 transition-all py-2">
                    ${Math.round(stats['ops']) / 10}/10
                </div>
            </div>`;
        });

            
            $(dateDiv).next().html(content);

            // Execute the callback if provided
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    });
}


$(document).ready(function() {
    var firstDateButton = $('.first-date').prev(); // Assuming the button is directly before the collapsible content
    toggleCollapse(firstDateButton[0], {target: firstDateButton[0]}, $('.first-date').attr('id'));

    $('#sortSelect').on('change', function() {
        var sortBy = $(this).val();
        
        var staffBoxes = $('.staff-box').sort(function(a, b) {
            if (sortBy === 'name') {
                return $(a).data('name').localeCompare($(b).data('name'));
            } else { // Sort by OPS
                return $(b).data('ops') - $(a).data('ops');
            }
        });

        $('#staffGrid').html(staffBoxes);
    });

    $(".kpi-button").on('click', function() {
        var kpiType = $(this).text().trim().toLowerCase(); // e.g. "ar"

        // Iterate over all expanded date sections
        $(".collapsible-content.expanded").each(function() {
            var dateSection = $(this);

            var sortedRows = $(".staff-row", dateSection).sort(function(a, b) {
                return $(b).data(kpiType) - $(a).data(kpiType); // Descending order
            });

            dateSection.html(sortedRows);
        });
    });
});


</script>
</body>
</html>