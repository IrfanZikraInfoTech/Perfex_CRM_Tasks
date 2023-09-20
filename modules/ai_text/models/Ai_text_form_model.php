<?php

defined('BASEPATH') or exit('No direct script access allowed');



class Ai_text_form_model extends App_Model

{

    public function __construct()

    {

        parent::__construct();



        $this->load->database();



    }

        // INSERTING

        public function savetemplate($data)

        {

            $data['inputform'] = json_encode(json_decode($data['inputform'], true));



            $this->db->insert('tbl_ai_templates', $data);

            return $this->db->insert_id();

        }

    

        // GETTING DATA FROM TABLE TEMPLATES

        public function get_saved_data() {

            $query = $this->db->get('tbl_ai_templates');

            return $query->result();

        }



        // GETTING SAVED DATA THROUGH ID

        public function saved_data($id) {



            $this->db->where('id', $id);

            $query = $this->db->get('tbl_ai_templates');



            if ($query->num_rows() > 0) {

                return $query->row_array(); 

             }

         

             return false;

        }



        // SAVING GENERATED DATA

        public function saveGeneratedData($generatedData, $templateName) {

            $query = $this->db->get('tblstaff');

            

            if ($query->num_rows() > 0) {

                $row = $query->row();

                $tblstaff = $row->staffid;

            } else {

                $tblstaff = null;

            }

        

            $data = array(

                'history' => $generatedData,

                'staff_id' => $tblstaff,

                'template_name' => $templateName,

            );

        

            $this->db->insert('tblgenerated_data', $data);

        }



        // GET GENERATED DATA

        public function generated_data()

        {

            $query = $this->db->get('tblgenerated_data');

            return $query->result();

        }

        

        // DELETE GENERATED DATA

        public function deleteData($id) {

            

            $this->db->where('id', $id);

            $this->db->delete('tblgenerated_data'); 

    

            return $this->db->affected_rows() > 0;

        }



        // EDIT TEMPLATE DATA

        public function editdata($id) {



            $this->db->where('id', $id);

            $query = $this->db->get('tbl_ai_templates');

    

            if ($query->num_rows() > 0) {               

                return $query->row();

            

            } else {

                

                return null; 

            }

        }



        // UPDATE EDITED TEMPLATE DATA

        public function updateTemplateData($id, $data)

        {

            $data['inputform'] = json_encode(json_decode($data['inputform'], true));



            $this->db->where('id', $id);

            $this->db->update('tbl_ai_templates', $data);

        }



        // DELETE TEMPLATE

        public function deletetemplate($id) {

            

            $this->db->where('id', $id);

            $this->db->delete('tbl_ai_templates'); 

    

            return $this->db->affected_rows() > 0;

        }



        // Saving Image

        public function save_image($url, $prompt) {



            $image = file_get_contents($url);

            $dir_path = 'modules/AI_text/images/';



            if (!is_dir($dir_path)) {

                if (!mkdir($dir_path, 0777, true)) {

                    throw new Exception('Failed to create directory ' . $dir_path);

                }

            }



            $file_name = substr(sha1(strtotime("now").rand(1111,9999)), 0 ,10) . '.png';

            $file_path = $dir_path . $file_name;



            if (file_put_contents($file_path, $image) === false) {

                throw new Exception('Failed to save image to ' . $file_path);

            }



            $this->db->insert('tblgenerated_image',

            

            ['generated_image' => $file_path,

            'input_text' => $prompt

            ]);

            return $this->db->insert_id();



        }

    

        // Displaying Image

        public function get_image($id) {

            

            $query = $this->db->get_where('tblgenerated_image', ['id' => $id]);

            return $query->row();

        }

        

        // DISPLAYING ALL IMAGES

        public function get_images() {

            $query = $this->db->get('tblgenerated_image');

            return $query->result();

        }



        // DELETE Image

        public function delete_image($id) {

            $this->db->where('id', $id);

            $this->db->delete('tblgenerated_image');

        }



        // DELETES IMAGES 5 DAYS AGO

         public function delete_old_images() {

            $five_days_ago = date('Y-m-d H:i:s', strtotime('-5 days'));

        

            $this->db->where('created_at <', $five_days_ago);

            $old_images = $this->db->get('tblgenerated_image')->result();

        

            foreach ($old_images as $old_image) {

                $file_path = FCPATH . './modules/AI_text/images/' . $old_image->image_file_name;

                if (file_exists($file_path)) {

                    unlink($file_path);

                }

        

                $this->db->delete('tblgenerated_image', array('id' => $old_image->id));

            }

        }



    }

        

        

        



    



