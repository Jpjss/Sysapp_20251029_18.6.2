<?php
/**
 * VERIFICADOR DE SCHEMAS E BANCOS DE DADOS
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/core/Session.php';

Session::start();

echo "🔍 VERIFICANDO ESTRUTURA COMPLETA DO POSTGRESQL\n\n";

try {
    if (!Session::check('Config.database')) {
        $host = defined('DB_HOST') ? DB_HOST : 'localhost';
        $database = defined('DB_NAME') ? DB_NAME : 'sysapp';
        $user = defined('DB_USER') ? DB_USER : 'postgres';
        $password = defined('DB_PASS') ? DB_PASS : 'postgres';
        $port = defined('DB_PORT') ? DB_PORT : '5432';
    } else {
        $host = Session::read('Config.host');
        $database = Session::read('Config.database');
        $user = Session::read('Config.user');
        $password = Session::read('Config.password');
        $port = Session::read('Config.porta');
    }
    
    $dsn = "pgsql:host={$host};port={$port};dbname={$database}";
    $db = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    echo "✅ Conectado: {$host}:{$port}/{$database}\n\n";
    
    // 1. Listar todos os bancos de dados
    echo "=" . str_repeat("=", 69) . "\n";
    echo "1. BANCOS DE DADOS DISPONÍVEIS:\n";
    echo str_repeat("=", 70) . "\n";
    
    $dbs = $db->query("SELECT datname FROM pg_database WHERE datistemplate = false ORDER BY datname")->fetchAll();
    foreach ($dbs as $dbinfo) {
        echo "  • {$dbinfo['datname']}\n";
    }
    
    // 2. Listar todos os schemas do banco atual
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "2. SCHEMAS NO BANCO '{$database}':\n";
    echo str_repeat("=", 70) . "\n";
    
    $schemas = $db->query("
        SELECT 
            schema_name,
            (SELECT COUNT(*) 
             FROM information_schema.tables 
             WHERE table_schema = schema_name) as total_tabelas
        FROM information_schema.schemata 
        WHERE schema_name NOT IN ('information_schema', 'pg_catalog', 'pg_toast')
        ORDER BY schema_name
    ")->fetchAll();
    
    foreach ($schemas as $schema) {
        echo sprintf("  • %-30s (%d tabelas)\n", $schema['schema_name'], $schema['total_tabelas']);
    }
    
    // 3. Listar tabelas de cada schema
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "3. TABELAS POR SCHEMA:\n";
    echo str_repeat("=", 70) . "\n";
    
    foreach ($schemas as $schema) {
        $schemaName = $schema['schema_name'];
        echo "\n📁 Schema: {$schemaName}\n";
        echo str_repeat("-", 70) . "\n";
        
        $tabelas = $db->query("
            SELECT tablename
            FROM pg_tables 
            WHERE schemaname = '{$schemaName}'
            ORDER BY tablename
        ")->fetchAll();
        
        if (empty($tabelas)) {
            echo "  (vazio)\n";
        } else {
            foreach ($tabelas as $tabela) {
                $tableName = $tabela['tablename'];
                
                try {
                    $count = $db->query("SELECT COUNT(*) FROM {$schemaName}.{$tableName}")->fetchColumn();
                    echo sprintf("  %-50s %12s registros\n", 
                        $tableName, 
                        number_format($count)
                    );
                } catch (Exception $e) {
                    echo sprintf("  %-50s %s\n", $tableName, "(erro ao contar)");
                }
            }
        }
    }
    
    // 4. Verificar se sysapp_config_empresas tem alguma info útil
    echo "\n" . str_repeat("=", 70) . "\n";
    echo "4. ANALISANDO CONFIGURAÇÃO DE EMPRESAS:\n";
    echo str_repeat("=", 70) . "\n";
    
    $empresas = $db->query("SELECT * FROM sysapp_config_empresas")->fetchAll();
    
    if (!empty($empresas)) {
        foreach ($empresas as $empresa) {
            echo "\nEmpresa encontrada:\n";
            foreach ($empresa as $campo => $valor) {
                if (!is_numeric($campo)) {
                    echo "  {$campo}: {$valor}\n";
                }
            }
        }
    } else {
        echo "Nenhuma empresa configurada.\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}
