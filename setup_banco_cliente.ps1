# Script para criar banco de dados de cliente exemplo
Write-Host "=== Criando Banco de Dados de Cliente Exemplo ===" -ForegroundColor Cyan

$env:PGPASSWORD = "systec"
$postgresPath = "C:\Program Files\PostgreSQL\18\bin"
if (-not (Test-Path $postgresPath)) {
    $postgresPath = "C:\Program Files\PostgreSQL\17\bin"
}
$psqlPath = Join-Path $postgresPath "psql.exe"

$dbName = "erp_cliente_exemplo"
$dbUser = "admin_cliente"
$dbPassword = "cliente@2025"

Write-Host ""
Write-Host "Criando usuario..." -ForegroundColor Green
& $psqlPath -U postgres -d postgres -c "CREATE USER $dbUser WITH PASSWORD '$dbPassword';" 2>$null

Write-Host "Criando banco de dados..." -ForegroundColor Green
& $psqlPath -U postgres -d postgres -c "CREATE DATABASE $dbName OWNER $dbUser ENCODING 'UTF8';"

Write-Host "Concedendo permissoes..." -ForegroundColor Green
& $psqlPath -U postgres -d postgres -c "GRANT ALL PRIVILEGES ON DATABASE $dbName TO $dbUser;"

Write-Host "Criando tabelas e dados..." -ForegroundColor Green
$sql = @"
CREATE TABLE IF NOT EXISTS glb_pessoa (
    cd_pessoa SERIAL PRIMARY KEY,
    nm_pessoa VARCHAR(200) NOT NULL,
    nm_fant VARCHAR(200),
    nr_cpf_cnpj VARCHAR(20),
    ds_email VARCHAR(200),
    dt_nascimento DATE,
    dt_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fg_ativo CHAR(1) DEFAULT 'S'
);

CREATE TABLE IF NOT EXISTS glb_pessoa_telefone (
    cd_telefone SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    nr_telefone VARCHAR(20),
    tp_telefone VARCHAR(20)
);

CREATE TABLE IF NOT EXISTS glb_questionario (
    cd_questionario SERIAL PRIMARY KEY,
    nm_questionario VARCHAR(200) NOT NULL,
    ds_questionario TEXT,
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO glb_pessoa (nm_pessoa, nm_fant, nr_cpf_cnpj, ds_email, dt_nascimento, fg_ativo) VALUES
('Joao Silva Santos', 'Joao Silva', '123.456.789-00', 'joao.silva@email.com', '1985-03-15', 'S'),
('Maria Oliveira Costa', 'Maria Oliveira', '987.654.321-00', 'maria.oliveira@email.com', '1990-07-22', 'S'),
('Pedro Santos Almeida', 'Pedro Santos', '456.789.123-00', 'pedro.almeida@email.com', '1988-11-10', 'S'),
('Ana Paula Rodrigues', 'Ana Paula', '789.123.456-00', 'ana.rodrigues@email.com', '1992-05-18', 'S'),
('Carlos Eduardo Souza', 'Carlos Souza', '321.654.987-00', 'carlos.souza@email.com', '1987-09-25', 'S');

INSERT INTO glb_pessoa_telefone (cd_pessoa, nr_telefone, tp_telefone) VALUES
(1, '(11) 98765-4321', 'Celular'),
(2, '(21) 99876-5432', 'Celular'),
(3, '(31) 98765-1234', 'Celular'),
(4, '(41) 99123-4567', 'Celular'),
(5, '(51) 98456-7890', 'Celular');

INSERT INTO glb_questionario (nm_questionario, ds_questionario, fg_ativo) VALUES
('Questionario de Satisfacao', 'Avaliacao de satisfacao do cliente', 'S');

GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO $dbUser;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $dbUser;
"@

$tempFile = [System.IO.Path]::GetTempFileName()
$sql | Out-File -FilePath $tempFile -Encoding ASCII
& $psqlPath -U postgres -d $dbName -f $tempFile
Remove-Item $tempFile

Write-Host ""
Write-Host "=== Banco Criado com Sucesso! ===" -ForegroundColor Green
Write-Host ""
Write-Host "USE ESTAS INFORMACOES NA ABA 'CRIAR DATABASE':" -ForegroundColor Yellow
Write-Host ""
Write-Host "Nome da Empresa: Empresa Exemplo LTDA" -ForegroundColor Cyan
Write-Host "Host do Banco: localhost" -ForegroundColor Cyan
Write-Host "Nome do Database: $dbName" -ForegroundColor Cyan
Write-Host "Usuario: $dbUser" -ForegroundColor Cyan
Write-Host "Senha: $dbPassword" -ForegroundColor Cyan
Write-Host "Porta: 5432" -ForegroundColor Cyan
Write-Host ""
