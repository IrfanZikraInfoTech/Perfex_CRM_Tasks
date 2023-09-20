<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responsive Form</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .bg-layer-1 {
            z-index: 2;
        }
        .bg-layer-2 {
            z-index: -4;
        }
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
<body class="relative overflow-hidden bg-gray-100">


    <a href="../"><img src="https://i.ibb.co/RhSFc27/Zikra-Infotec-for-web-png.png" alt="ZIT LLC" class="bg-layer-1 absolute mx-auto mb-4 w-80 parallax" str="30"></a>
 
    <div class="bg-layer-2 absolute w-full h-full text-green-800 parallax p-20 flex items-end justify-end" str="-40">###</div>

    <div class="min-h-screen flex flex-col items-center justify-center parallax" str="10" >
        
        <form class="bg-white mt-[50px] p-8 border-4 relative border-double border-yellow-500 rounded-3xl shadow-lg shadow-white w-full max-w-4xl parallax" str="-30" method="post">
        
        <div class="flex flex-row items-center justify-center mb-8">
            
            <div class="absolute left-8 font-mono transition-all text-2xl parallax text-center" str="100"><a href="<?= base_url("career/"); ?>" class="w-fit mx-auto rounded p-2 hover:bg-blue-400 transition-all hover:text-white underline hover:skew-x-2 decoration-double decoration-white hover:decoration-yellow-500 border border-solid">Back</a></div>

            <div class="font-mono transition-all text-3xl parallax text-center" str="100"><div class="w-fit mx-auto rounded p-2 hover:bg-blue-400 transition-all hover:text-white underline hover:skew-x-2 decoration-double decoration-white hover:decoration-yellow-500 border border-solid"><?= $campaign->position; ?></div></div>

        </div>

            <h2 class="text-2xl mb-4 text-center"></h2>
            <div class="overflow-y-scroll noscroll max-h-[70vh]">

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
                            echo '<input id="' . $fieldName . '" name="' . $fieldName . '" class="genInput transition-all hover:scale-[0.99] w-full p-2 border border-gray-300 rounded" type="text" placeholder="Enter ' . ucfirst($fieldName) . '" maxlength="' . $fieldMaxLength . '" required>';
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

                
                <button class="bg-sky-500 hover:bg-sky-700  transition-all duration-150 ease-linear shadow outline-none bg-pink-500 hover:bg-pink-600 hover:shadow-lg focus:outline-none text-white py-3 px-4 rounded-lg text-lg w-full" type="submit">Submit</button>
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

            // Create a FormData object to handle file uploads
            const formData = new FormData(this);

            $.ajax({
                url: '<?php echo admin_url('recruitment_portal/handle_submission'); ?>',
                type: 'POST',
                data: formData,
                processData: false, // Don't process the data
                contentType: false, // Don't set content type, let the browser handle it
                success: function (response) {

                    console.log(response);
                
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