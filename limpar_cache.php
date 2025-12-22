<?php
/**
 * Script para limpar cache do OPcache
 */

// Limpa o OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache limpo com sucesso!\n";
} else {
    echo "OPcache não está ativo.\n";
}

// Limpa o cache de arquivo realpath
if (function_exists('clearstatcache')) {
    clearstatcache(true);
    echo "Cache de estatísticas de arquivo limpo!\n";
}

echo "\nCache limpo. Reinicie o servidor PHP.\n";
