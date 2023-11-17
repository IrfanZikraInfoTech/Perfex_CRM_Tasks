<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Check if 'tbl_payroll_salary' table exists, if not, create it.
if (!$CI->db->table_exists( 'tbl_payroll_salary')) {
    $CI->db->query('CREATE TABLE `' .  'tbl_payroll_salary` (
  `salary_id` int(11) NOT NULL,
  `employee_salary` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL
)');

    $CI->db->query('ALTER TABLE `' .  'tbl_payroll_salary`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `employee_id` (`employee_id`);');

    $CI->db->query('ALTER TABLE `' .  'tbl_payroll_salary`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT;');

    // Here we add the foreign key constraint
    $CI->db->query('ALTER TABLE `' .  'tbl_payroll_salary` ADD CONSTRAINT `fk_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `tblstaff`(`staffid`) ON DELETE CASCADE ON UPDATE CASCADE;');
}

// Check if 'tbl_payroll_records' table exists, if not, create it.
if (!$CI->db->table_exists('tbl_payroll_records')) {
  $CI->db->query('CREATE TABLE `' .  'tbl_payroll_records` (
    `id` int(11) NOT NULL,
    `staff_id` int(11) NOT NULL,
    `changedby` int(11) NOT NULL,
    `salary` decimal(11) NOT NULL,
    `month` int(10) NOT NULL,
    `bonus` int(11) DEFAULT NULL,
    `status` int(11) NULL,
    `created_date` date NOT NULL,
    `update_date` date DEFAULT NULL,
    `deduction` int(111) DEFAULT NULL,
    `payment_mode` char(20) NOT NULL,
    `refrence_number` int(50) DEFAULT NULL,
    `currency` VARCHAR(3) NOT NULL,
    `remark` VARCHAR(255) NOT NULL
  )');


    $CI->db->query('ALTER TABLE `' .  'tbl_payroll_records`
  ADD PRIMARY KEY (`id`);');

    $CI->db->query('ALTER TABLE `' .  'tbl_payroll_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;');
}

?>
