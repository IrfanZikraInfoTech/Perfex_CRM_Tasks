<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 

?>
<style>
.google-visualization-orgchart-table{
    margin: auto;
}
</style>
<div id="wrapper">

   <div class="content flex flex-col gap-10">


        <!--\\\\\\\ Individual Stats \\\\\\\\-->

       
        <div class="w-full">
        <div class="w-full mb-4 bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all overflow-x-auto xl:cursor-auto cursor-move ">
            <h2 class="text-3xl font-bold text-center">Team</h2>
            <div class="mt-10 flex flex-row justify-between items-center gap-4 px-4 py-2 cursor-pointer text-lg text-gray-600 transition-all bg-gray-100 rounded-[40px]" onclick="toggleCollapse(this, event, 'chart_content')">
        <div class="opacity-0 transform transition-transform duration-300"></div>
            <div>View Chart</div>
            <div class="fas fa-angle-down rotate-[90deg] transform transition-transform duration-300"></div>
        </div>
        <div class="collapsible-content w-full transition-all ease-in-out rounded-[40px] border border-solid border-white bg-<?= get_option('management_theme_background')?> shadow-inner overflow-hidden mb-5 xl:text-base text-sm" style="max-height: 0; overflow: hidden;" id="chart_content">
        
        <div class="my-4 p-4 rounded-[50px] border border-solid border-gray-100 hover:border-<?= get_option('management_theme_border')?> bg-gray-200" id="chart_container">
              
                    <div class="w-full justify-center py-4" id="chart_div"></div>
               
            </div> 
        </div> 
        </div>
        


    <div class="flex flex-col gap-4 mt-4">

        <?php  foreach ($departments as $department): ?>

            <div class="rounded-[40px] bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all">

            <h2 class="text-xl pl-7 uppercase font-bold text-gray-800 text-center py-4"><?php echo $department->name; ?></h2>
            
            <div class="px-5 pb-5">
                <?php $staff_members = $this->team_management_model->get_staff_by_department($department->departmentid); 
                
                ?>
                    <div class="p-5 rounded-[40px] bg-gray-100 grid 2xl:grid-cols-5 lg:grid-cols-3 md:grid-cols-2 grid-cols-1  gap-10">
                    <?php  foreach ($staff_members as $staff): ?>
                        <?php 
                        // Check if staff is CEO
                        $isCEO = ($staff->report_to === null );

                        // If staff is not CEO or if toggle for CEO data is ON
                        if (!$isCEO || ($isCEO && $show_ceo_data == 1)): 
                        ?>
                        <!-- Your staff card code starts here -->
                        <div class="flex flex-col bg-white w-full rounded-[30px] overflow-hidden transition-all duration-500 ease-in-out hover:scale-[0.97] shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?>">
                            <div class="flex justify-center items-center p-4 bg-gray-200">
                                <?php echo staff_profile_image($staff->staffid, ['h-48', 'w-48', 'rounded-full', 'object-cover'], 'thumb'); ?>
                            </div>
                            <div class="p-4">
                                <h2 class="text-base text-center"><?php echo $staff->firstname. ' ' .$staff->lastname ?></h2>


                                <?php
                               $report_to_id = $staff->report_to;
                                $reporting_to_name = id_to_name($report_to_id, 'tblstaff', 'staffid', 'firstname') . ' ' .id_to_name($report_to_id, 'tblstaff', 'staffid', 'lastname');

                                if ($isCEO || empty($reporting_to_name)) {
                                    $reporting_to_name = "None";
                                }
                              ?>
                                <p class="text-base text-center">
                                    <span class="font-semibold">Report:</span> 
                                    <span class="font-medium"><?= $reporting_to_name; ?></span>
                                </p>
  

                                <h2 class="text-lg font-bold text-center"><?php echo $staff->staff_title ?></h2>
                                
                                
                                
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Current Status</div>
                                    <div class="flex flex-row gap-1 mt-1 text-gray-800"><div class="w-1 <?php echo 'bg-' . $staff->statusColor; ?> h-5 mr-2"></div><?php echo $staff->status; ?> \ <?php echo $staff->working ? 'Working' : 'Not working'; ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Time spent today</div>
                                    <div class="mt-1 text-gray-800"><?php echo convertSecondsToRoundedTime($staff->live_time_today); ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Current Task</div>
                                    <div class="mt-1 text-gray-800"><?php echo $staff->currentTaskName; ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Current Project</div>
                                    <div class="mt-1 text-gray-800"><?php echo $staff->currentTaskProject ?? 'None'; ?></div>
                                </div>
                                <div class="mt-4">
                                    <div class="text-sm font-semibold text-gray-500">Task Timer</div>
                                    <div class="mt-1 text-gray-800"><?php echo convertSecondsToRoundedTime($staff->currentTaskTime); ?></div>
                                </div>
                                <?php if(has_permission('team_management', '', 'admin')){ ?>
                                <div class="mt-6 grid grid-cols-1 gap-2">
                                    <a target="_blank" href="<?= admin_url();?>team_management/control_room/<?= $staff->staffid ?>" class="text-center py-2 px-3 text-white rounded-3xl bg-sky-400 hover:bg-sky-500 hover:text-gray-100 transition-all">Control Room</a>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Your staff card code ends here -->
                    <?php endforeach; ?>
                    </div>            
                </div>

            </div>
        <?php endforeach; ?>

      
   </div>

</div>


<?php init_tail(); ?>
<script src='https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js'></script>
<script>
    function toggleCollapse(button, event, elementId) {
    if (event.target.tagName.toLowerCase() === 'button' || event.target.tagName.toLowerCase() === 'input') {
        return;
    }

    var content = document.getElementById(elementId);
    var arrow = button.querySelector('.fa-angle-down');

    var isExpanding = !content.classList.contains("expanded");
    content.classList.toggle("expanded");
    if(isExpanding) {
        content.style.padding = " 10px";
        content.style.maxHeight = content.scrollHeight + "px";
    } else {
       
        content.style.maxHeight = "0px";
        content.style.padding = " 0px";

    }
    arrow.classList.toggle('rotate-[90deg]');
}

    $('#universalReminders').on('click', function() {

        if(confirm("Are you sure?")){
            alert_float("info", "Please wait, Reminders take time");
        // Save the formFieldsData to the database
            $.ajax({
                url: '<?= admin_url();?>team_management/universalReminders', // Replace this with the actual URL to your controller function
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if(response.success){
                        alert_float("success", "Reminders sent!");
                    }else{
                        alert_float("danger", "Reminders not sent!");
                    }
                    
                },
                error: function() {
                    // Show an error message or perform any other action on save failure
                    alert_float("danger", "Reminders not sent!");
                }
            });
        }
    });  

   
