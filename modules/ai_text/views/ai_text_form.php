<?php
defined('BASEPATH') or exit('No direct script access allowed');
init_head(); // This initializes the header
?>

<div id="wrapper">
    <div class="content">
            <div class="col-md-12 tw-mb-5">
                <h2 class="text-center text-white mt-2 heading">Custom Template</h2>
            </div>

            <!-- TEMPLATE FORM -->
            <div class="row tw-mb-10 form">    
                <div class="col-md-6 tw-mt-2">
                    <h4 class=" tw-mt-5 tw-py-4 head2 text-center tw-mb-5"><b>Template</b></h4>
                        <form id="save">
                            <div class="form-container tw-mt-5">
                                <label for="Template-Title text-dark" class="form-label ">Template-Title:</label>
                                <input class="form-control" type="text" id="Template-Title" name="title" required>
                            </div>

                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark" for="Template-Description">Template-Description:</label>
                                <input class="form-control" type="text" id="Template-Description" name="description" required>
                            </div>

                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark" for="Template-icon">Template Icon:</label>
                                <input type="text" class="form-control" id="image" name="image">
                            </div>

                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark" for="Template-color">Template Color:</label>
                                <input type="color" class="form-control" id="color" name="color">
                            </div>
                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark" for="Template-color">Template Category:</label>
                                <select class="form-select category" name="category">
                                    <option value="all">All</option>
                                    <option value="blogs">Blog</option>
                                    <option value="ecommerce">E-commerce</option>                                    
                                    <option value="development">Development</option>
                                    <option value="advertisement">Advertisement</option>
                                    <option value="socialmedia">Social Media</option>
                                </select>
                            </div>
                </div>    
            </div>
        
            
            <!-- INPUT FIELDS FORM -->
            <div class="row tw-mt-8 form">
            <div class="col-md-6">
                        <div class="row head2">
                            <div class="col-md-9 tw-m-1 tw-p-0">
                                <h4 class="tw-py-2 tw-ps-5 text-center"><b>Input Groups</b></h4>
                            </div>  
                                <div class="col-md-3 tw-mt-4">
                                    <button type="submit" id="add_form" >
                                    <i class="fa-solid fa-plus"></i></button>
                                    <button type="submit" id="remove_form">
                                    <i class="fa-solid fa-minus"></i></button>   
                                </div>  
                        </div>   
                <div class="form-group" id="form" name="form">
                    <div class="row tw-mt-2 tw-mb-5">
                        <div class="form-container tw-mt-5"> 
                            <label class="form-label">Select Input Type</label>
                                <select class="form-select input_type" name="Input_type" >
                                    <option value="text">Input Field</option>
                                    <option value="textarea">Textarea Field</option>
                                </select>
                        </div>
                        <div class="form-container tw-mt-5">
                            <label class="form-label text-dark iname" for="input name">Input Name:</label>
                            <input type="text" class="form-control inputname" name="Input_name" id="inputname">
                        </div>   
                        <div class="form-container tw-mt-5">
                            <label class="form-label text-dark idescription" for="input description">Input Description</label>
                            <input type="text" class="form-control inputdesc" id="inputdesc" name="Input_desc">
                        </div>   
                    </div>
                </div>
            </div>
            </div>

            <!-- PROMPT FORM -->

                <div class="row form">
                    <div class="col-md-6 tw-mt-2 tw-mb-5">
                        <h4 class="tw-mt-5 tw-py-4 head2 text-center tw-mb-5"><b>Prompt</b></h4>
                            <div class="form-container tw-mt-5">
                                <label for="custom_create text-dark" class="form-label">Created Inputs:</label>
                                <div id="created-inputs" class="form-control text-area" name="created-inputs" required readonly></div>
                            </div>

                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark" for="Custom Prompt">Custom Prompt:</label>
                                <textarea type="text" class="form-control" id="c-prompt" name="c-prompt" rows="5"></textarea>
                            </div>   
                    </div>
                </div>

            <!-- BUTTON -->
            <div class="text-center tw-mt-5">
                <button class="form-button btn btn-info" type="submit">Submit</button>
            </div>
        </form>          
    </div>  
</div>


