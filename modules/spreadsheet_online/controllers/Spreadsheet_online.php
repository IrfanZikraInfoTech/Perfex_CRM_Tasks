<?php

defined('BASEPATH') or exit('No direct script access allowed');
/**
 * Class Spreadsheet_online 
 */
class Spreadsheet_online extends AdminController
{
    /**
     * __construct
     */
    public function __construct()
    {
      parent::__construct();
      $this->load->model('spreadsheet_online_model');
      $this->load->model('departments_model');
      $this->load->model('clients_model');
      $this->load->model('staff_model');
    }

    /**
     * manage
     * @return view
     */
    public function manage(){
      $data['title'] = _l('spreadsheet_online');
      $data['tab'] = $this->input->get('tab');
      $data['departments'] = $this->departments_model->get();
      $data['staffs'] = $this->staff_model->get();
      $data['clients'] = $this->clients_model->get();
      $data['client_groups'] = $this->clients_model->get_groups();

      if($data['tab'] == ''){
       $data['tab'] = 'my_folder';
     }
     if($data['tab'] == 'my_folder'){
      $data['folder_my_tree'] = $this->spreadsheet_online_model->tree_my_folder();
    }
    if($data['tab'] == 'my_share_folder'){
      $data['folder_my_share_tree'] = $this->spreadsheet_online_model->tree_my_folder_share();
    }
    $this->load->view('manage', $data);
  }

