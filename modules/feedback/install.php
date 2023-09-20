<?php

defined('BASEPATH') or exit('No direct script access allowed');



if (!$CI->db->table_exists(db_prefix() . 'feedback')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'feedback` (
  `id` int(11) NOT NULL,
  `customerid` int(11) NOT NULL,
  `projectid` varchar(40) NOT NULL,
  `coding` varchar(10) DEFAULT NULL,
  `communication` varchar(10) DEFAULT NULL,
  `services` varchar(10) DEFAULT NULL,
  `recommendation` varchar(10) DEFAULT NULL,
  `message` varchar(50) DEFAULT NULL,
  `feedback_submitted` int(11) DEFAULT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'feedback`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' . db_prefix() . 'feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

