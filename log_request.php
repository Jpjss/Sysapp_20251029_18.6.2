<?php
// Log de debug para login
$logFile = __DIR__ . '/login_debug.log';

$timestamp = date('Y-m-d H:i:s');
$method = $_SERVER['REQUEST_METHOD'] ?? 'N/A';
$uri = $_SERVER['REQUEST_URI'] ?? 'N/A';
$postData = json_encode($_POST);
$session = json_encode($_SESSION ?? []);

$logEntry = "\n=== $timestamp ===\n";
$logEntry .= "Method: $method\n";
$logEntry .= "URI: $uri\n";
$logEntry .= "POST: $postData\n";
$logEntry .= "Session Before: $session\n";

file_put_contents($logFile, $logEntry, FILE_APPEND);
