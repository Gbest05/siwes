<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mock server variables to simulate visiting register page
$_SERVER['REQUEST_URI'] = '/auth/register';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT'] = 'CLI';

// Require bootstrap index.php to boot the app and capture output
require_once __DIR__ . '/../public/index.php';
