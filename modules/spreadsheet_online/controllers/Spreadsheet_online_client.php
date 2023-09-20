<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Team password client controller
 */
class Spreadsheet_online_client extends ClientsController
{
  /**
   * __construct
   */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('spreadsheet_online_model');
    }
    /**
     * index 
     * @param  int $page 
     * @param  int $id   
     * @param  string $key  
     * @return view       
     */
      public function index(){  
       if(is_client_logged_in()){
          $data['folder_my_share_tree'] = $this->spreadsheet_online_model->tree_my_folder_share_client();
          $data['title'] = _l('spreadsheet_online');
          $this->data($data);
          $this->view('client_share');
          $this->layout();
        }else{
          redirect(site_url('authentication'));
        }
      } 

      /**
     * get hash client
     * @param int $id 
     * @return json    
     */
    public function get_hash_client($id){
      $rel_id = get_client_user_id();
      $rel_type = 'client';
      echo json_encode($this->spreadsheet_online_model->get_hash($rel_type, $rel_id, $id));
    }

    /**
     * new file view 
     * @param  int $parent_id 
     * @param  int $id        
     * @return  view or json            
     */
    public function file_view_share($hash = ""){
      if(is_client_logged_in()){
        $data_form = $this->input->post();
      $data['tree_save'] = json_encode($this->spreadsheet_online_model->get_folder_tree());
      
      if($hash != ""){
        $share_child = $this->spreadsheet_online_model->get_share_form_hash($hash);
        $id = $share_child->id_share;
        $file_excel = $this->spreadsheet_online_model->get_file_sheet($id);
        $data['parent_id'] = $file_excel->parent_id;
        $data['role'] = $share_child->role;
        if (($share_child->rel_id != get_client_user_id())) {
              access_denied('spreadsheet_online');
        }
      }else{
        $id = "";
        $data['parent_id'] = "";
        $data['role'] = 1;
      }

      $data_form = $this->input->post();
      $data['title'] = _l('new_file');
      $data['folder'] = $this->spreadsheet_online_model->get_my_folder_all();
      if($data_form || isset($data_form['id'])){
        if($data_form['id'] == ""){
          $success = $this->spreadsheet_online_model->add_file_sheet($data_form);
          if(is_numeric($success)){
            $message = _l('added_successfully');
            $file_excel = $this->spreadsheet_online_model->get_file_sheet($success);
            echo json_encode(['success' => true, 'message' => $message, 'name_excel' => $file_excel->name ]);
          }
          else{
            $message = _l('added_fail');
            echo json_encode(['success' => false, 'message' => $message]);
          }
        }
      }
      if($id != "" || isset($data_form['id'])){
        if(isset($data_form['id'])){
          if($data_form['id'] != ""){
            $data['id'] = $data_form['id'];
          }
        }else{
          $data['id'] = $id;
          $data['file_excel'] = $this->spreadsheet_online_model->get_file_sheet($data['id']);
          $mystring = $data['file_excel']->data_form;
          $findme   = 'images';
          $findme1   = '"color":",';
          $findme2   = '"value2":",';
          $findme3   = ':",';
          $pos = strpos($mystring, $findme);
          $pos1 = strpos($mystring, $findme1);
          $pos2 = strpos($mystring, $findme2);
          $pos3 = strpos($mystring, $findme2);
          if($pos){
            $data['data_form'] = str_replace('""', '"', $mystring); 
          }else{
            $data['data_form'] = $mystring; 
          }

          if($pos1){
            $data['data_form'] = str_replace('"color":",', '"color":"",', $mystring); 
            $data['data_form'] = str_replace('"value2":",', '"value2":"",', $mystring); 
          }

          if($pos2){
            $data['data_form'] = str_replace('"value2":",', '"value2":"",', $mystring); 
          }

          if($pos3){
            $data['data_form'] = str_replace(':",', ':"",', $mystring); 
          }
        }

        if($data_form && $data_form['id'] != ""){
          $success = $this->spreadsheet_online_model->edit_file_sheet($data_form);
          if($success == true){
            $message = _l('updated_successfully');
            echo json_encode(['success' => $success, 'message' => $message]);
          }
          else{
            $message = _l('updated_fail');
            echo json_encode(['success' => $success, 'message' => $message]);
          }
        }
      }
      if(!isset($success)){
        $this->data($data);
        $this->view('share_file_view_client');
        $this->layout();
      }
      }else{
        redirect(site_url('authentication'));
      }
      
    }

    /**
     * Add edit folder
    */
    public function add_edit_folder_client(){
      if($this->input->post()){
        $data = $this->input->post();    
        if($data['id'] == ''){
          $id = $this->spreadsheet_online_model->add_folder($data);
          if(is_numeric($id)){
            $message = _l('added_successfully');
            set_alert('success', $message);
          }
          else{
            $message = _l('added_fail');
            set_alert('warning', $message);
          }
        }
        else{
          $res = $this->spreadsheet_online_model->edit_folder($data);
          if($res == true){
            $message = _l('updated_successfully');
            set_alert('success', $message);
          }
          else{
            $message = _l('updated_fail');
            set_alert('warning', $message);
          }
        }
        redirect(site_url('spreadsheet_online/spreadsheet_online_client'));
      }    
    }
    
    /**
     * new file view 
     * @param  int $parent_id 
     * @param  int $id        
     * @return  view or json            
     */
    public function file_view_share_related($hash = ""){
      $data_form = $this->input->post();
      $data['tree_save'] = json_encode($this->spreadsheet_online_model->get_folder_tree());
      
      if($hash != ""){
        $share_child = $this->spreadsheet_online_model->get_share_form_hash_related($hash);
        $id = $share_child->parent_id;
        $file_excel = $this->spreadsheet_online_model->get_file_sheet($id);
        $data['parent_id'] = $file_excel->parent_id;
        $data['role'] = $share_child->role;
      }else{
        $id = "";
        $data['parent_id'] = "";
        $data['role'] = 1;
      }

      $data_form = $this->input->post();
      $data['title'] = _l('new_file');
      $data['folder'] = $this->spreadsheet_online_model->get_my_folder_all();
      if($data_form || isset($data_form['id'])){
        if($data_form['id'] == ""){
          $success = $this->spreadsheet_online_model->add_file_sheet($data_form);
          if(is_numeric($success)){
            $message = _l('added_successfully');
            $file_excel = $this->spreadsheet_online_model->get_file_sheet($success);
            echo json_encode(['success' => true, 'message' => $message, 'name_excel' => $file_excel->name ]);
          }
          else{
            $message = _l('added_fail');
            echo json_encode(['success' => false, 'message' => $message]);
          }
        }
      }
      if($id != "" || isset($data_form['id'])){
        if(isset($data_form['id'])){
          if($data_form['id'] != ""){
            $data['id'] = $data_form['id'];
          }
        }else{
          $data['id'] = $id;
          $data['file_excel'] = $this->spreadsheet_online_model->get_file_sheet($data['id']);
          $mystring = $data['file_excel']->data_form;
          $findme   = 'images';
          $findme1   = '"color":",';
          $findme2   = '"value2":",';
          $findme3   = ':",';
          $pos = strpos($mystring, $findme);
          $pos1 = strpos($mystring, $findme1);
          $pos2 = strpos($mystring, $findme2);
          $pos3 = strpos($mystring, $findme2);
          if($pos){
            $data['data_form'] = str_replace('""', '"', $mystring); 
          }else{
            $data['data_form'] = $mystring; 
          }

          if($pos1){
            $data['data_form'] = str_replace('"color":",', '"color":"",', $mystring); 
            $data['data_form'] = str_replace('"value2":",', '"value2":"",', $mystring); 
          }

          if($pos2){
            $data['data_form'] = str_replace('"value2":",', '"value2":"",', $mystring); 
          }

          if($pos3){
            $data['data_form'] = str_replace(':",', ':"",', $mystring); 
          }
        }

        if($data_form && $data_form['id'] != ""){
          $success = $this->spreadsheet_online_model->edit_file_sheet($data_form);
          if($success == true){
            $message = _l('updated_successfully');
            echo json_encode(['success' => $success, 'message' => $message]);
          }
          else{
            $message = _l('updated_fail');
            echo json_encode(['success' => $success, 'message' => $message]);
          }
        }
      }
      if(!isset($success)){
        $this->data($data);
        $this->view('share_file_view_client');
        $this->layout();
      }
    }
    /**
     * get file sheet 
     * @param  int $id 
     * @return  json    
     */
    public function get_file_sheet($id){
      $data = $this->spreadsheet_online_model->get_file_sheet($id);
      $data_form = $data->data_form;
      $findme   = 'images';
      $findme1   = '"color":",';
      $findme2   = '"value2":",';
      $findme3   = ':",';
      $pos = strpos($data_form, $findme);
      $pos1 = strpos($data_form, $findme1);
      $pos2 = strpos($data_form, $findme2);
      $pos3 = strpos($data_form, $findme2);
      if($pos){
        $data_form = str_replace('""', '"', $data_form); 
      }else{
        $data_form = $data_form; 
      }

      if($pos1){
        $data_form = str_replace('"color":",', '"color":"",', $data_form); 
        $data_form = str_replace('"value2":",', '"value2":"",', $data_form); 
      }

      if($pos2){
        $data_form = str_replace('"value2":",', '"value2":"",', $data_form); 
      }

      if($pos3){
        $data_form = str_replace(':",', ':"",', $data_form); 
      }
      echo json_encode($data_form);  
    }
}