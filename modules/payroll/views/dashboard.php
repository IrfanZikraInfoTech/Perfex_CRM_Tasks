<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content">
    <div class="clearfix"></div>
        <div class="row">
            <div class="flex justify-end mt-6 ml-4">
                <form method="post" action="<?php echo base_url('payroll/dashboard'); ?>" class="flex items-center bg-white rounded-full shadow-lg">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    
                    <div class="p-2">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-6a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <select name="selectedMonth" id="monthSelect" class="w-full py-2 pr-4 pl-2 rounded-full focus:outline-none focus:border-blue-300">
                        <!-- Populate options for each month -->
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo (isset($selectedMonth) && $selectedMonth == $i) ? 'selected' : ''; ?>>
                                <?php echo date('F', mktime(0, 0, 0, $i, 10)); ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn btn-primary text-white py-2 px-4 rounded-full" style="background-color: <?= $bg_color ?>;">
                        Submit
                    </button>
                </form>
            </div>
            <div class="flex justify-start mr-4">
                <div class="text-lg font-semibold text-gray-700">
                    <h3 class="text-md text-gray-600">Total Staff Receiving Salaries: <span class="text-blue-600"><?php echo $total_staff; ?></span>
                    </h3>
                </div>
            </div>
        </div>
		<!-- top cards -->
        <div class="row">
            <div class="flex flex-wrap -mx-2">
                <div class="w-full lg:w-6/12 p-4">
                    <div class="bg-white p-6 rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200 transition-all duration-500 ease-in-out relative flex items-center justify-center">
                        <div class="mr-6">
                            <p id="total_salaries_for_month" class="text-3xl font-extrabold text-[#0086BE]"><?php echo $total_salaries_for_month; ?></p>
                            <h5 class="text-lg font-medium text-uppercase mb-2 text-[#0086BE]">Salaries </h5>
                        </div>
                        <div class="w-16 h-16 bg-[#0086BE] rounded-full flex items-center justify-center ml-auto">
                            <i class="fas fa-dollar-sign text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                   <div class="w-full lg:w-6/12 p-4">
                    <div class="bg-white p-6 rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200 transition-all duration-500 ease-in-out relative flex items-center justify-center">
                        <div class="mr-6">
                            <p id="total_salaries_pakistan" class="text-3xl font-extrabold text-[#115740]"><?php echo $total_salaries_pak . ' ' . $currency_pak; ?>/ $<?php echo $total_salaries_pak_usd; ?></p>
                            <h5 class="text-lg font-medium text-uppercase mb-2 text-[#115740]">Pakistan Salaries</h5>
                        </div>
                        <div class="w-16 h-16 bg-[#115740] rounded-full flex items-center justify-center ml-auto">
                            <i class="fas fa-rupee-sign text-white text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-6/12 p-4">
                    <div class="bg-white p-6 rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200 transition-all duration-500 ease-in-out relative flex items-center justify-center">
                        <div class="mr-6">
                            <p id="total_salaries_bangladesh" class="text-3xl font-extrabold text-[#F42A41]"><?php echo $total_salaries_bang . ' ' . $currency_bang; ?></p>
                            <h5 class="text-lg font-medium text-uppercase mb-2 text-[#F42A41]">Bangladesh Salaries</h5>
                        </div>
                        <div class="w-16 h-16 bg-[#F42A41] rounded-full flex items-center justify-center ml-auto">
                        <span class="text-white text-2xl"> ৳</span> <!-- Updated to use the actual currency symbol -->
                        </div>
                    </div>
                </div>

                <div class="w-full lg:w-6/12 p-4">
                    <div class="bg-white p-6 rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200 transition-all duration-500 ease-in-out relative flex items-center justify-center">
                        <div class="mr-6">
                            <p id="total_salaries_india" class="text-3xl font-extrabold text-[#FF671F]">                <?php echo $total_salaries_ind . ' ' . $currency_ind; ?> / $<?php echo $total_salaries_ind_usd; ?>

                            <h5 class="text-lg font-medium text-uppercase mb-2 text-[#FF671F)]">India Salaries</h5>
                        </div>
                        <div class="w-16 h-16 bg-[#FF671F] rounded-full flex items-center justify-center ml-auto">
                        <span class="text-white text-2xl">₹</span> <!-- Updated to use the actual currency symbol -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap -mx-2">

        <div class="w-full md:w-1/2 p-2">
                <div class="card bg-white shadow-lg rounded-lg hoverable py-3 cursor-pointer rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <h2 class="card-title ms-1 text-uppercase text-center mb-4" style="font-weight: bold; color: #343a40; letter-spacing: 1.5px;"></h2>
                        <div class="d-flex justify-content-center">
                            <canvas id="salariesChart" style="max-width: 90%; height: auto;"class="my-2 mx-auto"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full md:w-1/2 p-2">
                <div class="card bg-white shadow-lg rounded-lg hoverable py-3 cursor-pointer rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <h2 class="card-title ms-1 text-uppercase text-center mb-4" style="font-weight: bold; color: #343a40; letter-spacing: 1.5px;"></h2>
                        <div class="d-flex justify-content-center">
                            <canvas id="departmentSalariesChart" style="max-width: 90%; height: auto;"class="my-2 mx-auto"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full p-2">
                <div class="card bg-white shadow-lg rounded-lg hoverable py-3 cursor-pointer rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                        <h2 class="card-title ms-1 text-uppercase text-center mb-4" style="font-weight: bold; color: #343a40; letter-spacing: 1.5px;"></h2>
                        <div class="d-flex justify-content-center">
                            <canvas id="monthSalariesChart" style="max-width: 80%; height: auto;"class="my-2 mx-auto"></canvas>
                        </div>
                    </div>
                </div>
            </div>


        </div>


    </div>
