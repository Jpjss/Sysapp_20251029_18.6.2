<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'core/Session.php';
require_once 'models/Empresa.php';
require_once 'models/Interface.php';

Session::start();

$db = Database::getInstance();
$db->connect();

$empresaModel = new Empresa();
$interfaceModel = new InterfaceModel();

echo "<h1>Debug: Empresas e Interfaces</h1>";

echo "<h2>Empresas:</h2>";
$empresas = $empresaModel->listar();
echo "<pre>";
print_r($empresas);
echo "</pre>";

echo "<h2>Interfaces:</h2>";
$interfaces = $interfaceModel->listar();
echo "<pre>";
print_r($interfaces);
echo "</pre>";
