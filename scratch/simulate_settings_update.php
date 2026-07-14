<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock POST values for the system configs update
$_SERVER['REQUEST_URI'] = '/settings/update';
$_SERVER['REQUEST_METHOD'] = 'POST';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI';

$_POST['action_type'] = 'system';
$_POST['system_name'] = 'SIWES Placement Portal Test';
$_POST['institution_name'] = 'Test University';
$_POST['current_session'] = '2026/2027';
$_POST['landing_hero_title'] = 'The Future of SIWES';
$_POST['landing_hero_subtitle'] = 'Test subtitle content';
$_POST['contact_email'] = 'siwes@test.edu.ng';
$_POST['contact_phone'] = '+2348000000';
$_POST['matric_format'] = 'CSC/2021/000';
$_POST['level_options'] = '3:3rd Year,4:4th Year';

// Start session and mock authenticated Admin user
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';

require_once __DIR__ . '/../public/index.php';
