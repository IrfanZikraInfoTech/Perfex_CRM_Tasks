<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Knowledge_base_q_a_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get article by id
     * @param  string $id   article ID
     * @param  string $slug if search by slug
     * @return mixed       if ID or slug passed return object else array
     */
    public function get($id = '', $slug = '')
    {
        $this->db->select('slug,articleid, articlegroup, subject, curator,' . db_prefix() . 'hr_knowledge_base.file_name,' . db_prefix() . 'hr_knowledge_base.question_answers,' . db_prefix() . 'hr_knowledge_base.description,' . db_prefix() . 'hr_knowledge_base.active as active_article,' . db_prefix() . 'hr_knowledge_base_groups.active as active_group,name as group_name,staff_article');
        $this->db->from(db_prefix() . 'hr_knowledge_base');
        $this->db->join(db_prefix() . 'hr_knowledge_base_groups', db_prefix() . 'hr_knowledge_base_groups.groupid = ' . db_prefix() . 'hr_knowledge_base.articlegroup', 'left');
        $this->db->order_by('article_order', 'asc');
        if (is_numeric($id)) {
            $this->db->where('articleid', $id);
        }
        if ($slug != '') {
            $this->db->where('slug', $slug);
        }
        if ($this->input->get('groupid')) {
            $this->db->where('articlegroup', $this->input->get('groupid'));
        }
        if (is_numeric($id) || $slug != '') {
            return $this->db->get()->row();
        }

        return $this->db->get()->result_array();
    }

    /**
     * Get related artices based on article id
     * @param  mixed $current_id current article id
     * @return array
     */
    public function get_related_articles($current_id, $customers = true)
    {
        $total_related_articles = hooks()->apply_filters('total_related_articles', 5);

        $this->db->select('articlegroup');
        $this->db->where('articleid', $current_id);
        $article = $this->db->get(db_prefix() . 'hr_knowledge_base')->row();

        $this->db->where('articlegroup', $article->articlegroup);
        $this->db->where('articleid !=', $current_id);
        $this->db->where('active', 1);
        if ($customers == true) {
            $this->db->where('staff_article', 0);
        } else {
            $this->db->where('staff_article', 1);
        }
        $this->db->limit($total_related_articles);

        return $this->db->get(db_prefix() . 'hr_knowledge_base')->result_array();
    }

    /**
     * Add new article
     * @param array $data article data
     */
    public function add_article($data)
    {
        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        //set default staff_article = 1
        $data['staff_article'] = 1;
        
        if (isset($data['question_answers'])) {
            $data['question_answers'] = 1;
        } else {
            $data['question_answers'] = 0;
        }
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['slug']        = slug_it($data['subject']);
        $this->db->like('slug', $data['slug']);
        $slug_total = $this->db->count_all_results(db_prefix() . 'hr_knowledge_base');
        if ($slug_total > 0) {
            $data['slug'] .= '-' . ($slug_total + 1);
        }
        $data = hooks()->apply_filters('before_add_hr_kb_article', $data);

        $this->db->insert(db_prefix() . 'hr_knowledge_base', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Article Added [ArticleID: ' . $insert_id . ' GroupID: ' . $data['articlegroup'] . ']');
        }

        return $insert_id;
    }

    /**
     * Update article
     * @param  array $data article data
     * @param  mixed $id   articleid
     * @return boolean
     */
    public function update_article($data, $id)
    {
        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        //set default staff_article = 1
        $data['staff_article'] = 1;

        if (isset($data['question_answers'])) {
            $data['question_answers'] = 1;
        } else {
            $data['question_answers'] = 0;
        }

        $this->db->where('articleid', $id);
        $this->db->update(db_prefix() . 'hr_knowledge_base', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Article Updated [ArticleID: ' . $id . ']');

            return true;
        }

        return false;
    }


    /**
     * update kan ban
     * @param  [type] $data 
     * @return [type]       
     */
    public function update_kan_ban($data)
    {
        $affectedRows = 0;
        foreach ($data['order'] as $o) {
            $this->db->where('articleid', $o[0]);
            $this->db->update(db_prefix() . 'hr_knowledge_base', [
                'article_order' => $o[1],
                'articlegroup'  => $data['groupid'],
            ]);
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * Change article status
     * @param  mixed $id     article id
     * @param  boolean $status is active or not
     */
    public function change_article_status($id, $status)
    {
        $this->db->where('articleid', $id);
        $this->db->update(db_prefix() . 'hr_knowledge_base', [
            'active' => $status,
        ]);
        log_activity('Article Status Changed [ArticleID: ' . $id . ' Status: ' . $status . ']');
    }


    /**
     * update groups order
     * @return [type] 
     */
    public function update_groups_order()
    {
        $data = $this->input->post();
        foreach ($data['order'] as $group) {
            $this->db->where('groupid', $group[0]);
            $this->db->update(db_prefix() . 'hr_knowledge_base_groups', [
                'group_order' => $group[1],
            ]);
        }
    }

    /**
     * Delete article from database and all article connections
     * @param  mixed $id article ID
     * @return boolean
     */
    public function delete_article($id)
    {
         $this->load->model('hr_profile_model');
        //delete atachement file
        $hr_article_file = $this->hr_profile_model->get_hr_profile_file($id, 'hr_profile_kb_articl');
        foreach ($hr_article_file as $file_key => $file_value) {
            $this->hr_profile_model->delete_hr_article_attachment_file($file_value['id']);
        }

        $this->db->where('articleid', $id);
        $this->db->delete(db_prefix() . 'hr_knowledge_base');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('articleid', $id);
            $this->db->delete(db_prefix() . 'hr_knowedge_base_article_feedback');

            $this->db->where('rel_type', 'hr_profile_kb_article');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'hr_views_tracking');

            log_activity('Article Deleted [ArticleID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get all KGB (Knowledge base groups)
     * @param  mixed $id Optional - KB Group
     * @param  mixed $active Optional - actve groups or not
     * @return mixed      array if not id passed else object
     */
    public function get_kbg($id = '', $active = '')
    {
        if (is_numeric($active)) {
            $this->db->where('active', $active);
        }
        if (is_numeric($id)) {
            $this->db->where('groupid', $id);

            return $this->db->get(db_prefix() . 'hr_knowledge_base_groups')->row();
        }
        $this->db->order_by('group_order', 'asc');

        return $this->db->get(db_prefix() . 'hr_knowledge_base_groups')->result_array();
    }

    /**
     * Add new knowledge base group/folder
     * @param array $data group data
     * @return boolean
     */
    public function add_group($data)
    {
        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }

        $data['group_slug'] = slug_it($data['name']);
        $this->db->like('group_slug', $data['group_slug']);
        $slug_total = $this->db->count_all_results(db_prefix() . 'hr_knowledge_base_groups');
        if ($slug_total > 0) {
            $data['group_slug'] .= '-' . ($slug_total + 1);
        }

        $this->db->insert(db_prefix() . 'hr_knowledge_base_groups', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Article Group Added [GroupID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Get knowledge base group by id
     * @param  mixed $id groupid
     * @return object
     */
    public function get_kbg_by_id($id)
    {
        $this->db->where('groupid', $id);

        return $this->db->get(db_prefix() . 'hr_knowledge_base_groups')->row();
    }

    /**
     * Update knowledge base group
     * @param  array $data group data
     * @param  mixed $id   groupid
     * @return boolean
     */
    public function update_group($data, $id)
    {
        if (isset($data['disabled'])) {
            $data['active'] = 0;
            unset($data['disabled']);
        } else {
            $data['active'] = 1;
        }
        $this->db->where('groupid', $id);
        $this->db->update(db_prefix() . 'hr_knowledge_base_groups', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Article Group Updated [GroupID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Change group status
     * @param  mixed $id     groupid id
     * @param  boolean $status is active or not
     */
    public function change_group_status($id, $status)
    {
        $this->db->where('groupid', $id);
        $this->db->update(db_prefix() . 'hr_knowledge_base_groups', [
            'active' => $status,
        ]);
        log_activity('Article Status Changed [GroupID: ' . $id . ' Status: ' . $status . ']');
    }


    /**
     * change group color
     * @param  [type] $data 
     * @return [type]       
     */
    public function change_group_color($data)
    {
        $this->db->where('groupid', $data['group_id']);
        $this->db->update(db_prefix() . 'hr_knowledge_base_groups', [
            'color' => $data['color'],
        ]);
    }

    /**
     * Delete knowledge base article
     * @param  mixed $id groupid
     * @return boolean
     */
    public function delete_group($id)
    {
        $current = $this->get_kbg_by_id($id);
        // Check if group already is using
        if (is_reference_in_table('articlegroup', db_prefix() . 'hr_knowledge_base', $id)) {
            return [
                'referenced' => true,
            ];
        }
        $this->db->where('groupid', $id);
        $this->db->delete(db_prefix() . 'hr_knowledge_base_groups');
        if ($this->db->affected_rows() > 0) {
            log_activity('Knowledge Base Group Deleted');

            return true;
        }

        return false;
    }

    /**
     * Add new article vote / Called from client area
     * @param mixed $articleid article id
     * @param boolean $bool
     */
    public function add_article_answer($articleid, $bool)
    {
        $bool = (bool) $bool;

        $ip = $this->input->ip_address();

        $this->db->where('ip', $ip)->where('articleid', $articleid)->order_by('date', 'desc')->limit(1);
        $answer = $this->db->get(db_prefix() . 'hr_knowedge_base_article_feedback')->row();

        if ($answer) {
            $last_answer    = strtotime($answer->date);
            $minus_24_hours = strtotime('-24 hours');
            if ($last_answer >= $minus_24_hours) {
                return [
                    'success' => false,
                    'message' => _l('clients_article_only_1_vote_today'),
                ];
            }
        }
        $this->db->insert(db_prefix() . 'hr_knowedge_base_article_feedback', [
            'answer'    => $bool,
            'ip'        => $ip,
            'articleid' => $articleid,
            'date'      => date('Y-m-d H:i:s'),
        ]);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            return [
                'success' => true,
                'message' => _l('clients_article_voted_thanks_for_feedback'),
            ];
        }

        return [
            'success' => false,
        ];
    }


    /**
     * get file article
     * @param  [type] $articleid 
     * @param  string $rel_type  
     * @return [type]            
     */
    public function get_file_article($articleid, $rel_type = "hr_profile_kb_article"){
        $this->db->where('rel_type', $rel_type);
        $this->db->from('files');
        $this->db->join('hr_knowledge_base', 'files.rel_id=hr_knowledge_base.articleid');
        return $this->db->get()->row();
    }

    /**
     * get list files
     * @param  [type] $id        
     * @param  [type] $articleid 
     * @return [type]            
     */
    public function get_list_files($id, $articleid){
        $this->db->where('rel_type', 'hr_profile_kb_article');
        $this->db->where('rel_id', $articleid);
        $results = $this->db->get(db_prefix().'files')->result_array();
        return $results;        
    }


    /**
     * send mail support
     * @param  [type] $data 
     * @return [type]       
     */
    public function send_mail_support($data)
    {

        $staff_id = get_staff_user_id();

        $inbox = array();
        
        $inbox['to'] = $data['email'];
        $inbox['sender_name'] = get_staff_full_name($staff_id);
        $inbox['subject'] = _strip_tags($data['subject']);
        $inbox['body'] = _strip_tags($data['content']);        
        $inbox['body'] = nl2br_save_html($inbox['body']);
        $inbox['date_received']      = date('Y-m-d H:i:s');

        $sender_email = _l('hr_this_is_the_email_sent_from').' '.hr_get_staff_email_by_id($staff_id).'. '._l('hr_if_there_is_any_response_please_send_it_via_this_email').': '.hr_get_staff_email_by_id($staff_id).'. ';

        
        if(strlen(get_option('smtp_host')) > 0 && strlen(get_option('smtp_password')) > 0 && strlen(get_option('smtp_username')) > 0){

            $ci = &get_instance();
            $ci->email->initialize();
            $ci->load->library('email');    
            $ci->email->clear(true);
            $ci->email->from(get_option('smtp_email'), $inbox['sender_name']);
            $ci->email->to($inbox['to']);

            $ci->email->subject($inbox['subject']);
            $email_footer = get_option('email_footer');
            $email_footer = str_replace('{companyname}', $companyname, $email_footer);
            $ci->email->message(get_option('email_header') .$sender_email. $inbox['body'] . $email_footer);

            $result = $ci->email->send(true);

            if($result){
                return true;
            }
            return fasle;
        }
        
        return fasle;
    }


}
