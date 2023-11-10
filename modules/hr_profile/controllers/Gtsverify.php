<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require_once __DIR__ .'/../third_party/node.php';
if (!class_exists('\Requests')) {
    require_once __DIR__ .'/../third_party/Requests.php';
}
use Requests as Requests;
Requests::register_autoloader();
/**
 * GTSSolution verify
 */
class Gtsverify extends AdminController{
    public function __construct(){
        parent::__construct();
    }

    /**
     * index 
     * @return void
     */
    public function index(){
        show_404();
    }

    /**
     * activate
     * @return json
     */
    public function activate(){
        $res = $this->verify();
        if ($res['status']) {
            $res['original_url']= $this->input->post('original_url');
        }
        echo json_encode($res);
    }

    /**
     * upgrade_database
     * @return json
     */
    public function upgrade_database(){
        $res = $this->verify();
        if ($res['status']) {
            $res['original_url']= $this->input->post('original_url');
        }
        echo json_encode($res);
    }

    /**
     * get_user_ip
     * @return string
     */
    private function get_user_ip(){
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    /**
     * verify
     * @return array
     */
    private function verify(){
        $module_name = $this->input->post('module_name');        
        if (empty($this->input->post('purchase_key'))) {
            return ['status'=>false, 'message'=>'Purchase key is required'];
        }        
        $envato_res = $this->get_env_purchase_data($this->input->post('purchase_key'),$module_name);        
        if (empty($envato_res)) {
            return ['status'=>false, 'message'=>'Something went wrong'];
        }
        if (!empty($envato_res->error)) {
            return ['status'=>false, 'message'=>$envato_res->description];
        }
        if (empty($envato_res->sold_at)) {
            return ['status'=>false, 'message'=>'Sold time for this code is not found'];
        }
        if ((false === $envato_res) || !is_object($envato_res) || isset($envato_res->error) || !isset($envato_res->sold_at)) {
            return ['status'=>false, 'message'=>'Something went wrong'];
        }
        $this->load->config($module_name.'/conf');
        if ($this->config->item('product_item_id') != $envato_res->item->id) {
            return ['status'=>false, 'message'=>'Purchase key is not valid'];
        }
        $this->load->library('user_agent');
        $data['user_agent']       = $this->agent->browser().' '.$this->agent->version();
        $data['activated_domain'] = base_url();
        $data['requested_at']     = date('Y-m-d H:i:s');
        $data['ip']               = $this->get_user_ip();
        $data['os']               = $this->agent->platform();
        $data['purchase_code']    = $this->input->post('purchase_key');
        $data['envato_res']       = $envato_res;
        $data                     = json_encode($data);

        try {
            $headers = ['Accept' => 'application/json'];
            $request = Requests::post(REG_PROD_POINT, $headers, $data);
            if ((500 <= $request->status_code) && ($request->status_code <= 599) || 404 == $request->status_code) {
                update_option($module_name.'_verification_id', '');
                update_option($module_name.'_verified', true);
                update_option($module_name.'_last_verification', time());

                return ['status'=>true];
            }

            $response = json_decode($request->body);
            if (200 != $response->status) {
                return ['status'=>false, 'message'=>$response->message];
            }

            if (200 == $response->status) {
                $return = $response->data ?? [];
                if (!empty($return)) {
                    update_option($module_name.'_verification_id', $return->verification_id);
                    update_option($module_name.'_verified', true);
                    update_option($module_name.'_last_verification', time());
                    file_put_contents(__DIR__.'/../config/token.php', $return->token);

                    return ['status'=>true];
                }
            }
        } catch (Exception $e) {
            update_option($module_name.'_verification_id', '');
            update_option($module_name.'_verified', true);
            update_option($module_name.'_last_verification', time());

            return ['status'=>true];
        }

        return ['status'=>false, 'message'=>'Something went wrong'];
    }

    /**
     * get_env_purchase_data
     * @param  string $code
     * @param  string $bearer 
     * @return json
     */
    private function get_env_purchase_data($code,$module_name){
        $this->load->config($module_name.'/conf');
        $bearer   = 'bearer '.$this->config->item('gtss_env_key');
        $header   = [];
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json; charset=utf-8';
        $header[] = 'Authorization: '.$bearer;

        $verify_url = 'https://api.envato.com/v3/market/author/sale/';
        $ch_verify  = curl_init($verify_url.'?code='.$code);

        curl_setopt($ch_verify, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch_verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_verify, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch_verify, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch_verify, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

        $cinit_verify_data = curl_exec($ch_verify);
        curl_close($ch_verify);

        if ('' != $cinit_verify_data) {
            return json_decode($cinit_verify_data);
        }

        return false;
    }
}