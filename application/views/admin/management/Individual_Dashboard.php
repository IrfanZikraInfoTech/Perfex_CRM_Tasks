<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); 

function convertSecondsToRoundedTime($seconds)
{
    $hours = floor($seconds / 3600);
    $minutes = round(($seconds % 3600) / 60);

    if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    } else {
        return "{$minutes}m";
    }
}

?>
<style>
    .row-options{
        display: none;
    }
</style>
<div id="wrapper">
<!-- Main container -->
<div class="bg-white p-6 rounded shadow-md max-w-2xl mx-auto">

<!-- Header -->
<div class="flex items-center space-x-4 mb-6">
    <div class="bg-gray-300 p-10 rounded-full"></div>
    <div>
        <p class="font-bold text-xl">MOHAMMAD ANSAR ULLAH ANAS</p>
        <p>DIRECTOR</p>
        <p>MANAGEMENT DEPARTMENT</p>
        <p>REPORTING TO: GOD ALMIGHTY</p>
    </div>
</div>

<!-- KPI and Score -->
<div class="grid grid-cols-2 gap-6 mb-6">

    <!-- KPI -->
    <div>
        <h2 class="mb-4 font-bold text-xl">Key Performance Indicators</h2>
        <ul>
            <li>Punctuality Rate: 100%</li>
            <li>Task Completion Rate: 50%</li>
            <li>Task Efficacy Rate: 100%</li>
            <!-- Add more KPIs as required -->
        </ul>
    </div>

    <!-- Score -->
    <div>
        <h2 class="mb-4 font-bold text-xl">Overall Performance Score</h2>
        <!-- Note: For a dynamic radial progress bar, you might need a library or SVG. For simplicity, I'm using text here. -->
        <div class="text-center">
            <p class="text-9xl">8/10</p>
            <p class="mt-2">Custom Remark</p>
        </div>
    </div>

</div>

<!-- Export button -->
<div class="text-center">
    <button class="bg-blue-500 text-white rounded py-2 px-4 hover:bg-blue-600">EXPORT</button>
</div>

</div>

</div>


<?php init_tail(); ?>

<script>

</script>

</body>
</html>