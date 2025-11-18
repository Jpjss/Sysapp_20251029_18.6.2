<?php
require_once __DIR__ . '/config/config.php';

echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "dirname(SCRIPT_NAME): " . dirname($_SERVER['SCRIPT_NAME']) . "\n";
echo "BASE_URL: " . BASE_URL . "\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
