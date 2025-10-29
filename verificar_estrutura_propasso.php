<?php
/**
 * Script para verificar estrutura de tabelas no banco Propasso
 */

require_once 'config/database.php';
require_once 'core/Session.php';

Session::start();

$db = Database::getInstance();

// Conecta ao banco Propasso
$connected = $db->connect(
    'banco.propasso.systec.ftp.sh',
    'bd_propasso',
    'admin',
    'systec2011.',
    5432
);

if (!$connected) {
    die("❌ Erro ao conectar ao banco Propasso\n");
}

echo "✅ Conectado ao banco Propasso\n\n";

// Verifica tabelas relacionadas a questionários
$tabelas = [
    'glb_questionario',
    'glb_questionario_pergunta',
    'glb_questionario_glb_questionario_pergunta',
    'glb_questionario_resposta',
    'glb_questionario_resposta_historico',
    'vw_questionario_prox_atendimento',
    'vw_questionario_prox_atendimento_aniversariante',
    'vw_relatorio_simplificado'
];

foreach ($tabelas as $tabela) {
    echo "=== $tabela ===\n";
    
    // Verifica se existe
    $sql = "SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND table_name = '$tabela'
    ) as existe";
    
    $result = $db->fetchOne($sql);
    
    if ($result && $result['existe'] === 't') {
        echo "✅ Tabela existe\n";
        
        // Conta registros (somente para tabelas, não views)
        if (strpos($tabela, 'vw_') !== 0) {
            $count = $db->fetchOne("SELECT COUNT(*) as total FROM $tabela");
            echo "   Total de registros: " . ($count['total'] ?? 0) . "\n";
        } else {
            echo "   (View - não contabilizado)\n";
        }
        
        // Mostra estrutura
        $sql = "SELECT column_name, data_type 
                FROM information_schema.columns 
                WHERE table_name = '$tabela' 
                ORDER BY ordinal_position";
        
        $colunas = $db->fetchAll($sql);
        echo "   Colunas:\n";
        foreach ($colunas as $coluna) {
            echo "   - {$coluna['column_name']} ({$coluna['data_type']})\n";
        }
    } else {
        echo "❌ Tabela não existe\n";
    }
    
    echo "\n";
}

echo "=== FIM ===\n";
