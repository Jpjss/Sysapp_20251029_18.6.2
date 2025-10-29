# Script para vincular usuario admin ao banco de dados recém-criado
Write-Host "=== Vinculando Usuario Admin ao Novo Banco ===" -ForegroundColor Cyan

$env:PGPASSWORD = "systec"
$postgresPath = "C:\Program Files\PostgreSQL\18\bin"
if (-not (Test-Path $postgresPath)) {
    $postgresPath = "C:\Program Files\PostgreSQL\17\bin"
}
$psqlPath = Join-Path $postgresPath "psql.exe"

Write-Host ""
Write-Host "Verificando empresa cadastrada..." -ForegroundColor Green

$sql = @"
-- Busca o codigo da empresa recém-cadastrada
DO `$`$
DECLARE
    v_cd_empresa INTEGER;
    v_cd_usuario INTEGER := 1; -- usuario admin
BEGIN
    -- Busca a empresa 'Empresa Exemplo LTDA'
    SELECT cd_empresa INTO v_cd_empresa 
    FROM sysapp_config_empresas 
    WHERE nm_empresa = 'Empresa Exemplo LTDA'
    LIMIT 1;
    
    IF v_cd_empresa IS NOT NULL THEN
        -- Vincula usuario à empresa
        INSERT INTO sysapp_config_user_empresas (cd_usuario, cd_empresa, fg_ativo)
        VALUES (v_cd_usuario, v_cd_empresa, 'S')
        ON CONFLICT DO NOTHING;
        
        -- Vincula usuario à empresa em todas as interfaces
        INSERT INTO sysapp_config_user_empresas_interfaces (cd_usuario, cd_empresa, cd_interface, fg_ativo)
        SELECT v_cd_usuario, v_cd_empresa, cd_interface, 'S'
        FROM sysapp_controle_interface
        WHERE fg_ativo = 'S'
        ON CONFLICT DO NOTHING;
        
        RAISE NOTICE 'Usuario admin vinculado à empresa % com sucesso!', v_cd_empresa;
    ELSE
        RAISE NOTICE 'Empresa nao encontrada. Verifique se foi cadastrada corretamente.';
    END IF;
END `$`$;

-- Exibe as empresas do usuario admin
SELECT ce.cd_empresa, ce.nm_empresa, ce.ds_banco
FROM sysapp_config_empresas ce
INNER JOIN sysapp_config_user_empresas cue ON ce.cd_empresa = cue.cd_empresa
WHERE cue.cd_usuario = 1 AND cue.fg_ativo = 'S'
ORDER BY ce.nm_empresa;
"@

$tempFile = [System.IO.Path]::GetTempFileName()
$sql | Out-File -FilePath $tempFile -Encoding ASCII
& $psqlPath -U postgres -d sysapp -f $tempFile
Remove-Item $tempFile

Write-Host ""
Write-Host "=== Vinculacao Concluida! ===" -ForegroundColor Green
Write-Host ""
Write-Host "PROXIMOS PASSOS:" -ForegroundColor Yellow
Write-Host "1. Faca LOGOUT do sistema (clique em 'Sair' no menu)" -ForegroundColor Cyan
Write-Host "2. Faca LOGIN novamente com admin/admin" -ForegroundColor Cyan
Write-Host "3. Voce vera uma tela para SELECIONAR A EMPRESA" -ForegroundColor Cyan
Write-Host "4. Escolha 'Empresa Exemplo LTDA'" -ForegroundColor Cyan
Write-Host "5. Agora voce vera os 5 clientes cadastrados!" -ForegroundColor Cyan
Write-Host ""
