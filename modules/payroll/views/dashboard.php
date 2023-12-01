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
                        <?php 
                            // Use the current month as the default if $selectedMonth is not set
                            $currentMonth = date('n');
                            for ($i = 1; $i <= 12; $i++):
                                $isSelected = (isset($selectedMonth) ? $selectedMonth : $currentMonth) == $i;
                        ?>
                        <option value="<?php echo $i; ?>" <?= $isSelected ? 'selected' : ''; ?>>
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
        <div class="row">
            <div class="flex justify-end mt-6 ml-4">
                <div>
                <label for="pkrDollarRate">Dollar Rate (PKR):</label>
                <input type="text" id="pkrDollarRate" class="form-control" value="<?= isset($saved_rates['rate_pkr']) ? $saved_rates['rate_pkr'] : '' ?>" oninput="convertToUSD()">

                </div>
                <div class="ml-4">
                <label for="indDollarRate">Dollar Rate (INR):</label>
                <input type="text" id="indDollarRate" class="form-control"  value="<?= isset($saved_rates['rate_ind']) ? $saved_rates['rate_ind'] : '' ?>" oninput="convertToUSDIndia()">

                </div>
                <div class="ml-4">
                <label for="bangDollarRate">Dollar Rate (BDT):</label>
                <input type="text" id="bangDollarRate" class="form-control"  value="<?= isset($saved_rates['rate_bang']) ? $saved_rates['rate_bang'] : '' ?>" oninput="convertToUSDBangladesh()">

                </div>
            </div>
        </div>
		<!-- top cards -->
        <div class="row">
            <div class="flex flex-wrap -mx-2">
                <div class="w-full lg:w-6/12 p-4">
                    <div class="bg-white p-6 rounded-[20px] shadow-xl hover:shadow-2xl border border-gray-200 transition-all duration-500 ease-in-out relative flex items-center justify-center">
                        <div class="mr-6">
                            <p id="total_salaries_for_month" class="text-3xl font-extrabold text-[#0086BE]"></p>
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
                            <p id="total_salaries_pakistan" class="text-3xl font-extrabold text-[#115740]"><?php echo $total_salaries_pak . ' ' . $currency_pak; ?>/ $<span id="usdValue">0</span></p>
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
                        <p id="total_salaries_bangladesh" class="text-3xl font-extrabold text-[#F42A41]">
                            <?php echo $total_salaries_bang . ' ' . $currency_bang; ?> / $<span id="usdValueBangladesh">0</span>
                        </p>

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
                        <p id="total_salaries_india" class="text-3xl font-extrabold text-[#FF671F]">
                            <?php echo $total_salaries_ind . ' ' . $currency_ind; ?> / $<span id="usdValueIndia">0</span>
                        </p>

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
// autosave values in server:

let saveRateTimeout;
function saveExchangeRate(currency) {
    var rate = $('#' + currency + 'DollarRate').val(); // Using jQuery for getting the value
    var selectedMonth = $('#monthSelect').val();
    var currentYear = new Date().getFullYear();

    // Only proceed if there is a rate to save
    if (rate) {
        // Clear any existing timeout to debounce the requests
        clearTimeout(saveRateTimeout);

        // Set a new timeout to save the rate after a delay
        saveRateTimeout = setTimeout(() => {
            var data = {
                month: selectedMonth,
                year: currentYear,
                rate: rate,
                currency: currency,
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            };

            // Using jQuery's ajax method
            $.ajax({
                url: '<?= admin_url('payroll/save_exchange_rate'); ?>',
                type: 'POST',
                data: data,
                success: function(response) {
                    if(response.status === 'success') {
                        console.log('Exchange rate saved');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', status, error);
                }
            });
        }, 500); // Delay in ms before the rate save occurs
    }
}




    
function updateTotalUSDAmount() {
    var pkrRate = document.getElementById('pkrDollarRate').value;
    var totalSalariesPKR = <?= $total_salaries_pak; ?>;
    var totalUSDForPKR = pkrRate ? totalSalariesPKR / pkrRate : 0;

    var bangRate = document.getElementById('bangDollarRate').value;
    var totalSalariesBDT = <?= $total_salaries_bang; ?>;
    var totalUSDForBDT = bangRate ? totalSalariesBDT / bangRate : 0;

    var indRate = document.getElementById('indDollarRate').value;
    var totalSalariesINR = <?= $total_salaries_ind; ?>;
    var totalUSDForINR = indRate ? totalSalariesINR / indRate : 0;

    var totalUSD = totalUSDForPKR + totalUSDForBDT + totalUSDForINR;
    document.getElementById('total_salaries_for_month').textContent = 'Total in USD: $' + totalUSD.toFixed(2);
}



function convertToUSD() {
    var pkrRate = document.getElementById('pkrDollarRate').value; // Get the PKR rate from the input
    var totalSalariesPKR = <?= $total_salaries_pak; ?>; // Your PHP variable containing total salaries
    
    if (pkrRate && totalSalariesPKR) {
        var totalSalariesUSD = totalSalariesPKR / pkrRate; // Perform the conversion
        document.getElementById('usdValue').textContent = totalSalariesUSD.toFixed(2); // Display the result
    } else {
        document.getElementById('usdValue').textContent = '0'; // If no rate is entered, display 0
    }
    updateTotalUSDAmount();
    saveExchangeRate('pkr');
}

function convertToUSDBangladesh() {
    var bangRate = document.getElementById('bangDollarRate').value; // Get the BDT rate from the input
    var totalSalariesBDT = <?= $total_salaries_bang; ?>; // Your PHP variable containing total salaries for Bangladesh
    
    if (bangRate && totalSalariesBDT) {
        var totalSalariesUSD = totalSalariesBDT / bangRate; // Perform the conversion
        document.getElementById('usdValueBangladesh').textContent = totalSalariesUSD.toFixed(2); // Display the result
    } else {
        document.getElementById('usdValueBangladesh').textContent = '0'; // If no rate is entered, display 0
    }
    updateTotalUSDAmount();
    saveExchangeRate('bang');
}


function convertToUSDIndia() {
    var indRate = document.getElementById('indDollarRate').value; // Get the INR rate from the input
    var totalSalariesINR = <?= $total_salaries_ind; ?>; // Your PHP variable containing total salaries for India
    
    if (indRate && totalSalariesINR) {
        var totalSalariesUSD = totalSalariesINR / indRate; // Perform the conversion
        document.getElementById('usdValueIndia').textContent = totalSalariesUSD.toFixed(2); // Display the result
    } else {
        document.getElementById('usdValueIndia').textContent = '0'; // If no rate is entered, display 0
    }
    updateTotalUSDAmount()
    saveExchangeRate('ind');
}


$(document).ready(function() {
    // Check if there are saved rates and if so, trigger the conversion
    if ($('#pkrDollarRate').val()) {
        convertToUSD();
    }
    if ($('#indDollarRate').val()) {
        convertToUSDIndia();
    }
    if ($('#bangDollarRate').val()) {
        convertToUSDBangladesh();
    }
    
    // If there are saved rates, also update the total USD amount
    if ($('#pkrDollarRate').val() || $('#indDollarRate').val() || $('#bangDollarRate').val()) {
        updateTotalUSDAmount();
    }
});
</script>



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
// Assuming $all_months_salaries is now an associative array with each month's total salary in USD
$months = array_keys($all_months_salaries);
$month_names = array_map(function($month_num) {
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
                label: 'Total Salaries in USD',
                data: <?php echo json_encode(array_values($all_months_salaries)); ?>, // Using array_values to get only the salary amounts
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



