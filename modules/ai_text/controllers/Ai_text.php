<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Ai_text extends AdminController

{

    public function __construct()

    {

        parent::__construct();



        $this->load->model('ai_text_form_model');



        }



    // TEMPLATE_FORM

    public function ai_form()

    {

        $this->load->view('ai_text_form');

    }



    // ADDING TEMPLATE DATA IN DATABASE

    public function save_template()

    {

                                                                                                                               

        $data = array(                                                                                                      

            'template_name' => $this->input->post('title'),

            'template_description' => $this->input->post('description'),

            'template_icon' => $this->input->post('image'),

            'template_color' => $this->input->post('color'),

            'template_category'  => $this->input->post('category'),

            'inputform' => $this->input->post('inputform'),

            'custom_prompt' => $this->input->post('c-prompt'),



        );  



        $insert_id = $this->ai_text_form_model->savetemplate($data);                                                           

        echo 'Template created successfully';



    }



    // DISPLAYING TEMPLATE DATA IN DATABASE

    public function display_data()

    {

        $data['saved_data'] = $this->ai_text_form_model->get_saved_data();                                                          // Retrieve the data from the model 

        $this->load->view('ai_texts', $data);                                                                                       // Load the view file to display the data

    }



    //DISPLAYING TEMPLATE PAGE WITH ID

    public function generate_text($id)

    {

        

        $record = $this->ai_text_form_model->saved_data($id);

        if ($record) {

            $record['inputform'] = json_decode($record['inputform'], true);

        }



        $data['record'] = $record;

        $data['id'] = $id;                                                                                                                  



        $this->load->view('AI_generate_text', $data);

        

    }





    // SUBMITTING PROMPT FORM TO GENERATE DATA

    public function form_submission()

    {



        $form_data = $this->input->post();

        $id = $form_data['id'];

        $record = $this->ai_text_form_model->saved_data($id);

        $custom_prompt = $record['custom_prompt'];

    

        foreach ($form_data as $key => $value) {

            $placeholder = '**'  . str_replace('_', ' ', ucfirst($key)) . '**';

            $custom_prompt = str_replace($placeholder, $value, $custom_prompt);

        }



        $custom_prompt .= "\nTone of voice: " . $form_data['tone_of_voice'];



        $maximum_length = intval($form_data['maximum_length']);

        $number_of_results = intval($form_data['number_of_results']);

        $creativity = $form_data['creativity'];

    

        $custom_prompt .= "Give the output using formatting tags in HTML.";

        $gpt_response = $this->call_gpt($custom_prompt, $maximum_length, $number_of_results, $creativity);

        

        echo $gpt_response;

    }



    // CALL TO GPT

    function call_gpt($query, $maxTokens, $number_of_results, $creativity)

    {

        $headers = [

            'Content-Type: application/json',

            'Authorization: Bearer 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'

        ];

        

        $messages = [

            ["role" => "system", "content" => "You are supposed to help in content generation."],

            ["role" => "user", "content" => $query]

        ];

        

        

        $data = [

            'model' => 'gpt-4',

            'messages' => $messages,

            'max_tokens' =>intval($maxTokens),

            'top_p' => 0.8,

            'n' => intval($number_of_results),

        ];

        

        if ($creativity === "High") {

            $data['temperature'] = 0.8;

        } elseif ($creativity === "Low") {

            $data['temperature'] = 0.2;

        } else {

            $data['temperature'] = 0.5; 

        }



        $ch = curl_init('https://api.openai.com/v1/chat/completions');

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        

        $response = curl_exec($ch);

        curl_close($ch);

        



        $response_data = json_decode($response, true);

        $gpt_response = $response_data['choices'][0]['message']['content'];



        return $gpt_response;

    }

    

    // SAVING GENERATED DATA 

    public function savegeneratedData() 

    {

        $generatedData = $this->input->post('generatedData');

        $templateName = $this->input->post('templateName');

    

        $this->ai_text_form_model->saveGeneratedData($generatedData, $templateName);   

        echo "Data saved successfully!";

    }

    

    // DISPLAYING GENERATED DATA

    public function display_gen_data() 

    {        

        $data['gen_data'] = $this->ai_text_form_model->generated_data(); 

        $this->load->view('ai_generated_data',$data);



    }



    // DELETE GENERATED DATA

    public function delete_gen_Data() {

        $id = $this->input->post('id');

        $result = $this->ai_text_form_model->deleteData($id); // Call the model method to delete the data



        if ($result) {

            echo "Data deleted successfully!";

        } else {

            echo "Error deleting data.";

        }

    }



    // MANAGING ALL TEMPLATES 

    public function manage_template()

    {

        $data['saved_data'] = $this->ai_text_form_model->get_saved_data();

        $this->load->view('manage_template', $data);  

    }



    // EDIT TEMPLATES

    public function edit($id)

    {

        $form_data = $this->ai_text_form_model->editdata($id);



        $inputFormData = $form_data->inputform;

        $inputForm = json_decode($inputFormData);

    

        $data['form_data'] = $form_data;

        $data['inputForm'] = $inputForm;

        $data['id'] = $id;                                                                                                                  // Add the ID to the $data array



        $this->load->view('edit_template', $data);

    }



    // UPDATING EDIT TEMPLATE DATA

    public function updateTemplateData() 

    {

        $data = array(                                                                                                      

            'template_name' => $this->input->post('title'),

            'template_description' => $this->input->post('description'),

            'template_icon' => $this->input->post('image'),

            'template_color' => $this->input->post('color'),

            'template_category'  => $this->input->post('category'),

            'inputform' => $this->input->post('inputform'),

            'custom_prompt' => $this->input->post('c-prompt'),



        ); 

                

        $id = $this->input->post('id');   



        $this->ai_text_form_model->updateTemplateData($id, $data);



    }



    // DELETE TEMPLATE

    public function delete_template() {

        $id = $this->input->post('id');

        $result = $this->ai_text_form_model->deletetemplate($id);



        if ($result) {

            echo "Data deleted successfully!";

        } else {

            echo "Error deleting data.";

        }

    }





    // IMAGE GENERATOR

    public function ai_image()

    {

        $this->load->view('ai_image');

    }



    // PROMPT TO GENERATE IMAGE

    public function generate_image() {



        $idea = $this->input->post('idea');

        $resolution = $this->input->post('resolution');

        $lighting = $this->input->post('lighting');

        $style = $this->input->post('style');

        $numImages = $this->input->post('numImages');



        $prompt = "A {$lighting}, {$style} representation of {$idea}";



        $response_data = $this->call_openai_api($prompt, $numImages, $resolution);



        $image_urls = array();

        foreach ($response_data['data'] as $image_data) {

            $image_url = $image_data['url'];

            $image_id = $this->ai_text_form_model->save_image($image_url, $prompt);

            $image_urls[] = $image_url;

        }



        echo json_encode($image_urls);

    }



    // CALL TO GPT TO GENERATE IMAGE

    private function call_openai_api($prompt, $numImages, $resolution)  {

        

        $headers = [

            'Content-Type: application/json',

            'Authorization: Bearer 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'

        ];

        

        

        $postFields = [

            'prompt' => $prompt,

            'n' => intval($numImages),

            'size' => $resolution,

        ];



        $ch = curl_init('https://api.openai.com/v1/images/generations');

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

        



        $response = curl_exec($ch);

        curl_close($ch);

    

        $response_data = json_decode($response, true);

    

        return $response_data;

    

    }



    // DISPLAY IMAGE ON VIEW FILE

    public function display_image($id) {



        $image = $this->ai_text_form_model->get_image($id);



        if ($file_path === null) {

            show_error('Image not found', 404);

        } else {

            $this->load->view('ai_image', ['generated_image' => $file_path]);

        }



    }



    // DISPLAY ALL IMAGES AS A GALLERY

    public function display_images() {

        $data['images'] = $this->ai_text_form_model->get_images();



        $this->load->view('display_images', $data);



    }



    // DELETE IMAGE

    public function delete_image() {

        $id = $this->input->post('id');

        $image = $this->ai_text_form_model->get_image($id);

        if ($image) {

            $imagePath = FCPATH . str_replace('/', '\\', $image->generated_image);

            if (file_exists($imagePath)) {

                if (unlink($imagePath)) {

                    $this->ai_text_form_model->delete_image($id);

                    echo 'Image deleted successfully.';

                } 

            }

        } else {

            echo 'Image not found.';

        }

    }



    // DELETES IMAGE THAT PASSED 5 DAYS

    public function five_days_ago_images() {

        $id = $this->input->post('id');

        $image = $this->ai_text_form_model->get_image($id);

        if ($image) {

            $created_at = strtotime($image->created_at);

            $five_days_ago = strtotime('-5 days');

            

            if ($created_at <= $five_days_ago) {



                $imagePath = FCPATH . str_replace('/', '\\', $image->generated_image);

                if (file_exists($imagePath)) {

                    if (unlink($imagePath)) {

                        $this->ai_text_form_model->delete_image($id);

                        echo 'Image deleted successfully.';

                    }

                }

            } else {

                echo 'Image is not 5 days old yet.';

        }

    }





}

    

}







