<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$uploadDir = __DIR__ . '/public/uploads/xml_temp/';

echo "Upload Dir: $uploadDir\n";
echo "Dir exists: " . (is_dir($uploadDir) ? 'Yes' : 'No') . "\n";

if (!is_dir($uploadDir)) {
    echo "Directory does not exist!\n";
    exit;
}

$files = glob($uploadDir . '*.xml');
echo "Files found: " . count($files) . "\n";

if (empty($files)) {
    echo "No files found!\n";
    exit;
}

// Lista arquivos
foreach ($files as $file) {
    echo "- " . basename($file) . " (" . filesize($file) . " bytes)\n";
}

// Testa criação do ZIP
$zipName = 'xmls_corrigidos_' . date('YmdHis') . '.zip';
$zipPath = __DIR__ . '/public/uploads/' . $zipName;

echo "\nZIP Path: $zipPath\n";

$zip = new ZipArchive();
$result = $zip->open($zipPath, ZipArchive::CREATE);

if ($result !== true) {
    echo "Failed to create ZIP! Error code: $result\n";
    exit;
}

echo "ZIP created successfully!\n";

foreach ($files as $file) {
    $added = $zip->addFile($file, basename($file));
    echo "Adding " . basename($file) . ": " . ($added ? 'OK' : 'FAIL') . "\n";
}

$zip->close();

echo "\nZIP closed!\n";
echo "ZIP size: " . filesize($zipPath) . " bytes\n";
echo "Test completed successfully!\n";

// Limpa o ZIP de teste
unlink($zipPath);
echo "Test ZIP deleted.\n";
