<?php
/**
 * Inicialização com configurações otimizadas para processamento de XMLs
 * Inclua este arquivo no início do XmlController
 */

// Aumenta limites PHP para grandes volumes
ini_set('upload_max_filesize', '500M');
ini_set('post_max_size', '600M');
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 600);
ini_set('max_input_time', 600);
ini_set('max_file_uploads', 5000);

// Otimizações de memória
ini_set('zend.enable_gc', 1); // Ativa garbage collector
gc_enable();

// Log de configurações aplicadas
error_log("XML Config: Limites aumentados - Memory: " . ini_get('memory_limit') . 
          ", Max Files: " . ini_get('max_file_uploads') . 
          ", Exec Time: " . ini_get('max_execution_time'));
