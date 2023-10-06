<?php 
function adf($seconds) {
    $hours = floor($seconds / 3600);
    $seconds %= 3600;
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;

    return str_pad($hours, 2, '0', STR_PAD_LEFT) . ':' .
           str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' .
           str_pad($remainingSeconds, 2, '0', STR_PAD_LEFT);
}

?>

<style>
.dot-container {
  transition: all 0.2s ease;
}

.dot {
  transition: all 0.2s ease;
}

.dot:hover + .info {
  display: block;
}

.info {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  white-space: nowrap;
}


</style>

<div class="bg-white shadow rounded-lg p-4 my-4 flex md:flex-row flex-col justify-between relative overflow-hidden">

    <div id="snooze-indicator" class="absolute w-full min-h-full top-0 left-0 bg-[#000]/70 z-10" style="display:none;">
        <div class="flex w-full h-full justify-center items-center my-auto absolute">
            <h3 class="text-xl text-white" style="font-family:monospace;">Tab is snoozed, Pls refresh ;</h3>
        </div>
    </div>

    <div class="md:w-3/5 w-full">

        <div class="flex items-center md:flex-row flex-col">
            <?php echo staff_profile_image($GLOBALS['current_user']->staffid, ['h-full', 'w-32' , 'object-cover', 'md:mr-4' , 'md:ml-0 mx-auto self-start' , 'staff-profile-image-thumb'], 'thumb') ?>
            <div class="flex flex-col gap-1 md:items-start items-center">

                <div class="text-xl font-semibold flex flex-row justify-between">

                    <div class="flex items-center">Hi, <?php echo $GLOBALS['current_user']->firstname; ?>! 👋</div>                    

                </div>
                <p class="text-lg">Welcome to your dashboard.</p>

                <div class="w-fit flex flex-row justify-between border border-slate-300 border-double pl-2 text-lg rounded shadow-md transition-all hover:shadow-none">

                    <div class="pr-2">Status: </div>

                    <select class="px-2 bg-transparent text-lime-500 appearance-none cursor-pointer " onchange="statusSelectColors(this);" id="status">
                        <option id="Online" value="Online" class="text-lime-500">Online</option>
                        <option id="AFK" value="AFK" class="text-blue-500">AFK</option>
                        <option id="Offline" value="Offline" class="text-pink-500">Offline</option>
                        <option id="Leave" value="Leave" class="text-amber-600">Leave</option>
                    </select>


                </div>

                <div class="my-2" id="shiftInfo">Upcoming Shift: </div>

                <div class="flex flex-row gap-2">
                    <button class="px-2 py-1 text-base bg-blue-600 rounded text-white transition-all shadow-lg hover:shadow-none" id="clock-in">Clock in</button>
                    <button class="px-2 py-1 text-base bg-blue-600 rounded text-white transition-all shadow-lg hover:shadow-none" id="clock-out">Clock Out</button>
                </div>
            </div>
        </div>
    </div>

    <div class="md:w-2/5 w-full flex flex-col md:text-right text-center md:mt-0 mt-5 justify-between">

        <div>
            <h2 class="text-xl font-semibold" id="live-timer">
                    <?php //echo formatTime($stats->total_time); ?>
            </h2>
        </div>

        <div class="flex flex-col gap-2">
            <h3 class="text-md">Today: <span id="today-timer"></span></h3>
            <h3 class="text-md">Yesterday: <span id="yesterday-timer"></span></h3>
            <h3 class="text-md">This Week: <span id="current-week-timer"></span></h3>
            <h3 class="text-md">Last Week: <span id="last-week-timer"></span></h3>
        </div>
    </div>
</div>

