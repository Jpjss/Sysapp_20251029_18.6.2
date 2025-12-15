<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['REQUEST_URI'] = '/api/empresas';
$_SERVER['REQUEST_METHOD'] = 'GET';

ob_start();
require __DIR__ . '/api/index.php';
$output = ob_get_clean();

echo $output;
