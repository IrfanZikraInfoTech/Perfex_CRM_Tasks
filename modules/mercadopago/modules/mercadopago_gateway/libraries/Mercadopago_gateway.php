<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once ('mercadopago.php');

class Mercadopago_gateway extends App_gateway
{
    protected $sandbox_url = 'https://www.mercadopago.eu/vmp/checkout-test';
    protected $production_url = 'https://www.mercadopago.eu/vmp/checkout';
    
    protected $private_key_url = '/keys/store_private_key.pem';
    protected $public_key_url = '/keys/api_public_key.pem';

    public function __construct()
    {
        /**
        * Call App_gateway __construct function
        */
        parent::__construct();
        
        $this->ci = & get_instance();
        
        /**
         * Gateway unique id - REQUIRED
         */
        $this->setId('mercadopago');

        /**
         * REQUIRED
         * Gateway name
         */
        $this->setName('Mercado Pago');

        /**
         * Add gateway settings
         */
        $this->setSettings([
            [
                'name'              =>'client_id',
                'label'             =>'Client Id',
                'type'              =>'input'
            ],
             [
                'name'              =>'client_secret',
                'label'             =>'Client Secret',
                'type'              =>'input'
            ],
            [
                'name'              => 'api_access_token',
                'label'             => 'Access Token',
                'type'              => 'input'
            ],
            [
                'name'              => 'description_dashboard',
                'label'             => 'settings_paymentmethod_description',
                'type'              => 'textarea',
                'default_value'     => 'Payment for Invoice {invoice_number}',
            ],
            [
                'name'              => 'currencies',
                'label'             => 'settings_paymentmethod_currencies',
                'default_value'     => 'USD,CAD'
            ],
            [
                'name'              => 'test_mode_enabled',
                'type'              => 'yes_no',
                'default_value'     => 0,
                'label'             => 'settings_paymentmethod_testing_mode',
            ],
        ]);

    }  
    
    public function process_payment($data)
    {
        $invoice = $data['invoice'];
        $successUrl = $_SERVER['HTTP_REFERER'];
        $_SESSION['url'] =  $successUrl;
        $_SESSION['data'] = $data;

        $user =  $this->ci->db->select('tblstaff.*');
        $this->ci->db->from('tblstaff');
        $this->ci->db->where('staffid',$invoice->client->userid);
        $user = $this->ci->db->get()->result()[0];

        $client_id =  $this->ci->db->select('tbloptions.*');
        $this->ci->db->from('tbloptions');
        $this->ci->db->where('name','paymentmethod_mercadopago_client_id');
        $client_id = $this->ci->db->get()->result()[0]->value;

        $client_secret =  $this->ci->db->select('tbloptions.*');
        $this->ci->db->from('tbloptions');
        $this->ci->db->where('name','paymentmethod_mercadopago_client_secret');
        $client_secret = $this->ci->db->get()->result()[0]->value;

        $mp = new MP($client_id, $client_secret);

        $preference_data = array(
              "items" => array(
                array(
                  "id" => uniqid(),
                  "quantity" => 1,
                  "unit_price" => +$data['amount'],

                )
              ),
              "payer" => array(
                "name" => $user->firstname,
                "surname" => $user->lastname,
                "email" => $user->email,
                "date_created" => "",
              ),
              "back_urls" => array(
                "success" => base_url()."PaymentsMercadopago/mercadopagoPayment",
                "failure" => $_SERVER['HTTP_REFERER'],
              ),
              "payment_methods" => array(
               
                "excluded_payment_types" => array(
                  array(
                    "id" => "atm",       
                  ),
                  array(
                    "id" => "ticket",      
                  )
                ),
                "installments"=> 1
              ),
              
              "notification_url" => base_url(),          
              "payer_email" => $user->email,
            );

            $preference = $mp->create_preference($preference_data);

            $_SESSION['mp'] = $mp;

            if ($test_mode_enabled == 1) {
              redirect($preference["response"]["sandbox_init_point"]); 
            }
            if ($test_mode_enabled == 0) {
              redirect($preference["response"]["init_point"]);
            }
       

              

    }

    public function get_action_url()
    {

    }

    public function finish_payment($post_data)
    {

    }
}