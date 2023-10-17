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
        <div class="w-full mb-4">
            <h2 class="text-3xl font-bold text-center">Team</h2>
            <div class="my-4 p-4 rounded-[50px] border border-solid border-gray-100 hover:border-yellow-400 bg-gray-200">
                <div class="w-flex justify-center w-full overflow-x-auto" id="chart_div"></div>
            </div>

        </div>

    <div class="flex flex-col gap-4">

        <?php  foreach ($departments as $department): ?>

            <div class="rounded-[40px] bg-white shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400 transition-all">

            <h2 class="text-xl pl-7 uppercase font-bold text-gray-800 text-center py-4"><?php echo $department->name; ?></h2>
            
            <div class="px-5 pb-5">
                <?php $staff_members = $this->team_management_model->get_staff_by_department($department->departmentid); 
                
                ?>
                    <div class="p-5 rounded-[40px] bg-gray-100 flex flex-row flex-wrap gap-10">
                    <?php  foreach ($staff_members as $staff): ?>
                        <!-- Your staff card code starts here -->
                        <div class="flex flex-col bg-white xl:w-1/4 2xl:w-1/5 lg:w-1/3 md:w-[40%] w-full rounded-[30px] overflow-hidden transition-all duration-500 ease-in-out hover:scale-[0.97] shadow-lg hover:shadow-xl border border-solid border-white hover:border-yellow-400">
                            <div class="flex justify-center items-center p-4 bg-gray-200">
                                <?php echo staff_profile_image($staff->staffid, ['h-48', 'w-48', 'rounded-full', 'object-cover'], 'thumb'); ?>
                            </div>
                            <div class="p-4">
                                <h2 class="text-lg font-bold text-center"><?php echo $staff->firstname. ' ' .$staff->lastname ?></h2>
                                <h2 class="text-lg font-bold text-center"><?php echo $staff->staff_title ?></h2>
                                
                                <div class="mt-1">
                                <?php
                                $report_to_id = $staff->report_to;

                                $reporting_to_name = id_to_name($report_to_id, 'tblstaff', 'staffid', 'firstname') . ' ' .id_to_name($report_to_id, 'tblstaff', 'staffid', 'lastname');
                                ?>
                                <p class="text-md">
                                    <span class="font-semibold">Reporting to:</span> 
                                    <span class="font-medium"><?= $reporting_to_name; ?></span>
                                </p>
                                </div>
                                
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
                        <!-- Your staff card code ends here -->
                    <?php endforeach; ?>
                    </div>            
                </div>

            </div>
        <?php endforeach; ?>

      
   </div>

</div>


<?php init_tail(); ?>

<script>
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

    // Function to add data to the chart
    function addData(node, parentId) {
        var name = '<div class="flex flex-col w-max !justify-center !mx-auto">'+node.profile+'<div>' + node.firstname + ' ' + node.lastname + '</div><div class="font-bold">' + (node.staff_title || '') + '</div></div>';
        var id = node.staffid.toString();
        data.addRow([{
            v: id,
            f: name
        }, parentId]);

        if (node.subordinates) {
            for (var i = 0; i < node.subordinates.length; i++) {
                addData(node.subordinates[i], id); // recursive call for subordinates
            }
        }
    }

    // Assuming hierarchyData is an array of top-level nodes
    for (var i = 0; i < hierarchyData.length; i++) {
        addData(hierarchyData[i], null);
    }

    // Create the chart.
    var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
    // Draw the chart, setting the allowHtml option to true for the tooltips.
    chart.draw(data, {'allowHtml':true, nodeClass: '!text-lg !rounded-[30px] !px-10 !py-2 bg-white transition-all hover:bg-sky-100 !border-none', width: '100%'});
    }



    const container = document.querySelector('#chart_div');
                
                let startY;
                let startX;
                let scrollLeft;
                let scrollTop;
                let isDown;
                
                container.addEventListener('mousedown',e => mouseIsDown(e));  
                container.addEventListener('mouseup',e => mouseUp(e))
                container.addEventListener('mouseleave',e=>mouseLeave(e));
                container.addEventListener('mousemove',e=>mouseMove(e));
                
                
                function mouseIsDown(e){
                  isDown = true;
                  startY = e.pageY - container.offsetTop;
                  startX = e.pageX - container.offsetLeft;
                  scrollLeft = container.scrollLeft;
                  scrollTop = container.scrollTop; 
                
                }
                function mouseUp(e){
                  isDown = false;
                }
                function mouseLeave(e){
                  isDown = false;
                }
                function mouseMove(e){
                  if(isDown){
                    
                    
                
                    e.preventDefault();
                
                
                
                    //Move vertcally
                    const y = e.pageY - container.offsetTop;
                    const walkY = y - startY;
                    container.scrollTop = scrollTop - walkY;
                
                    //Move Horizontally
                    const x = e.pageX - container.offsetLeft;
                    const walkX = x - startX;
                    container.scrollLeft = scrollLeft - walkX;
                
                  }
                }
</script>


</body>
</html>