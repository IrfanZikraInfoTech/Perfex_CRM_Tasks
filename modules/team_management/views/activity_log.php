<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper" class="wrapper">
    <div class="content flex flex-col">

        <div class="bg-white flex flex-col gap-4 rounded">
            <h2 class="text-xl text-center py-2">All Activites of <?php echo $staff?></h2>
            <div class="align-middle inline-block min-w-full">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">S.No </th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity Type</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        <?php $i =0; foreach ($activities as $activity): $i++;?>
                        <tr class="hover:bg-gray-200/30 transition-all">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $i; ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $activity['activity_type']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php $dateTime = new DateTime($activity['time']); $readableDate = $dateTime->format('l, F j, Y \a\t g:i A'); echo $readableDate ; ?></td>
                        </tr>
                        <?php endforeach; ?>

                    </tbody>

                </table>
            </div>
        </div>
    </div>
</div>


<?php init_tail(); ?>

</body>
</html>