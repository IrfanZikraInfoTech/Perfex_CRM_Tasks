<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $campaign->title ?> Application</title>
    
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Hide scrollbar for Chrome, Safari and Opera */
        .noscroll::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .noscroll {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
        }
    </style>
    
</head>
<body class="relative bg-gray-100">
<?php
$default_bg_color = "#1da6e7";  // White color
$default_button_color = "#e9b328";  // Grey color

$bg_color = isset($colorScheme['background_color']) ? $colorScheme['background_color'] : $default_bg_color;
$button_color = isset($colorScheme['button_color']) ? $colorScheme['button_color'] : $default_button_color;
?> 

<a href="https://zikrainfotech.com"><div id="logo" class="bg-layer-1 md:absolute mx-auto md:inset-x-auto inset-x-0 mb-2 xl:w-64 lg:w-48 w-48 md:pl-6 pt-2 z-10">
        <img src="<?= base_url('uploads/company/' . get_option('company_logo')) ?>" class="img-responsive" alt="Zikra Infotech LLC">
            </div></a>

<div class="flex flex-col items-center md:justify-center parallax p-4 2xl:pt-[20px] xl:pt-[60px] md:pt-[120px]" >
    
    <form class="bg-white lg:p-8 p-2 border-4 relative  rounded-3xl shadow-2xl  w-full max-w-4xl parallax">
     
        <div class="flex md:flex-row flex-col md:items-center items-end md:justify-center md:mb-8 mb-2 md:mt-4 md:gap-6 gap-2">
            
            <div class="md:absolute md:w-[inherit] w-full left-8 font-mono transition-all md:text-2xl text-lg md:text-center md:pl-0 pl-4" >
            <a href="<?= base_url("career/"); ?>" 
   class="w-fit md:mx-auto rounded-lg p-1 transition-all ease-in-out duration-200 transform hover:scale-105 hover:shadow-lg hover:skew-x-2 border-2 border-solid focus:outline-none focus:ring focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-<?= str_replace('#', '', $button_color) ?>"
   style="background-color: <?= $bg_color ?>; color: white; border-color: <?= $bg_color ?>;"
   onmouseover="this.style.backgroundColor='white'; this.style.color='<?= $bg_color ?>'; this.style.borderColor='transparent';"
   onmouseout="this.style.backgroundColor='<?= $bg_color ?>'; this.style.color='white'; this.style.borderColor='<?= $bg_color ?>';"
>Back</a>

            </div>

            <a href="<?= base_url("career/view/".$campaign->id) ?>" 
   class="max-w-[70%] font-mono transition-all md:text-3xl text-xl parallax text-center w-fit mx-auto rounded-lg p-2 transition-all ease-in-out duration-200 transform hover:scale-105 hover:shadow-lg hover:skew-x-2 border-2 border-solid focus:outline-none focus:ring focus:ring-offset-2 focus:ring-offset-gray-100 focus:ring-<?= str_replace('#', '', $button_color) ?>"
   style="background-color: <?= $bg_color ?>; color: white; border-color: <?= $bg_color ?>;"
   onmouseover="this.style.backgroundColor='white'; this.style.color='<?= $bg_color ?>'; this.style.borderColor='transparent';"
   onmouseout="this.style.backgroundColor='<?= $bg_color ?>'; this.style.color='white'; this.style.borderColor='<?= $bg_color ?>';">
  <div class="w-fit mx-auto">
    <?= $campaign->position; ?>
  </div>