<?php
init_tail();
?>


    <script>
        $(document).ready(function() {
            $('#add_form').click(function(e) {
                e.preventDefault();
                var formGroup = $('#form').last();
                var allFilled = true;

                formGroup.find('input, select').each(function() {
                    if ($(this).val() === '') {
                        allFilled = false;
                    }
                });

                if (allFilled) {
                    var newFormGroup = formGroup.clone();
                    newFormGroup.find('input, select').val('');
                    formGroup.parent().append(newFormGroup);
                } else {
                    alert('Please fill all fields before adding a new form');
                }
            });
            $('#remove_form').click(function(e) {
                e.preventDefault();
                if ($('.form-group').length > 1) {
                    $('.form-group').last().remove();
                } else {
                    alert('You cannot remove the last form');
                }
            });
        });


        $(document).ready(function() {
            var inputNames = [];

            // Listen for changes in the input name field
            $(document).on('input', '.inputname', function() {
                var inputName = '**' + $(this).val() + '**';
                var index = $('.inputname').index(this); 
                if (inputNames[index]) {
                    // If the input name already exists, update it
                    inputNames[index] = inputName;
                    // Also update the corresponding button text
                    $('#button-' + index).text(inputName);
                    $('#button-' + index).off('click').click(function() {
                $('#c-prompt').val($('#c-prompt').val()+inputName);
            });

                } else {
                    // If the input name does not exist, add it
                    inputNames.push(inputName);
                    // Also create a new button
                    var button = $('<button>').text(inputName).addClass('button-style').attr('id', 'button-' + index).click(function() {
                        $('#c-prompt').val($('#c-prompt').val()+inputName);
                    });
                    $('#created-inputs').append(button);
                }
                var maxHeight = 200;
                var lineHeight = parseInt($(this).css('line-height'), 10);
                var lines = Math.ceil($(this).prop('scrollHeight') / lineHeight);

                if (lines > 1) {
                    var newHeight = (lines * lineHeight) + 20; // Increase the height based on the number of lines
                    $('#created-inputs').css('height', newHeight + 'px');
                } else {
                    var createdInputsHeight = $('#created-inputs').height();
                    $('#created-inputs').css('height', createdInputsHeight + 20 + 'px');
                }
            });
        });


        $(document).ready(function() {
            // adds a submit event listener to the form with the ID
            $('#save').submit(function(e) {
                e.preventDefault();

                var data = {};
                var inputFormsData = [];

                // Serialize the template form
                var templateFormData = $('#save').serializeArray();
                $.map(templateFormData, function(n, i){
                    data[n['name']] = n['value'];
                });

                // Serialize the input forms
                $('.form-group').each(function() {
                    var formData = {};

                    // Get the form data
                    var inputType = $(this).find('.input_type').val();
                    var inputName = $(this).find('.inputname').val();
                    var inputDesc = $(this).find('.inputdesc').val();

                    // Add the form data to the formData object
                    formData['Input_type'] = inputType;
                    formData['Input_name'] = inputName;
                    formData['Input_desc'] = inputDesc;

                    // Add the formData object to the inputFormsData array
                    inputFormsData.push(formData);
                });


                // Convert inputFormsData to a JSON string
                data['inputform'] = JSON.stringify(inputFormsData);

                // Serialize the prompt form
                var promptFormData = $('#save').serializeArray();
                $.map(promptFormData, function(n, i){
                    data[n['name']] = n['value'];
                });

                // Add the CSRF token to the data object
                data[csrfData.token_name] = csrfData.hash;

                // Perform AJAX request to save the data and handle the response
                $.ajax({
                    url: "<?= admin_url('ai_text/save_template') ?>", // Your controller function URL for saving the template
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        // Handle success response
                        alert(response);
                        $('#save')[0].reset();
                    },
                    error: function(xhr) {
                        // Handle error response
                        console.error(xhr);
                    }
                });
            });
        });

    
    </script>
    

<style>

.form{
    display: flex;
    justify-content: center;
}
.head2{
        background-color:#c1d8d9;
        border-radius:8px;
        color:#115455;
        justify-content:center;

    }
.heading{
    font-family: "Lucida" Grande, sans-serif;
    font-size:30px;
    }
.f1{
        border-radius:20px; 
    }
.form-button {
    padding: 10px 20px;
    color: white;
    border: none;
    cursor: pointer; 
    border-radius:10%
}
.form-select {
    width: 100%;
    padding: 0.5rem;
    font-size: 1rem;
    border: 1px solid #ccc;
    border-radius: 0.25rem;
    background-color: #fff;
    color: #333;
}

.form-select option {
    padding: 0.5rem;
}

.button-style {
    border-radius: 5px;
    background-color: #115455;
    color: white;
    border: none;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    margin-left:5px
}
</style>