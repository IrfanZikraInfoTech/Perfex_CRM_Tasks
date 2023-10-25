<div class=" flex lg:flex-row flex-col justify-between relative gap-5">


    <div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all">

        <div class="flex items-center xl:flex-row flex-col lg:justify-start justify-center">
            <?php echo staff_profile_image($GLOBALS['current_user']->staffid, ['h-full', 'w-44' , 'object-cover', 'xl:mr-4' , 'xl:ml-0 mx-auto self-start' , 'staff-profile-image-thumb'], 'thumb') ?>
            <div class="flex flex-col gap-1 xl:items-start items-center">

                <div class="text-xl font-semibold flex flex-row justify-between">

                    <div class="flex items-center">Hi, <?php echo $GLOBALS['current_user']->firstname; ?>! 👋</div>                    

                </div>
                <p class="text-lg">Welcome to your dashboard.</p>

                <div class="my-2" id="shiftInfo">Upcoming Shift: </div>

                <div class="flex flex-row gap-2">
                    <button class="px-3 py-1 text-base bg-blue-600 rounded-3xl text-white transition-all shadow-lg hover:shadow-none" id="clock-in">Clock in</button>
                    <button class="px-3 py-1 text-base bg-blue-600 rounded-3xl text-white transition-all shadow-lg hover:shadow-none" id="clock-out">Clock Out</button>
                </div>
            </div>
        </div>
    </div>

    <div class="xl:w-[30%] w-full flex flex-row md:text-right shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border')?> transition-all text-center md:mt-0 mt-5 justify-between bg-white rounded-[50px] p-6 relative">


        <div class="h-[90%] inset-0 my-auto z-10 absolute opacity-40">

            <canvas id="progressChart"></canvas>

        </div>

        <div class="flex flex-col gap-3 w-full justify-center">

            <select style="border:0px;
                outline:0px;
                background-color:white;
                background:none;
                -webkit-appearance:none;
                appearance:none;
                " class=" z-20  px-2 text-xl transition-all hover:shadow-lg rounded-full font-semibold text-center w-auto mx-auto " onchange="statusSelectColors(this);" id="status">
                <option id="Online" value="Online" class="text-lime-500">Online</option>
                <option id="AFK" value="AFK" class="text-blue-500">AFK</option>
                <option id="Offline" value="Offline" class="text-pink-500">Offline</option>
                <option id="Leave" value="Leave" class="text-amber-600">Leave</option>
            </select>

            <div class="w-full">
                <h2 class="text-3xl font-bold text-center " id="live-timer">
                    00:00:00
                </h2>
            </div>
            <h2 class="text-xl font-semibold text-center text-gray-400" id="clock-in-time">
                0h 0m
            </h2>

        </div>

    </div>
</div>

   

<script>

var ctx = document.getElementById('progressChart').getContext('2d');

var clock_in = <?= $total_time ?>;
var total = <?= $shift_seconds ? $shift_seconds : 27000 ?>;

function secondsToHm(seconds) {
    var hours = Math.floor(seconds / 3600);
    var remainingSeconds = seconds % 3600;
    var minutes = Math.floor(remainingSeconds / 60);
    return hours + "h " + minutes + "m";
}

var chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [clock_in, ( (clock_in < total) ? (total - clock_in) : 0)],
            backgroundColor: ['#4caf50', '#e5e5e5'],
            borderWidth: 0,
            borderAlign: 'inner'
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '85%',
        plugins: {
            tooltip: {
                callbacks: {
                    label: ((tooltipItem, data) => {
                        return secondsToHm(tooltipItem.raw);
                    })
                }
            }
        }
    }
});

function updateTimerChart(){
    chart.data = {
        datasets: [{
            data: [clock_in, ( (clock_in < total) ? (total - clock_in) : 0)],
            backgroundColor: ['#4caf50', '#e5e5e5'],
            borderWidth: 0,
            borderAlign: 'inner'
        }]
    };
    chart.update('none');
}

</script>