</script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  var hierarchyData = <?php echo json_encode($hierarchy); ?>;

  google.charts.load('current', {packages:["orgchart"]});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    var data = new google.visualization.DataTable();
    data.addColumn('string', 'Name');
    data.addColumn('string', 'Manager');

    function addData(node, parentId) {
        var name = '<div class="flex flex-col w-max !justify-center !mx-auto">'+node.profile+'<div>' + node.firstname + ' ' + node.lastname + '</div><div class="font-bold">' + (node.staff_title || '') + '</div></div>';
        var id = node.staffid.toString();
        data.addRow([{
            v: id,
            f: name
        }, parentId]);

        if (node.subordinates) {
            for (var i = 0; i < node.subordinates.length; i++) {
                addData(node.subordinates[i], id);
            }
        }
    }

    for (var i = 0; i < hierarchyData.length; i++) {
        addData(hierarchyData[i], null);
    }

    var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
    chart.draw(data, {'allowHtml':true, nodeClass: 'py-3 !text-lg !rounded-[30px] !px-10 !py-2 bg-white transition-all hover:bg-<?= get_option('management_theme_hover')?> !border-none', width: '100%'});
    }

    var chart_div = document.querySelector('#chart_div');

    // And pass it to panzoom
    panzoom(chart_div,{
        maxZoom: 1,
        minZoom: 0.1,
        initialX: -19000,
        initialY: 0,
        initialZoom: 0.8
    });
</script>


</body>
</html>