<?php

defined('BASEPATH') or exit('No direct script access allowed');



$CI =& get_instance();



// Create tbltemplates table

if (!$CI->db->table_exists('tbl_ai_templates')) {

    $CI->db->query('CREATE TABLE tbl_ai_templates (

        id int(11) NOT NULL AUTO_INCREMENT,

        template_name varchar(255) NOT NULL,

        template_description text DEFAULT NULL,

        template_icon varchar(255) DEFAULT NULL,

        template_color varchar(20) DEFAULT NULL,

        inputform text NOT NULL,

        custom_prompt text NOT NULL,

        PRIMARY KEY (id)

    )');

}



// Create tblgenerated_data table

if (!$CI->db->table_exists('tblgenerated_data')) {

    $CI->db->query('CREATE TABLE `tblgenerated_data` (

        `id` int(11) NOT NULL,

        `staff_id` int(11) NOT NULL,

        `template_name` varchar(300) NOT NULL,

        `history` text NOT NULL,

        `date` DATETIME DEFAULT CURRENT_TIMESTAMP

    )');

}



// Create tblgenerated_image table

if (!$CI->db->table_exists('tblgenerated_image')) {

    $CI->db->query('CREATE TABLE `tblgenerated_image` (

        `id` int(11) NOT NULL AUTO_INCREMENT,

        `input_text` text NOT NULL,

        `generated_image` varchar(300) NOT NULL,

        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (`id`)

    )');

}







// Additional installation steps or actions



?>