# Script para vincular usuário admin à empresa Propasso
# Executar no PowerShell

$env:PGPASSWORD='systec'

Write-Host "=== Vinculando usuário admin à empresa Propasso ===" -ForegroundColor Green

# Conecta ao PostgreSQL usando o caminho completo
& "C:\Program Files\PostgreSQL\18\bin\psql.exe" -U postgres -d sysapp -c "
-- Vincular usuário à empresa
INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario)
SELECT 3, 1
WHERE NOT EXISTS (
    SELECT 1 FROM sysapp_config_user_empresas 
    WHERE cd_empresa = 3 AND cd_usuario = 1
);

-- Vincular às interfaces (copia as mesmas interfaces das outras empresas)
INSERT INTO sysapp_config_user_empresas_interfaces (cd_empresa, cd_usuario, cd_interface)
SELECT 3, 1, cd_interface 
FROM sysapp_config_user_empresas_interfaces 
WHERE cd_usuario = 1 AND cd_empresa = 1
ON CONFLICT DO NOTHING;

-- Verifica o resultado
SELECT 'Empresas do usuário admin:' as info;
SELECT e.cd_empresa, e.nm_empresa, e.ds_banco
FROM sysapp_config_empresas e
INNER JOIN sysapp_config_user_empresas ue ON e.cd_empresa = ue.cd_empresa
WHERE ue.cd_usuario = 1
ORDER BY e.cd_empresa;
"

Write-Host "`n=== Vinculação concluída! ===" -ForegroundColor Green
Write-Host "Agora faça logout e login novamente no sistema." -ForegroundColor Yellow
