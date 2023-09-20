<?php

defined('BASEPATH') or exit('No direct script access allowed');

if (!$CI->db->table_exists(db_prefix() . 'zoom')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'zoom` (
  `id` int(11) NOT NULL,
  `api_key` varchar(40) NOT NULL,
  `api_secret` varchar(40) NOT NULL,
  `zoom_email` varchar(40) DEFAULT NULL

) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'zoom`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'zoom`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');

// Dummy API Key & API SECRET
$api_key='API KEY';
$api_secret='API SECRET';
$zoom_email='Emailaddress';

$data = array(
    'zoom_email'=>$zoom_email,
    'api_key'=>$api_key,
    'api_secret'=>$api_secret
);

$CI->db->insert( 'tblzoom',$data);



}


