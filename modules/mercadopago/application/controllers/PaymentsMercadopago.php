	<?php

defined('BASEPATH') or exit('No direct script access allowed');
require $_SERVER['DOCUMENT_ROOT']."/modules/mercadopago_gateway/libraries/mercadopago.php";

class PaymentsMercadopago extends ClientsController
{
 
    public function mercadopagoPayment() {

        $payment = $_SESSION['mp']->get(
                "/v1/payments/search",
                array(
                    "criteria" => "desc"
                )
            );

        $invoice_id = $_SESSION['data']['invoice']->id;

        $amount =  $this->db->select('amount');
        $this->db->from('tblinvoicepaymentrecords');
        $this->db->where('invoiceid',$_SESSION['data']['invoiceid']);
        $amount = $this->db->get()->result();

        $totPayedAmmount = 0;
        foreach ($amount as $am) {
            $totPayedAmmount =  $totPayedAmmount + $am->amount;
        }

        $data_tblinvoice = [];
        if ((int)(float)($_SESSION['data']['amount'] + $totPayedAmmount) == (int)(float)($_SESSION['data']['invoice']->total)) {
            $data_tblinvoices = array(
                'status'=>2
            );      
        }
        else {
             $data_tblinvoices = array(
                'status'=>3
            );
        }
        
        $this->db->where('id', $_SESSION['data']['invoiceid']);
        $this->db->update('tblinvoices',$data_tblinvoices);


        $data_payments = array(
            'invoiceid'=> $_SESSION['data']['invoiceid'],
            'amount' => $_SESSION['data']['amount'],
            'paymentmode' => $_SESSION['data']['paymentmode'],
            'daterecorded' => $_SESSION['data']['invoice']->datecreated,
            'date' => $_SESSION['data']['invoice']->date,
            'transactionid' =>$payment["response"]["results"][0]['id'],

        );

        $this->db->set($data_payments);
        $this->db->insert('tblinvoicepaymentrecords');

        unset($_SESSION['data']);
        unset($_SESSION['mp']);

        redirect($_SESSION['url']);

    }   
}