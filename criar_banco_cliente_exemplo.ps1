# Script para criar banco de dados de cliente exemplo no PostgreSQL
# Execute este script para criar um banco de dados fictício de teste

Write-Host "=== Criando Banco de Dados de Cliente Exemplo ===" -ForegroundColor Cyan
Write-Host ""

# Configurações
$PGPASSWORD = "systec"
$env:PGPASSWORD = $PGPASSWORD

$postgresPath = "C:\Program Files\PostgreSQL\18\bin"
if (-not (Test-Path $postgresPath)) {
    $postgresPath = "C:\Program Files\PostgreSQL\17\bin"
}

$psqlPath = Join-Path $postgresPath "psql.exe"

# Informações do banco de dados de exemplo
$dbName = "erp_cliente_exemplo"
$dbUser = "admin_cliente"
$dbPassword = "cliente@2025"

Write-Host "Informações do Banco de Dados:" -ForegroundColor Yellow
Write-Host "--------------------------------"
Write-Host "Nome da Empresa: Empresa Exemplo LTDA"
Write-Host "Host do Banco: localhost"
Write-Host "Nome do Database: $dbName"
Write-Host "Usuário: $dbUser"
Write-Host "Senha: $dbPassword"
Write-Host "Porta: 5432"
Write-Host "--------------------------------"
Write-Host ""

# Criar usuário
Write-Host "1. Criando usuário '$dbUser'..." -ForegroundColor Green
$createUserSQL = "CREATE USER $dbUser WITH PASSWORD '$dbPassword';"
& $psqlPath -U postgres -d postgres -c $createUserSQL 2>$null

# Criar banco de dados
Write-Host "2. Criando banco de dados '$dbName'..." -ForegroundColor Green
$createDbSQL = "CREATE DATABASE $dbName OWNER $dbUser ENCODING 'UTF8';"
& $psqlPath -U postgres -d postgres -c $createDbSQL

# Conceder permissões
Write-Host "3. Concedendo permissões..." -ForegroundColor Green
$grantSQL = "GRANT ALL PRIVILEGES ON DATABASE $dbName TO $dbUser;"
& $psqlPath -U postgres -d postgres -c $grantSQL

# Criar estrutura de tabelas no novo banco
Write-Host "4. Criando estrutura de tabelas..." -ForegroundColor Green

$schemaSQL = @"
-- Tabela de Pessoas/Clientes
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

-- Tabela de Telefones
CREATE TABLE IF NOT EXISTS glb_pessoa_telefone (
    cd_telefone SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    nr_telefone VARCHAR(20),
    tp_telefone VARCHAR(20)
);

-- Tabela de Endereços
CREATE TABLE IF NOT EXISTS glb_pessoa_endereco (
    cd_endereco SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    ds_logradouro VARCHAR(200),
    nr_numero VARCHAR(10),
    ds_complemento VARCHAR(100),
    ds_bairro VARCHAR(100),
    ds_cidade VARCHAR(100),
    ds_uf VARCHAR(2),
    nr_cep VARCHAR(10)
);