    /**
     * Add edit folder
    */
    public function add_edit_folder(){
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
        redirect(admin_url('spreadsheet_online/manage?tab=my_folder'));
      }    
    }
    /**
     * new file view 
     * @param  int $parent_id 
     * @param  int $id        
     * @return  view or json            
     */
    public function new_file_view($parent_id, $id = ""){
      $data_form = $this->input->post();
      $data['title'] = _l('new_file');
      $data['parent_id'] = $parent_id;
      $data['folder'] = $this->spreadsheet_online_model->get_my_folder_all();
      $data['role'] = "";
      $data['departments'] = $this->departments_model->get();
      $data['staffs'] = $this->staff_model->get();
      $data['clients'] = $this->clients_model->get();
      $data['client_groups'] = $this->clients_model->get_groups();
      if(isset($data_form['id'])){
        if($data_form['id'] == ""){
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
        if($data_form['id'] != ""){

          if(isset($data_form['id'])){
            if($data_form['id'] != ""){
              $data['id'] = $data_form['id'];
            }
          }else{
            $data['id'] = $id;
            $data['file_excel'] = $this->spreadsheet_online_model->get_file_sheet($data['id']);
            $data['data_form'] = str_replace('""', '"', $data['file_excel']->data_form); 
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
      }
      
      if($id != ''){
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

      $data['tree_save'] = json_encode($this->spreadsheet_online_model->get_folder_tree());
      if(!isset($success)){
        $this->load->view('new_file_view', $data);
      }
    }
    /**
     * delete folder file
     * @param  int $id 
     * @return  json    
     */
    public function delete_folder_file($id){
      $success = false;
      $message = _l('deleted_fail');
      if($id == 1){
        echo json_encode(['success' => false, 'message' => _l('cannot_deleted _root_directory')]);
      }else{
        if($id != ''){
          $success = $this->spreadsheet_online_model->delete_folder_file($id);
          $message = _l('deleted');
        }
        echo json_encode(['success' => $success, 'message' => $message]);
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
    /**
     * get folder zip 
     * @param   int $id   
     * @param   string $name 
     * @return  json      
     */
    public function get_folder_zip($id, $name){
      echo json_encode($this->spreadsheet_online_model->get_folder_zip($id, $name));
    }
    /**
     * update share spreadsheet online 
     * @return redirect
     */
    public function update_share_spreadsheet_online(){
      $data = $this->input->post();
      $success = $this->spreadsheet_online_model->update_share($data);
      

      $staff_notification = get_option('spreadsheet_staff_notification');
      $staff_sent_email = get_option('spreadsheet_email_templates_staff');
      $client_notification = get_option('spreadsheet_client_notification');
      $client_sent_email = get_option('spreadsheet_email_templates_client');

      if($success == true){
        $message = _l('updated_successfully');
        set_alert('success', $message);
        
        if(count($data['staffs_share'] > 0)){
          foreach ($data['staffs_share'] as $key => $value) {

            $this->db->where('id', $data['id']);
            $share = $this->db->get(db_prefix() . 'spreadsheet_online_my_folder')->row();

            $share->receiver = spreadsheet_email_staff($value);
            $share->staff_share_id = $value;

            $share->type_template = "staff_template";

            if($staff_sent_email == 1){
              $template = mail_template('spreadsheet_share', 'spreadsheet_online', array_to_object($share));
              $template->send();
            }

            if($staff_notification == 1){
              $link = '';
              $link = 'spreadsheet_online/new_file_view/'.$data['parent_id'].'/'.$data['id'];
              $string_sub = get_staff_full_name($value) . ' ' . _l('share') . ' ' . $share->type . ' ' . $share->name . ' ' . _l('for_you');
              $this->spreadsheet_online_model->notifications($value, $link, strtolower($string_sub));
            }

          }
        }

        if(count($data['clients_share'] > 0)){
          foreach ($data['clients_share'] as $key => $value) {
            

            $this->db->where('id', $data['id']);
            $share = $this->db->get(db_prefix() . 'spreadsheet_online_my_folder')->row();

            $this->db->where('id', $value);
            $contact = $this->db->get(db_prefix() . 'contacts')->row()->email;

            if($contact != null || $contact != ''){
              $share->receiver = $contact;
              $share->client_share_id = $value;
              $share->type_template = "client_template";
              if($client_sent_email == 1){
                $template = mail_template('spreadsheet_share_client', 'spreadsheet_online', array_to_object($share));
                $template->send();
              }


              if($client_notification == 1){
                $link_client = '';
                $link_client = 'spreadsheet_online/new_file_view/'.$data['parent_id'].'/'.$data['id'];
                $string_sub = get_staff_full_name($value) . ' ' . _l('share') . ' ' . $share->type . ' ' . $share->name . ' ' . _l('for_you');
                $this->spreadsheet_online_model->notifications($value, $link_client, strtolower($string_sub));
              }

            }
          }
        }

        


      }
      else{
        $message = _l('updated_fail');
        set_alert('warning', $message);
      }
      redirect(admin_url('spreadsheet_online/manage?tab=my_folder'));
      
    }


    /**
     * new file view 
     * @param  int $parent_id 
     * @param  int $id        
     * @return  view or json            
     */
    public function file_view_share($hash = ""){
      $data_form = $this->input->post();
      $data['tree_save'] = json_encode($this->spreadsheet_online_model->get_folder_tree());

      if($hash != ""){
        $share_child = $this->spreadsheet_online_model->get_share_form_hash($hash);
        $id = $share_child->id_share;
        $file_excel = $this->spreadsheet_online_model->get_file_sheet($id);
        $data['parent_id'] = $file_excel->parent_id;
        $data['role'] = $share_child->role;
        if (($share_child->rel_id != get_staff_user_id())) {
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
        $this->load->view('share_file_view', $data);
      }
    }
    /**
     * get hash staff
     * @param int $id 
     * @return json    
     */
    public function get_hash_staff($id){
      $rel_id = get_staff_user_id();
      $rel_type = 'staff';
      echo json_encode($this->spreadsheet_online_model->get_hash($rel_type, $rel_id, $id));
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
     * get related
     * @param  string $type 
     * @return json
     */
    public function get_related($type = ''){
      $rel_data = get_relation_data($type);
      $html_option = '';
      $html_option .= '<option value=""></option>';
      foreach ($rel_data as $key => $value) {
        $rel_val = get_relation_values($value,$type);
        $html_option .= '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
      }
      echo json_encode($html_option);
    }

    /**
     * update related spreadsheet online 
     * @return redirect
     */
    public function update_related_spreadsheet_online(){
      $data = $this->input->post();
      $success = $this->spreadsheet_online_model->update_related($data);

      if($success == true){
        $message = _l('updated_successfully');
        set_alert('success', $message);
      }
      else{
        $message = _l('updated_fail');
        set_alert('warning', $message);
      }
      redirect(admin_url('spreadsheet_online/manage?tab=my_folder'));
    }
    /**
     * get share staff client
     * @param  int $id 
     * @return json     
     */
    public function get_share_staff_client($id){

      $data = $this->spreadsheet_online_model->get_share_detail($id);

      $html_staff = "";
      $html_client = "";
      if(count($data['staffs_share']) > 0){
        foreach ($data['staffs_share'] as $key => $value) {
          $html_staff .= '
              <tr>
                <td>'.$value.'</td>
                <td>'.($data['staffs_role'][$key] == 1 ? "View" : "Edit").'</td>
              </tr>
          '; 
        }
      }

      if(count($data['clients_share']) > 0){
        foreach ($data['clients_share'] as $key => $value) {
          $html_client .= '
              <tr>
                <td>'.$value.'</td>
                <td>'.($data['clients_role'][$key] == 1 ? "View" : "Edit").'</td>
              </tr>
          '; 
        }
      }
      echo json_encode(['staffs' =>$html_staff, 'clients' =>  $html_client]);
    }
    /**
     * [get_my_folder description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function get_my_folder($id){
      echo json_encode($this->spreadsheet_online_model->get_my_folder($id));
    }
    /**
     * [get_my_folder_get_hash description]
     * @param  [type] $rel_type [description]
     * @param  [type] $rel_id   [description]
     * @param  [type] $id_share [description]
     * @return [type]           [description]
     */
    public function get_my_folder_get_hash($rel_type, $rel_id, $id_share){
      echo json_encode($this->spreadsheet_online_model->get_hash($rel_type, $rel_id, $id_share));
    }
    /**
     * [append_value_department description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function append_value_department($id){
      $data = get_all_staff_by_department($id);
      $html = '';
      if(count($data) > 0){
        $html .= '<option value=""></option>';
        foreach ($data as $key => $value) {
          $html .= '<option value="'.$value['staffid'].'">'.get_staff_full_name($value['staffid']).'</option>';
        }
      }
      echo json_encode($html);
    }
    /**
     * [get_staff_all description]
     * @return [type] [description]
     */
    public function get_staff_all(){
      $staffs = $this->staff_model->get();
      $html = '';
      if(count($staffs) > 0){
          $html .= '<option value=""></option>';
        foreach ($staffs as $key => $value) {
          $html .= '<option value="'.$value['staffid'].'">'.get_staff_full_name($value['staffid']).'</option>';
        }
      }
      echo json_encode($html);
    }
    /**
     * [append_value_group description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function append_value_group($id){
      $data = get_all_client_by_group($id);
      $html = '';
      if(isset($data[0])){
        if(count($data[0]) > 0){
          $html .= '<option value=""></option>';
          foreach ($data[0] as $key => $value) {
            $client = $this->clients_model->get($value);
            $html .= '<option value="'.$client->userid.'">'.$client->company.'</option>';
          }
        }
      }
      echo json_encode($html);
    }
    /**
     * [get_client_all description]
     * @return [type] [description]
     */
    public function get_client_all(){
      $client = $this->clients_model->get();
      $html = '';
      if(count($client) > 0){
          $html .= '<option value=""></option>';
        foreach ($client as $key => $value) {
          $html .= '<option value="'.$value['userid'].'">'.$value['company'].'</option>';
        }
      }
      echo json_encode($html);
    }

    /**
     * [get_client_all description]
     * @return [type] [description]
     */
    public function get_related_id($id){
      $data =  $this->spreadsheet_online_model->data_related_id($id);
      echo json_encode($data);
    }
    /**
     * [droppable_event description]
     * @param  [type] $id        [description]
     * @param  [type] $parent_id [description]
     * @return [type]            [description]
     */
    public function droppable_event($id, $parent_id){
      echo json_encode($this->spreadsheet_online_model->droppable_event($id, $parent_id));
    }


    /**
     * get hash related
     * @param int $id 
     * @return json    
     */
     public function get_hash_related($rel_id, $rel_type, $parent_id){
      echo json_encode($this->spreadsheet_online_model->get_hash_related($rel_type, $rel_id, $parent_id));
    }


    /**
     * file view related
     * @param  int $hash        
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
        $this->load->view('share_file_view', $data);
      }
    }

    public function spreadsheet_online_setting(){
      $data = $this->input->post();
      if(isset($data['spreadsheet_staff_notification'])){
        if($data['spreadsheet_staff_notification'] = 'on'){
          update_option('spreadsheet_staff_notification', 1);
        }
      }else{
          update_option('spreadsheet_staff_notification', 0);
        }

      if(isset($data['spreadsheet_email_templates_staff'])){
        if($data['spreadsheet_email_templates_staff'] = 'on'){
          update_option('spreadsheet_email_templates_staff', 1);
        }
      }else{
        update_option('spreadsheet_email_templates_staff', 0);
      }

      if(isset($data['spreadsheet_client_notification'])){
        if($data['spreadsheet_client_notification'] = 'on'){
          update_option('spreadsheet_client_notification', 1);
        }
      }else{
        update_option('spreadsheet_client_notification', 0);
      }

      if(isset($data['spreadsheet_email_templates_client'])){
        if($data['spreadsheet_email_templates_client'] = 'on'){
          update_option('spreadsheet_email_templates_client', 1);
        }
      }else{
        update_option('spreadsheet_email_templates_client', 0);
      }

      redirect(admin_url('spreadsheet_online/manage'));
    }
}