<?php
defined('BASEPATH') or exit('No direct script access allowed');
use app\services\ValidatesContact;

/**
 * Recruitment portal Controller
 */
class Recruitment_portal extends App_Controller
{   


    public $template = [];

    public $data = [];

    public $use_footer = true;

    public $use_submenu = true;

    public $use_navigation = true;

    /**
     * construct
     */
    public function __construct() {

        hooks()->do_action('after_clients_area_init', $this);

        parent::__construct();

        $this->load->library('app_rec_portal_area_constructor');

        $this->load->model('recruitment_model');
    }

    /**
     * index
     * @return view
     */
    public function index()
    {   
        $data['title']            = _l('recruitment_portal');
        $data['rec_campaingn'] = $this->recruitment_model->do_recruitment_portal_search(true, '', $page = 1, $count = false, $where = []);
        $this->data($data);

        $this->view('recruitment_portal/portal');
        $this->layout();

        
    }

    /**
     * job detail
     * @return view 
     */
    public function job_detail($id ='')
    {   
        $data['title']            = _l('recruitment_portal');
        $data['rec_campaingn'] = $this->recruitment_model->get_rec_campaign_detail($id);
        $data['rec_channel'] = $this->recruitment_model->get_recruitment_channel_form_campaingn($id);
        $data['id'] = $id;

        $this->data($data);

        $this->view('recruitment_portal/job_detail');
        $this->layout();

        
    }

    /**
     * search job
     * 
     */
    public function search_job()
    {

        $search = $this->input->post('search');
        $page = $this->input->post('page');
        $status = true;

        $data['title']            = _l('showing_search_result', $search);
        $data['rec_campaingn'] = $this->recruitment_model->do_recruitment_portal_search($status, $search, $page = 1, $count = false, $where = []);
        $data['rec_campaingn_total'] = $this->recruitment_model->do_recruitment_portal_search($status, $search, $page = 1, $count = true, $where = []);

        $data['search'] = $search;
        $data['page'] = (float)$page+1;
        $this->data($data);

        $this->view('recruitment_portal/portal');
        $this->layout();

    }

    /**
     * show more job
     *  
     */
    public function show_more_job(){

        $search = $this->input->post('search');
        $page = $this->input->post('page');

        $status = true;

        $data = $this->recruitment_model->do_recruitment_show_more_job($status, $search, $page, $count = false, $where = []);

        echo json_encode([
                'page'=> $data['page'],
                'data' => $data['value'],
                'status' => $data['status']
            ]);
        die;

    }

    /**
     * job live search
     * @return json 
     */
    public function job_live_search()
    {
        $search = $this->input->post('search');
        $page = $this->input->post('page');
        $status = true;
        
        $data = $this->recruitment_model->do_recruitment_show_more_job($status, $search, $page = 1, $count = false, $where = []);

        $rec_campaingn_total = $this->recruitment_model->do_recruitment_portal_search($status, $search, $page = 1, $count = true, $where = []);

        echo json_encode([
                'page'=> $data['page'],
                'data' => $data['value'],
                'status' => $data['status'],
                'rec_campaingn_total' => $rec_campaingn_total
            ]);
        die;


    }

    /**
     * send mail list candidate
     * @return redirect
     */
    public function send_mail_list_candidate() {
        if ($this->input->post()) {
            $data = $this->input->post();

            if(isset($data['job_detail_id'])){
                $job_detail_id .= $data['job_detail_id'] ;
                unset($data['job_detail_id']);
            }

            $rs = $this->recruitment_model->portal_send_mail_to_friend($data);
            if ($rs == true) {
                set_alert('success', _l('send_mail_successfully'));

            }

            if(isset($job_detail_id)){
                redirect(site_url('recruitment/recruitment_portal/job_detail/'.$job_detail_id));

            }else{
                redirect(site_url('recruitment/recruitment_portal'));

            }

        }
    }




    /**
     * version 1.1.2
     * seperation recruitment portal
     */

    public function layout($notInThemeViewFiles = false)
    {

        /**
         * Navigation and submenu
         * @deprecated 2.3.2
         * @var boolean
         */

        $this->data['use_navigation'] = $this->use_navigation == true;
        $this->data['use_submenu']    = $this->use_submenu == true;

        /**
         * @since  2.3.2 new variables
         * @var array
         */
        $this->data['navigationEnabled'] = $this->use_navigation == true;
        $this->data['subMenuEnabled']    = $this->use_submenu == true;

        /**
         * Theme head file
         * @var string
         */
        $this->template['head'] = $this->load->view('recruitment_portal/rec_portal/head', $this->data, true);

        $GLOBALS['customers_head'] = $this->template['head'];

        /**
         * Load the template view
         * @var string
         */
        $module                       = CI::$APP->router->fetch_module();
        $this->data['current_module'] = $module;

        $viewPath = !is_null($module) || $notInThemeViewFiles ? $this->view : 'recruitment_portal/' . $this->view;

        $this->template['view']    = $this->load->view($viewPath, $this->data, true);
        $GLOBALS['customers_view'] = $this->template['view'];

        /**
         * Theme footer
         * @var string
         */
        $this->template['footer'] = $this->use_footer == true
        ? $this->load->view('recruitment_portal/rec_portal/footer', $this->data, true)
        : '';
        $GLOBALS['customers_footer'] = $this->template['footer'];

        /**
         * @deprecated 2.3.0
         * Theme scripts.php file is no longer used since vresion 2.3.0, add app_customers_footer() in themes/[theme]/index.php
         * @var string
         */
        $this->template['scripts'] = '';
        if (file_exists(VIEWPATH . 'recruitment_portal/scripts.php')) {
            if (ENVIRONMENT != 'production') {
                trigger_error(sprintf('%1$s', 'Clients area theme file scripts.php file is no longer used since version 2.3.0, add app_customers_footer() in themes/[theme]/index.php. You can check the original theme index.php for example.'));
            }

            $this->template['scripts'] = $this->load->view('recruitment_portal/scripts', $this->data, true);
        }

        /**
         * Load the theme compiled template
         */
        $this->load->view('recruitment_portal/index', $this->template);
    }

    /**
     * Sets view data
     * @param  array $data
     * @return core/ClientsController
     */
    public function data($data)
    {
        if (!is_array($data)) {
            return false;
        }

        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Set view to load
     * @param  string $view view file
     * @return core/ClientsController
     */
    public function view($view)
    {
        $this->view = $view;

        return $this;
    }

    /**
     * Sets view title
     * @param  string $title
     * @return core/ClientsController
     */
    public function title($title)
    {
        $this->data['title'] = $title;

        return $this;
    }

    /**
     * Disables theme navigation
     * @return core/ClientsController
     */
    public function disableNavigation()
    {
        $this->use_navigation = false;

        return $this;
    }

    /**
     * Disables theme navigation
     * @return core/ClientsController
     */
    public function disableSubMenu()
    {
        $this->use_submenu = false;

        return $this;
    }

    /**
    * Disables theme footer
    * @return core/ClientsController
    */
    public function disableFooter()
    {
        $this->use_footer = false;

        return $this;
    }

    


}