-- Tabela de Questionários
CREATE TABLE IF NOT EXISTS glb_questionario (
    cd_questionario SERIAL PRIMARY KEY,
    nm_questionario VARCHAR(200) NOT NULL,
    ds_questionario TEXT,
    fg_ativo CHAR(1) DEFAULT 'S',
    dt_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de Perguntas
CREATE TABLE IF NOT EXISTS glb_questionario_pergunta (
    cd_pergunta SERIAL PRIMARY KEY,
    cd_questionario INTEGER REFERENCES glb_questionario(cd_questionario),
    ds_pergunta TEXT NOT NULL,
    tp_resposta VARCHAR(20),
    nr_ordem INTEGER
);

-- Tabela de Respostas
CREATE TABLE IF NOT EXISTS glb_questionario_resposta (
    cd_resposta SERIAL PRIMARY KEY,
    cd_pessoa INTEGER REFERENCES glb_pessoa(cd_pessoa),
    cd_pergunta INTEGER REFERENCES glb_questionario_pergunta(cd_pergunta),
    ds_resposta TEXT,
    dt_resposta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserindo dados fictícios de exemplo
INSERT INTO glb_pessoa (nm_pessoa, nm_fant, nr_cpf_cnpj, ds_email, dt_nascimento, fg_ativo) VALUES
('João Silva Santos', 'João Silva', '123.456.789-00', 'joao.silva@email.com', '1985-03-15', 'S'),
('Maria Oliveira Costa', 'Maria Oliveira', '987.654.321-00', 'maria.oliveira@email.com', '1990-07-22', 'S'),
('Pedro Santos Almeida', 'Pedro Santos', '456.789.123-00', 'pedro.almeida@email.com', '1988-11-10', 'S'),
('Ana Paula Rodrigues', 'Ana Paula', '789.123.456-00', 'ana.rodrigues@email.com', '1992-05-18', 'S'),
('Carlos Eduardo Souza', 'Carlos Souza', '321.654.987-00', 'carlos.souza@email.com', '1987-09-25', 'S'),
('Fernanda Lima Martins', 'Fernanda Lima', '654.987.321-00', 'fernanda.lima@email.com', '1995-01-30', 'S'),
('Ricardo Pereira Nunes', 'Ricardo Pereira', '147.258.369-00', 'ricardo.nunes@email.com', '1983-12-08', 'S'),
('Juliana Costa Ferreira', 'Juliana Costa', '258.369.147-00', 'juliana.ferreira@email.com', '1991-04-12', 'S'),
('Bruno Henrique Dias', 'Bruno Dias', '369.147.258-00', 'bruno.dias@email.com', '1989-08-20', 'S'),
('Camila Aparecida Silva', 'Camila Silva', '741.852.963-00', 'camila.silva@email.com', '1994-06-05', 'S');

-- Inserindo telefones
INSERT INTO glb_pessoa_telefone (cd_pessoa, nr_telefone, tp_telefone) VALUES
(1, '(11) 98765-4321', 'Celular'),
(1, '(11) 3456-7890', 'Comercial'),
(2, '(21) 99876-5432', 'Celular'),
(3, '(31) 98765-1234', 'Celular'),
(4, '(41) 99123-4567', 'Celular'),
(5, '(51) 98456-7890', 'Celular'),
(6, '(61) 99234-5678', 'Celular'),
(7, '(71) 98567-8901', 'Celular'),
(8, '(81) 99345-6789', 'Celular'),
(9, '(91) 98678-9012', 'Celular'),
(10, '(11) 99456-7890', 'Celular');

-- Inserindo endereços
INSERT INTO glb_pessoa_endereco (cd_pessoa, ds_logradouro, nr_numero, ds_bairro, ds_cidade, ds_uf, nr_cep) VALUES
(1, 'Rua das Flores', '123', 'Centro', 'São Paulo', 'SP', '01234-567'),
(2, 'Av. Atlântica', '456', 'Copacabana', 'Rio de Janeiro', 'RJ', '22070-001'),
(3, 'Rua da Bahia', '789', 'Centro', 'Belo Horizonte', 'MG', '30160-011'),
(4, 'Av. Cândido de Abreu', '321', 'Centro Cívico', 'Curitiba', 'PR', '80530-000'),
(5, 'Av. Borges de Medeiros', '654', 'Centro', 'Porto Alegre', 'RS', '90020-025'),
(6, 'Esplanada dos Ministérios', '147', 'Zona Cívico-Administrativa', 'Brasília', 'DF', '70050-000'),
(7, 'Av. Sete de Setembro', '258', 'Comércio', 'Salvador', 'BA', '40060-001'),
(8, 'Av. Boa Viagem', '369', 'Boa Viagem', 'Recife', 'PE', '51021-000'),
(9, 'Av. Presidente Vargas', '741', 'Campina', 'Belém', 'PA', '66010-000'),
(10, 'Av. Paulista', '852', 'Bela Vista', 'São Paulo', 'SP', '01310-100');

-- Criando questionário de exemplo
INSERT INTO glb_questionario (nm_questionario, ds_questionario, fg_ativo) VALUES
('Questionário de Satisfação', 'Avaliação de satisfação do cliente', 'S'),
('Pesquisa de Preferências', 'Levantamento de preferências de produtos', 'S');

-- Criando perguntas
INSERT INTO glb_questionario_pergunta (cd_questionario, ds_pergunta, tp_resposta, nr_ordem) VALUES
(1, 'Como você avalia nosso atendimento?', 'texto', 1),
(1, 'Você recomendaria nossos serviços?', 'simNao', 2),
(1, 'Qual sua nota geral de 0 a 10?', 'numero', 3),
(2, 'Qual categoria de produtos você mais compra?', 'texto', 1),
(2, 'Com que frequência você compra conosco?', 'texto', 2);

-- Inserindo respostas de exemplo
INSERT INTO glb_questionario_resposta (cd_pessoa, cd_pergunta, ds_resposta, dt_resposta) VALUES
(1, 1, 'Excelente atendimento, muito atenciosos', CURRENT_TIMESTAMP - INTERVAL '5 days'),
(1, 2, 'Sim', CURRENT_TIMESTAMP - INTERVAL '5 days'),
(1, 3, '9', CURRENT_TIMESTAMP - INTERVAL '5 days'),
(2, 1, 'Bom atendimento, mas pode melhorar', CURRENT_TIMESTAMP - INTERVAL '3 days'),
(2, 2, 'Sim', CURRENT_TIMESTAMP - INTERVAL '3 days'),
(2, 3, '8', CURRENT_TIMESTAMP - INTERVAL '3 days'),
(3, 4, 'Eletrônicos', CURRENT_TIMESTAMP - INTERVAL '2 days'),
(3, 5, 'Mensalmente', CURRENT_TIMESTAMP - INTERVAL '2 days');

-- Criando índices para performance
CREATE INDEX idx_pessoa_nome ON glb_pessoa(nm_pessoa);
CREATE INDEX idx_pessoa_cpf ON glb_pessoa(nr_cpf_cnpj);
CREATE INDEX idx_telefone_pessoa ON glb_pessoa_telefone(cd_pessoa);
CREATE INDEX idx_endereco_pessoa ON glb_pessoa_endereco(cd_pessoa);
CREATE INDEX idx_resposta_pessoa ON glb_questionario_resposta(cd_pessoa);

-- Concedendo permissões nas tabelas
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO $dbUser;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO $dbUser;
"@

# Salvar SQL em arquivo temporário
$tempSqlFile = [System.IO.Path]::GetTempFileName() + ".sql"
$schemaSQL | Out-File -FilePath $tempSqlFile -Encoding UTF8

# Executar script SQL
& $psqlPath -U postgres -d $dbName -f $tempSqlFile

# Remover arquivo temporário
Remove-Item $tempSqlFile -ErrorAction SilentlyContinue

Write-Host ""
Write-Host "=== Banco de Dados Criado com Sucesso! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Estatísticas do banco:" -ForegroundColor Cyan
Write-Host "- 10 clientes cadastrados"
Write-Host "- 11 telefones registrados"
Write-Host "- 10 endereços completos"
Write-Host "- 2 questionários ativos"
Write-Host "- 5 perguntas configuradas"
Write-Host "- 8 respostas de exemplo"
Write-Host ""
Write-Host "=== USE ESTAS INFORMAÇÕES NA ABA 'CRIAR DATABASE' ===" -ForegroundColor Yellow
Write-Host ""
Write-Host "📋 COPIE E COLE NO FORMULÁRIO:" -ForegroundColor White -BackgroundColor DarkBlue
Write-Host ""
Write-Host "  Nome da Empresa: " -NoNewline -ForegroundColor White
Write-Host "Empresa Exemplo LTDA" -ForegroundColor Cyan
Write-Host "  Host do Banco: " -NoNewline -ForegroundColor White
Write-Host "localhost" -ForegroundColor Cyan
Write-Host "  Nome do Database: " -NoNewline -ForegroundColor White
Write-Host "$dbName" -ForegroundColor Cyan
Write-Host "  Usuário: " -NoNewline -ForegroundColor White
Write-Host "$dbUser" -ForegroundColor Cyan
Write-Host "  Senha: " -NoNewline -ForegroundColor White
Write-Host "$dbPassword" -ForegroundColor Cyan
Write-Host "  Porta: " -NoNewline -ForegroundColor White
Write-Host "5432" -ForegroundColor Cyan
Write-Host ""
Write-Host "=== Pressione qualquer tecla para sair ===" -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