</div>
<?php init_tail(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.3/chart.min.js" integrity="sha512-fMPPLjF/Xr7Ga0679WgtqoSyfUoQgdt8IIxJymStR5zV3Fyb6B3u/8DcaZ6R6sXexk5Z64bCgo2TYyn760EdcQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
document.addEventListener("DOMContentLoaded", function(){
    var ctx = document.getElementById('salariesChart').getContext('2d');
    var salariesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Pakistan', 'Bangladesh', 'India'],
            datasets: [{
                label: 'Total Salaries',
                data: [ <?php echo $total_salaries_pak; ?>, <?php echo $total_salaries_bang; ?>, <?php echo $total_salaries_ind; ?>],
                backgroundColor: [
                    'rgba(0, 134, 190, 0.2)',
                    'rgba(17, 87, 64, 0.2)',
                    'rgba(244, 42, 65, 0.2)',
                    'rgba(255, 103, 31, 0.2)'
                ],
                borderColor: [
                    'rgba(17, 87, 64, 1)',
                    'rgba(244, 42, 65, 1)',
                    'rgba(255, 103, 31, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});




document.addEventListener("DOMContentLoaded", function(){
    var ctx = document.getElementById('departmentSalariesChart').getContext('2d');
    var departmentSalariesLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_column($department_salaries, 'department_name')); ?>,
            datasets: [{
                label: 'Total Salaries',
                data: <?php echo json_encode(array_column($department_salaries, 'total_salary')); ?>,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Salary'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Department'
                    }
                }
            },
            animation: {
                duration: 2000,
                easing: 'easeInOutQuart'
            },
            legend: {
                display: false
            },
            tooltips: {
                intersect: false,
                backgroundColor: 'rgba(0,0,0,0.7)',
                titleFontColor: '#fff',
                bodyFontColor: '#fff',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            elements: {
                line: {
                    tension: 0.4 // Smooths the line
                }
            }
        }
    });
});



<?php
$months = array_column($all_months_salaries, 'month');
$month_names = array_map(function($month_num) {
    // Make sure $month_num is an integer
    $month_num = (int)$month_num;
    return date('F', mktime(0, 0, 0, $month_num, 10));
}, $months);
?>

document.addEventListener("DOMContentLoaded", function() {
    var ctx = document.getElementById('monthSalariesChart').getContext('2d');
    var monthSalariesChart = new Chart(ctx, {
        type: 'horizontalBar',
        data: {
            labels: <?php echo json_encode($month_names); ?>,
            datasets: [{
                label: 'Total Salaries',
                data: <?php echo json_encode(array_column($all_months_salaries, 'total_salary')); ?>,
                backgroundColor:['#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
                borderColor:['#119EFA','#15f34f','#ef370dc7','#791db2d1', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263','#6AF9C4','#50B432','#0d91efc7','#ED561B'],
                borderWidth: 1,
                barThickness: 4, // Adjust bar thickness here

            }]
        },
        options: {
            scales: {
                xAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
});
</script>



