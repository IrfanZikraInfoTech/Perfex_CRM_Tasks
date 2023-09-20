<?php
init_head();?>

<div id="wrapper">
    <div class="content">
        <div class="col-md-12 tw-mb-5">
            <p>Generate high quality code in seconds.</p>
            <h2 class="text-white mt-2 heading"><?php echo $record['template_name'] ?></h2>
        </div>
        

        <hr style="width:100%;text-align:left;margin-left:0;border:1px solid #ced4da">


            <!-- TEMPLATE FORM -->
            <div class="row">    
                <div class="col-md-5">
                    <form id="prompt">
                    <input type="hidden" id="hiddenInput" name="id"  value="">
                        <div class="form-container tw-mt-5">

                            <?php 
                            if ($record && isset($record['inputform'])) {
                                foreach ($record['inputform'] as $input_field) {
                                    if (isset($input_field['Input_name'])) {
                                        $input_name = $input_field['Input_name'];
                                        ?>
                                        <label class="form-label text-dark tw-mt-5"><?php echo $input_name; ?></label>
                                        <?php if ($input_field['Input_type'] === 'textarea') { ?>
                                        <textarea class="form-control" row="5" name="<?php echo $input_name; ?>"></textarea>
                                            <?php } else { ?>
                                                <input type="text" class="form-control" name="<?php echo $input_name; ?>">
                                            <?php } ?>
                                            <?php
                                }
                            }
                        }
                        ?>
                        </div>
                        <div class="row">    
                          <div class="col-md-6 tw-px-0">
                            <div class="form-container tw-mt-5">
                                <label class="form-label">Maximum Length</label>
                                <input type="number" class="form-control" id="maximum_length"
                                    name="maximum_length"
                                    placeholder="Maximum character length of text" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark">Tone of Voice</label>
                                    <select class="form-select" id="tone_of_voice" name="tone_of_voice" required>
                                        <option value="Funny">Funny</option>
                                        <option value="Casual">Casual</option>
                                        <option value="Excited">Excited</option>
                                        <option value="Professional" selected>Professional</option>
                                        <option value="Witty">Witty</option>
                                        <option value="Sarcastic">Sarcastic</option>
                                        <option value="Feminine">Feminine</option>
                                        <option value="Masculine">Masculine</option>
                                        <option value="Bold">Bold</option>
                                        <option value="Dramatic">Dramatic</option>
                                        <option value="Grumpy">Grumpy</option>
                                        <option value="Secretive">Secretive</option>
                                    </select>
                            </div>
                          </div> 
                        </div>
                        <div class="row">    
                          <div class="col-md-6 tw-px-0">
                            <div class="form-container tw-mt-5">
                                <label class="form-label">Number of Results</label>
                                <input type="number" class="form-control" id="number_of_results"
                                    name="number_of_results" value="1"
                                    placeholder="Number of results" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-container tw-mt-5">
                                <label class="form-label text-dark">Creativity</label>
                                    <select class="form-select" id="creativity" name="creativity" required>
                                        <option value="0.5">Average</option>
                                        <option value="0.75">Good</option>
                                        <option value="1" selected>Premium</option>
                                    </select>
                            </div>
                          </div> 
                        </div>
                            <!-- BUTTON -->
                        <div class="text-center tw-mt-9">
                            <button class="form-button btn btn-info btn-md" type="submit">Generate</button>
                        </div>
                    </form>
                </div> 

                <div class="col-md-1 tw-mt-5">
                    <div class="vl"></div>
                </div>

                <div class="col-md-6 tw-mt-2">
                    <textarea id="mytextarea"></textarea>
                    <div class="text-center tw-mt-9">
                        <button class="form-button btn btn-warning btn-md" type="button" id="savebtn" >Save</button>
                    </div>
                </div>
            </div>  
        </div>
    </div>
</div>


<?php
init_tail();
?>

<script>
    // Saving Form DATA
    $(document).ready(function() {
        $('#prompt').on('submit', function(e) {
            e.preventDefault();

            var data = {};

            var FormData = $('#prompt').serializeArray();
            $.map(FormData, function(n, i){
                data[n['name']] = n['value'];
            });

            data['id'] = '<?php echo $id; ?>';

            data[csrfData.token_name] = csrfData.hash;
            
            $.ajax({
                url: '<?php echo admin_url('ai_text/form_submission'); ?>',
                type: 'post', 
                data: data,
                success: function(response) {
                    tinymce.get('mytextarea').setContent(response);
                    alert_float("info:", "Generated Data");

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);
                }

            });
        });
    });

    // Saving Tinymce Data
    $(document).ready(function() {
        $('#savebtn').click(function() {
            
            var generatedData = tinymce.get('mytextarea').getContent(); // Get the generated data from TinyMCE
            var templateName = "<?php echo $record['template_name']; ?>";
            

            $.ajax({
                url: '<?php echo admin_url('ai_text/savegeneratedData')?>',
                type: "POST",
                data: {
                    generatedData: generatedData,
                    templateName: templateName,
                },
                success: function(response) {
                    alert(response)     },
                error: function(xhr) {
                    console.error(xhr);
                }
            });
        });
    });

    tinymce.init({
        selector: '#mytextarea',
        height:400,
        plugins: 'textcolor advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code ',
        toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | forecolor',
    });


</script>

<style>
.tox-tinymce {
  border: none !important;
}
    .vl{
    border-right:2px solid #ced4da;
    margin-right:30px;
    height: 600px;
    }
.heading{
    font-family: "Lucida" Grande, sans-serif;
    font-size:30px;
    font-weight:bolder
    }

.form-button {
    padding: 10px 20px;
    color: white;
    border: none;
    cursor: pointer; 
    border-radius:15px;
    width:100%;
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

</style>