</a>

            
        </div>

            <h2 class="text-2xl mb-4 text-center"></h2>
            <div class="">

            <input type="hidden" name="campaign_id" value="<?php echo $campaign->id; ?>">
            <input type="hidden" name="<?= $csrf_name; ?>" value="<?= $csrf_token; ?>">

                <?php
                $fieldsData = json_decode($form_fields[0]->fields_data, true);
                foreach ($fieldsData as $field):

                    $fieldType = $field['type'];
                    $fieldName = $field['name'];
                    $fieldOptions = '';

                    if (isset($field['options'])) {
                        $fieldOptionsArray = json_decode($field['options'], true);
                    }

                    $fieldMin = isset($field['min']) ? $field['min'] : '';
                    $fieldMax = isset($field['max']) ? $field['max'] : '';
                    $fieldMaxLength = isset($field['maxLength']) ? $field['maxLength'] : '';
                ?>

                <div class="mb-4">
                    <label for="<?php echo $fieldName; ?>" class="block mb-2"><?php echo ucfirst($fieldName); ?></label>
                    <?php
                    switch ($fieldType) {
                        case 'text':
                            if ($fieldName == 'Phone Number') {
                                echo '<div style="display: inline-flex; border: 1px solid #ccc; border-radius: 4px; width: 100%;">
                                        <div style="background-color: #f0f0f0; padding: 0.375rem 0.75rem; border-right: 1px solid #ccc;">+</div>
                                        <input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border-0" type="tel" placeholder="Enter ' . ucfirst($fieldName) . '" maxlength="' . $fieldMaxLength . '" required style="flex-grow: 1;">
                                      </div>';
                            } else {
                                echo '<input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded" type="text" placeholder="Enter ' . ucfirst($fieldName) . '" maxlength="' . $fieldMaxLength . '" required>';
                            }
                        break;
                        case 'email':
                            echo '<input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded" type="email" placeholder="Enter ' . ucfirst($fieldName) . '" required>';
                            break;
                        case 'date':
                            echo '<input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded" type="date" placeholder="Enter ' . ucfirst($fieldName) . '" maxlength="' . $fieldMaxLength . '" required>';
                            break;
                        case 'textarea':
                            echo '<textarea required id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded" placeholder="Enter ' . ucfirst($fieldName) . '"></textarea>';
                            break;
                        case 'numbers':
                            echo '<input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded" type="number" placeholder="Enter ' . ucfirst($fieldName) . '" min="' . $fieldMin . '" max="' . $fieldMax . '" required>';
                            break;
                        case 'select':
                            echo '<select id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded">';
                            foreach ($fieldOptionsArray as $option) {
                                echo '<option value="' . trim($option) . '">' . trim($option) . '</option>';
                            }
                            echo '</select>';
                            break;
                        case 'checkbox':
                            foreach ($fieldOptionsArray as $option) {
                                echo '<div class="flex items-center mb-2">';
                                echo '<input id="' . $fieldName . '" name="' . $fieldName . '[]" class="genInput mr-2" type="checkbox" value="' . trim($option) . '">';
                                echo '<label for="' . $fieldName . '">' . trim($option) . '</label>';
                                echo '</div>';
                            }
                            break;
                        case 'radio':
                            foreach ($fieldOptionsArray as $option) {
                                echo '<div class="flex items-center mb-2">';
                                echo '<input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput mr-2" type="radio" value="' . trim($option) . '" required>';
                                echo '<label for="' . $fieldName . '">' . trim($option) . '</label>';
                                echo '</div>';
                            }
                            break;
                        default:
                            break;
                    }
                    ?>
                </div>
                                
                <?php endforeach; ?>

                <div class="mb-4">
                    <label for="resume" class="block mb-2">Resume</label>
                    <input required id="resume" class="w-full p-2 border border-gray-300 rounded" type="file" accept="application/pdf" name="resume">
                </div>

                <button type="submit" 
        class="transition-all duration-150 ease-linear focus:outline-none py-3 px-4 rounded-lg text-lg w-full hover:shadow-lg border border-solid"
        style="background-color: <?= $button_color ?>; color: white; border-color: <?= $button_color ?>;"
        onmouseover="this.style.backgroundColor='white'; this.style.color='<?= $button_color ?>'; this.style.borderColor='<?= $button_color ?>';"
        onmouseout="this.style.backgroundColor='<?= $button_color ?>'; this.style.color='white'; this.style.borderColor='<?= $button_color ?>';">
  Submit
</button>


            </div>
        </form>

    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener('mousemove', function(e) {

        const parallaxItems = document.querySelectorAll('.parallax');

        parallaxItems.forEach(element => {
            const str = element.getAttribute("str");
            const xPos = (window.innerWidth / 2 - e.clientX) / str;
            const yPos = (window.innerHeight / 2 - e.clientY) / str;
            element.style.transform = `translate(${xPos}px, ${yPos}px)`;
        });
        

        });
    </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function () {
        $('form').on('submit', function (e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Processing...',
                text: 'Submitting Application...',
                timerProgressBar: true,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            // Create a FormData object to handle file uploads
            const formData = new FormData(this);

            $.ajax({
                url: '<?php echo admin_url('recruitment_portal/handle_submission_skip_auth'); ?>',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false, // Don't process the data
                contentType: false, // Don't set content type, let the browser handle it
                success: function (response) {

                    //console.log(response);
                    Swal.close();
                
                    if(response.success){
                        $('.genInput').val('');

                        Swal.fire({
                            title: 'Success',
                            text: 'Application Submitted',
                            icon: 'success', // can be 'success', 'error', 'warning', 'info', or 'question'
                            confirmButtonText: 'Cool'
                        });

                    }else{
                        Swal.fire({
                            title: 'Failed',
                            text: 'Submission Failed: '+response.message,
                            icon: 'error', // can be 'success', 'error', 'warning', 'info', or 'question'
                            confirmButtonText: 'Try Again'
                        });
                    }


                },
                error: function (response) {
                    // Handle error (e.g., display an error message)
                }
            });
        });
    });
</script>

</body>
</html>