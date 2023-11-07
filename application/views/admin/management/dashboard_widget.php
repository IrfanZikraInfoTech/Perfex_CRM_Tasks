
 <style>
    .staff-profile-image-container {
  position: relative;
  display: inline-block;
}

.birthday-cap {
  width: 0;
  height: 0;
  border-left: 35px solid transparent;
  border-right: 35px solid transparent;
  border-bottom: 50px solid #00b4d8;
  position: absolute;
  top: -25px;
  left: 15%;
  transform: translateX(-50%) rotate(-35deg); /* Tilt the cap to the left */
  z-index: 1;
  /* Improved gradient for a fabric effect */
  background-image: linear-gradient(to bottom right, #00b4d8, #48cae4,#90e0ef); /* Pink gradient for a festive look */
  /* Subtle inner shadow for a plush look */
}

.birthday-cap::before {
  content: '';
  position: absolute;
  top: 50px; /* Adjust if necessary to align with the bottom of the cap */
  left: 50%; /* Center under the cap */
  transform: translateX(-50%) rotate(-2deg); /* Tilt to match the cap */
  width: 70px; /* Adjust to match the width of the cap */
  height: 10px; /* Height of the brim */
  background-color: gold; /* The color of the brim */
  z-index: 2;
  box-shadow: inset 0 1px 2px rgba(255, 255, 255, 0.6), 0 2px 4px rgba(0, 0, 0, 0.2);
  background-image: linear-gradient(to right, #f1c40f, #f39c12); /* Shiny effect */
}

.birthday-cap::after {
  content: '';
  position: absolute;
  top: -15px; /* Position it above the cap */
  left: 50%;
  transform: translateX(-50%);
  width: 30px; /* Size of the pom-pom */
  height: 30px; /* Size of the pom-pom */
  background-color: #fff;
  border-radius: 50%;
  z-index: 3;
  /* Add a radial gradient to give the impression of depth */
  background-image: radial-gradient(circle at 15px 15px, #f8f9fa, #e9ecef 70%, #dee2e6);
  /* Multiple shadows for a fluffy effect */
  box-shadow: 
    0 2px 4px rgba(0, 0, 0, 0.15),
    inset 0 2px 4px rgba(255, 255, 255, 0.4),
    inset 0 -3px 4px rgba(0, 0, 0, 0.1);
}


.balloon-container {
  margin-left: auto; /* This will push the balloon to the right */
}

</style> 



<div class=" flex lg:flex-row flex-col justify-between relative gap-5">


<div class="w-full bg-white rounded-[50px] p-6 shadow-lg hover:shadow-xl border border-solid border-white hover:border-<?= get_option('management_theme_border') ?> transition-all b-doodle">
  <div class="flex flex-col xl:flex-row items-center justify-between">
    <div class="flex flex-col xl:flex-row items-center xl:justify-start justify-center flex-grow">
      <div class="staff-profile-image-container flex items-center"> <!-- Added flex items-center -->
        <?php 
        echo staff_profile_image($GLOBALS['current_user']->staffid, ['h-full', 'w-44', 'object-cover', 'xl:mr-4', 'xl:ml-0', 'mx-auto', 'xl:self-start', 'staff-profile-image-thumb'], 'thumb');
        ?>
        
        <!-- Moved birthday elements into the same container -->
        <?php if ($isMyBirthday): ?>
          <div class="birthday-cap"></div>
         <?php endif; ?>
      </div>
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
      <?php if ($is_birthday): ?>
      <div class="balloon-container self-center xl:self-start">
        <!-- <img src="<?php echo base_url('uploads/company/ballon.png') ?>" alt="Happy Birthday" class="balloon-image w-44 h-auto xl:ml-4"> -->
      </div>
    <?php endif; ?>
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
                " class=" z-20  px-2 text-xl transition-all shadow-lg rounded-full font-semibold text-center w-auto mx-auto " onchange="statusSelectColors(this);" id="status">
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