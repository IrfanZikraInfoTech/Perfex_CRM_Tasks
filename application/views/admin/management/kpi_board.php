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

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all">
            <div class="flex lg:flex-row flex-col gap-4 items-stretch justify-between">
                <!-- User Information Section -->
                <div class="flex flex-col lg:w-1/3 w-full bg-<?= get_option('management_theme_background')?> p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px] min-h-full">
                    <div class="h-full flex justify-center items-center">
                        <h2 class="text-3xl font-bold text-center text-gray-700">KPI Board</h2>
                    </div>
                </div>
                <div class="flex flex-col lg:w-1/3 w-full bg-<?= get_option('management_theme_background')?> p-4 py-3 shadow-inner rounded-[50px]  max-h-[300px]">
                    <!-- Input boxes for FROM and TO -->
                    <div class="flex flex-col gap-2 my-2 mx-3 w-90">

                        <input type="date" id="from" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="From" value="<?= $from ?>">

                        <input type="date" id="to" class="w-full py-2 px-4 border !rounded-3xl text-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="To" value="<?= $to ?>">

                        <select data-width="100%" id="staff" data-live-search="true" class="selectpicker text-2xl font-bold text-uppercase" multiple>
                                
                            <?php 
                                $staff_members = $this->staff_model->get();


                                foreach($staff_members as $staff_member){
                                    $selected = '';

                                    if(isset($exclude_ids) && in_array($staff_member['staffid'], $exclude_ids)){
                                        $selected = 'selected';
                                    }

                                    echo '<option '.$selected.' value="'.$staff_member['staffid'].'">'.$staff_member['full_name'].'</option>';
                                }
                            ?>
                                
                        </select>

                        <button class="px-4 py-2 bg-<?= get_option('management_theme_background')?> border border-blue-600 rounded-[50px] text-blue-600 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-600 hover:text-white focus:ring-opacity-50 transition-all duration-300 mt-2" onclick="redirectToKpiBoard()">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </div>

                        
                </div>
            </div>
        </div>

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all flex flex-col">


            <!-- Select box for sorting -->
            <div class="w-full mb-6">
                <label for="sortSelect" class="mr-2">Sort by:</label>
                <select id="sortSelect" class="border rounded-md p-2 w-64">
                    <option value="name">Name</option>
                    <option value="ops">OPS</option>
                </select>
            </div>

            <div class="w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-<?= get_option('management_theme_background')?> shadow-inner overflow-hidden grid grid-cols-3 p-4 gap-4" id="staffGrid">
                <?php foreach($staff_kpi_data as $staff_id => $staff): ?>
                    <div class="flex flex-col justify-center items-center gap-2 shadow-inner bg-<?= get_option('management_theme_foreground')?> rounded-[40px] min-h-[100px] shadow-inner hover:shadow-xl shadow-none transition-all staff-box" data-name="<?= $staff['name'] ?>" data-ops="<?= $staff['ops'] ?>">
                        <h3 class="text-xl font-bold"><?= $staff['name'] ?></h3>
                        <h4 class="text-2xl font-bold"><?= round($staff['ops']/10,2) ?>/10</h4>
                    </div>
                <?php endforeach; ?>
            </div>


        </div>

        <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all flex flex-col">  

            <div class="w-full transition-all ease-in-out rounded-[40px] bg-<?= get_option('management_theme_foreground')?> font-bold flex flex-row text-xl overflow-hidden sticky top-4 z-20 mb-4">
                <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center">
                    Staff
                </div>
                <button title="Attendance Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ar">
                    AR
                </button>
                <button title="Punctuality Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="pr">
                    PR
                </button>
                <button title="Task Completion Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="tcr">
                    TCR
                </button>
                <button title="Task Efficiency Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ter">
                    TER
                </button>
                <button title="Task Time Adherence" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ttr">
                    TTR
                </button>

                <button title="Summary Adherence Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="sar">
                    SAR
                </button>
                <button title="AFK Adherence Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="adr">
                    ADR
                </button>
                <button title="Shift Productivity Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="spr">
                    SPR
                </button>
                <button title="Overall Performance Score" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ops">
                    OPS
                </button>
                
            </div>

            <div class="w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-<?= get_option('management_theme_background')?> shadow-inner overflow-hidden mb-4 rows-container">
             
                <?php foreach($staff_kpi_data as $staff_id => $staff): ?>
                    <div class="flex flex-row text-base transition-all hover:bg-sky-200/75 staff-row" data-ar="<?= round($staff['ar'],2) ?>" data-pr="<?= round($staff['pr'],2) ?>" data-tcr="<?= round($staff['tcr'],2) ?>" data-ter="<?= round($staff['ter'],2) ?>" data-ttr="<?= round($staff['ttr'],2) ?>" data-sar="<?= round($staff['sar'],2) ?>" data-adr="<?= round($staff['adr'],2) ?>" data-spr="<?= round($staff['spr'],2) ?>" data-ops="<?= round($staff['ops']/10,2) ?>">

                        <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center">
                        <?= $staff['name'] ?>
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                            <?= round($staff['ar'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['pr'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['tcr'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['ter'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['ttr'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['sar'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['adr'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['spr'],2) ?>%
                        </div>
                        <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                        <?= round($staff['ops']/10,2) ?>/10
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>


            <div class="w-full transition-all ease-in-out rounded-[40px] bg-yellow-100 font-bold flex flex-row text-xl top-4 z-10 mb-5 overflow-hidden sticky bottom-4">
                <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center">
                    Cumuluative KPIs
                </div>
                <button title="Attendance Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ar">
                    <?= round($cumulative_kpis['ar'],2) ?>%
                </button>
                <button title="Punctuality Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="pr">
                <?= round($cumulative_kpis['pr'],2) ?>%
                </button>
                <button title="Task Completion Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="tcr">
                <?= round($cumulative_kpis['tcr'],2) ?>%
                </button>
                <button title="Task Efficiency Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ter">
                <?= round($cumulative_kpis['ter'],2) ?>%
                </button>
                <button title="Task Time Adherence" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ttr">
                <?= round($cumulative_kpis['ttr'],2) ?>%
                </button>

                <button title="Summary Adherence Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="sar">
                <?= round($cumulative_kpis['sar'],2) ?>%
                </button>
                <button title="AFK Adherence Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="adr">
                <?= round($cumulative_kpis['adr'],2) ?>%
                </button>
                <button title="Shift Productivity Rate" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="spr">
                <?= round($cumulative_kpis['spr'],2) ?>%
                </button>
                <button title="Overall Performance Score" data-toggle="tooltip" data-placement="top" class="kpi-button w-[8.88%] border-l border-solid border-gray-600 text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2" data-kpi_name="ops">
                <?= round($cumulative_kpis['total_ops'],2) ?>%
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
            <div class="collapsible-content w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-<?= get_option('management_theme_background')?> shadow-inner overflow-hidden mb-5 <?= $firstClass ?> rows-container" id="<?= $date ?>"></div>

            <?php endforeach; ?>
            
        </div>


<?php init_tail(); ?>

<script>
function redirectToKpiBoard() {
    // Get the selected values from the #staff select element
    var staffSelect = document.getElementById('staff');
    var selectedOptions = [];
    for (var i = 0; i < staffSelect.options.length; i++) {
        if (staffSelect.options[i].selected) {
            selectedOptions.push(staffSelect.options[i].value);
        }
    }

    // Convert the selected values to a comma-separated string
    var staffValues = selectedOptions.join('e');

    // Get the values of 'from' and 'to' elements
    var fromValue = document.getElementById('from').value;
    var toValue = document.getElementById('to').value;

    // Construct the URL with the selected staff values
    var url = admin_url + 'team_management/kpi_board/' + fromValue + '/' + toValue + '/' + staffValues;

    // Redirect to the constructed URL
    window.location.href = url;
}

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
        data: { 
            date: date
        },
        dataType: 'json',
        success: function(staffStats) {
            var content = '';
            
            $.each(staffStats, function(staff_id, stats) {
            content += `
            <div class="flex flex-row text-base transition-all hover:bg-sky-200/75 staff-row" data-ar="${Math.round(stats['punctuality_rate']['present_percentage'] )}" data-pr="${Math.round(stats['punctuality_rate']['on_time_percentage'] )}" data-tcr="${Math.round(stats['task_rates']['completion_rate'] )}" data-ter="${Math.round(stats['task_rates']['efficiency_rate'] )}" data-ttr="${Math.round(stats['task_rates']['timer_adherence_rate'] )}" data-sar="${Math.round(stats['summary_adherence_rate']['percentage'] )}" data-adr="${Math.round(stats['afk_adherence_rate']['percentage'] )}" data-spr="${Math.round(stats['shift_productivity_rate']['percentage'] )}" data-ops="${Math.round(stats['ops']) / 10}" >

                <div class="w-[20%] hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2 text-center">
                ${stats['name']}
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['punctuality_rate']['present_percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['punctuality_rate']['on_time_percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['task_rates']['completion_rate'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['task_rates']['efficiency_rate'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['task_rates']['timer_adherence_rate'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['summary_adherence_rate']['percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['afk_adherence_rate']['percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
                    ${Math.round(stats['shift_productivity_rate']['percentage'] )}%
                </div>
                <div class="w-[8.88%] border-l border-solid text-center hover:bg-<?= get_option('management_theme_hover')?> transition-all py-2">
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
    // var firstDateButton = $('.first-date').prev(); // Assuming the button is directly before the collapsible content
    // toggleCollapse(firstDateButton[0], {target: firstDateButton[0]}, $('.first-date').attr('id'));

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
        var kpiType = $(this).data("kpi_name"); // e.g. "ar"

        // Iterate over all expanded date sections
        $(".rows-container").each(function() {
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