<?php defined('BASEPATH') or exit('No direct script access allowed');

class Webhook_library
{
    public function send_chat_webhook($message, $threadKey) {

        $ch = curl_init();
    
        $current_time = date("H:i");
    
        if ($current_time < '05:59') {
            // If current time is less than 6:00 AM, use yesterday's base threads
            $date = date("dmY", strtotime("-1 day"));
        } else {
            // If current time is greater than or equal to 6:00 AM, use today's base threads
            $date = date("dmY");
        }
    
        $threadUrl = "{$threadKey}-{$date}";
    
        $formatted_date = date("d/m/Y", strtotime($date));
    
        $data = array(
            "text" => $message
        );
    
        // Remember to replace 'SPACE_ID' with your actual Google Chat space ID
        if($threadKey === "shifts" || $threadKey === "afk")
        {
            $url = "https://chat.googleapis.com/v1/spaces/AAAAsIq3P_g/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=5OC02nE2oxlTecgPi4jV1TGQLhOnhap4KlpQKTx5rzI&threadKey={$threadUrl}&messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD";
        }
        else if($threadKey === "tasks-allocation" || $threadKey === "tasks-activity")
        {
            $url = "https://chat.googleapis.com/v1/spaces/AAAA6jknWu4/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=onQidKXA1QDI0IBDMkqU0d_31zwWFZsFE-QPb-jJa5c&threadKey={$threadUrl}&messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD";
        }
        else if($threadKey === "eos" || $threadKey === "workingHours")
        {
            $url = "https://chat.googleapis.com/v1/spaces/AAAAsGG8iYM/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=Mifshsjgb3HLutqyd8ScfXtpPfkDcykf2d_POhGWN3c&threadKey={$threadUrl}&messageReplyOption=REPLY_MESSAGE_FALLBACK_TO_NEW_THREAD";
        }else if($threadKey == "buddy"){
            $url = "https://chat.googleapis.com/v1/spaces/AAAAvf7-Lxg/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=Ip5xTAPI-0iG1D03Dhlij94c3H_llrjIBKxkwXkV_Gg";
        }

        
    
        $headers = array(
            'Content-Type: application/json'
        );
    
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
        $response = curl_exec($ch);
        curl_close($ch);
    
        return $response;
    }
}
