<?php
/**
 * Controller de Usuários
 */

require_once BASE_PATH . '/models/Usuario.php';
require_once BASE_PATH . '/models/Empresa.php';
require_once BASE_PATH . '/models/Interface.php';

class UsuariosController extends Controller {
    private $Usuario;
    private $Empresa;
    private $Interface;
    
    public function __construct() {
        parent::__construct();
        $this->Usuario = new Usuario();
        $this->Empresa = new Empresa();
        $this->Interface = new InterfaceModel();
    }
    
    /**
     * Página de login
     */
    public function login() {
        // DEBUG: Log da requisição
        include_once BASE_PATH . '/log_request.php';
        
        file_put_contents(__DIR__ . '/../login_debug.log', "\n=== INÍCIO DO MÉTODO LOGIN ===\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../login_debug.log', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../login_debug.log', "POST email: " . ($_POST['email'] ?? 'não definido') . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../login_debug.log', "Session::isValid(): " . (Session::isValid() ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
        
        // Se já está logado, redireciona
        if (Session::isValid()) {
            file_put_contents(__DIR__ . '/../login_debug.log', "REDIRECIONANDO: Usuário já está logado\n", FILE_APPEND);
            $this->redirect('relatorios/empresa');
        }
        
        file_put_contents(__DIR__ . '/../login_debug.log', "Não está logado, continuando...\n", FILE_APPEND);
        
        $this->layout = 'login';
        
        if ($this->isPost()) {
            file_put_contents(__DIR__ . '/../login_debug.log', "É POST, processando login...\n", FILE_APPEND);
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            
            file_put_contents(__DIR__ . '/../login_debug.log', "\n[PASSO 1] Email: '$email' (len: ".strlen($email).") | Senha: '$senha' (len: ".strlen($senha).")\n", FILE_APPEND);
            
            if (empty($email) || empty($senha)) {
                file_put_contents(__DIR__ . '/../login_debug.log', "[FALHA] Email ou senha vazios\n", FILE_APPEND);
                Session::setFlash('Usuário e senha são necessários!', 'error');
                $this->render();
                return;
            }
            
            // Busca usuário
            $configUser = $this->Usuario->findByLogin($email);
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 2] findByLogin retornou: " . json_encode($configUser) . "\n", FILE_APPEND);
            
            if (!$configUser) {
                file_put_contents(__DIR__ . '/../login_debug.log', "[FALHA] Usuário não encontrado\n", FILE_APPEND);
                Session::setFlash('Usuário ou senha incorreta!', 'error');
                $this->render();
                return;
            }
            
            $cd_usuario = $configUser['cd_usuario'];
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 3] cd_usuario: $cd_usuario\n", FILE_APPEND);
            
            // Busca dados completos do usuário
            $usuario = $this->Usuario->findForAuth($cd_usuario);
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 4] findForAuth retornou: " . json_encode($usuario) . "\n", FILE_APPEND);
            
            if (!$usuario) {
                file_put_contents(__DIR__ . '/../login_debug.log', "[FALHA] findForAuth retornou vazio\n", FILE_APPEND);
                Session::setFlash('Usuário ou senha incorreta!', 'error');
                $this->render();
                return;
            }
            
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 5] Comparando senhas usando password_verify\n", FILE_APPEND);
            file_put_contents(__DIR__ . '/../login_debug.log', "[DEBUG] Senha digitada: '$senha'\n", FILE_APPEND);
            file_put_contents(__DIR__ . '/../login_debug.log', "[DEBUG] Hash no banco: '{$usuario['senha_usuario']}'\n", FILE_APPEND);
            
            // Verifica senha usando password_verify
            if (!password_verify($senha, $usuario['senha_usuario'])) {
                file_put_contents(__DIR__ . '/../login_debug.log', "[FALHA] Senha incorreta (password_verify retornou false)\n", FILE_APPEND);
                Session::setFlash('Usuário ou senha incorreta!', 'error');
                $this->render();
                return;
            }
            
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 6] Senha OK! Buscando empresas...\n", FILE_APPEND);
            
            // Busca empresas do usuário
            $empresas = $this->Usuario->getEmpresas($cd_usuario);
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 7] Empresas encontradas: " . count($empresas) . "\n", FILE_APPEND);
            
            // Monta lista de códigos de empresa
            $cd_empresas = [];
            foreach ($empresas as $emp) {
                $cd_empresas[] = $emp['cd_empresa'];
            }
            $cd_empresas_str = implode(',', $cd_empresas);
            
            // Busca informações das empresas
            $infoDb = $this->Usuario->getEmpresasInfo($cd_usuario, $cd_empresas_str);
            
            // Busca permissões
            $permissoes = $this->Usuario->getPermissoes($cd_usuario);
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 8] Permissões encontradas: " . count($permissoes) . "\n", FILE_APPEND);
            
            if (empty($permissoes)) {
                Session::setFlash('Usuário sem permissões configuradas!', 'error');
                $this->render();
                return;
            }
            
            // Salva dados na sessão
            Session::write('Questionarios.cd_usu', $cd_usuario);
            Session::write('Questionarios.nm_usu', $usuario['nome_usuario']);
            Session::write('Questionarios.permissoes', $permissoes);
            
            $hora = date("H:i:s");
            Session::write('Questionarios.hora_login', date("d/m/Y") . " as " . $hora);
            
            file_put_contents(__DIR__ . '/../login_debug.log', "[PASSO 9] Dados salvos na sessão. Verificando...\n", FILE_APPEND);
            file_put_contents(__DIR__ . '/../login_debug.log', "[SESSION DUMP] " . print_r($_SESSION, true) . "\n", FILE_APPEND);
            
            // Se tem múltiplas empresas, salva e redireciona para seleção
            if (count($infoDb) > 1) {
                Session::write('Dados.database', $infoDb);
                file_put_contents(__DIR__ . '/../login_debug.log', "[SUCESSO] Redirecionando para seleção de empresa (múltiplas)\n", FILE_APPEND);
                $this->redirect('relatorios/empresa');
            } else {
                // Uma única empresa, configura direto
                $empresa = $infoDb[0];
                Session::write('Config.database', $empresa['nome_banco']);
                Session::write('Config.databasename', $empresa['nome_banco']);
                Session::write('Config.host', $empresa['hostname_banco']);
                Session::write('Config.user', $empresa['usuario_banco']);
                Session::write('Config.password', Security::decrypt($empresa['senha_banco']));
                Session::write('Config.porta', $empresa['porta_banco']);
                Session::write('Config.empresa', $empresa['nome_empresa']);
                
                file_put_contents(__DIR__ . '/../login_debug.log', "[SUCESSO] Redirecionando para dashboard (empresa única)\n", FILE_APPEND);
                $this->redirect('relatorios/index');
            }
        }
        
        $this->render();
    }
    
    /**
     * Logout
     */
    public function logout() {
        Session::destroy();
        $this->redirect('usuarios/login');
    }
    
    /**
     * Trocar senha
     */
    public function changePassword() {
        $this->requireAuth();
        
        if ($this->isPost()) {
            $senhaAtual = $_POST['senha_usuario'] ?? '';
            $novaSenha = $_POST['prox_senha_usuario'] ?? '';
            $confirmaSenha = $_POST['prox_senha_usuario_confirm'] ?? '';
            
            if (empty($senhaAtual) || empty($novaSenha) || empty($confirmaSenha)) {
                Session::setFlash('Todos os campos são obrigatórios!', 'error');
                $this->render();
                return;
            }
            
            $cd_usuario = Session::read('Questionarios.cd_usu');
            
            // Verifica senha atual
            $usuario = $this->Usuario->findForAuth($cd_usuario);
            $senhaAtualHash = Security::hash($senhaAtual, 'md5', SECURITY_SALT);
            
            if ($senhaAtualHash !== $usuario['senha_usuario']) {
                Session::setFlash('Senha atual incorreta!', 'error');
                $this->render();
                return;
            }
            
            // Verifica se as senhas novas coincidem
            if ($novaSenha !== $confirmaSenha) {
                Session::setFlash('Nova senha e confirmação não coincidem!', 'error');
                $this->render();
                return;
            }
            
            // Atualiza senha
            $novaSenhaHash = Security::hash($novaSenha, 'md5', SECURITY_SALT);
            
            if ($this->Usuario->trocarSenha($cd_usuario, $novaSenhaHash)) {
                Session::setFlash('Senha atualizada com sucesso!', 'success');
            } else {
                Session::setFlash('Erro ao atualizar senha!', 'error');
            }
        }
        
        $this->render();
    }
    
    /**
     * Listar usuários
     */
    public function visualizar() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $usuarios = $this->Usuario->listar($limit, $offset);
        $total = $this->Usuario->count();
        $totalPages = ceil($total / $limit);
        
        $this->set([
            'usuarios' => $usuarios,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
        
        $this->render();
    }
    
    /**
     * Novo usuário
     */
    public function novo() {
        $this->requireAuth();
        
        // DEBUG: Log do estado inicial
        file_put_contents(__DIR__ . '/../debug_view.log', "\n=== NOVO USUARIO ===\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../debug_view.log', "Banco atual antes de reconectar: " . $this->db->getDatabase() . "\n", FILE_APPEND);
        
        // Força reconexão ao banco do sistema usando as constantes de config
        $this->db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');
        
        file_put_contents(__DIR__ . '/../debug_view.log', "Banco após reconectar: " . $this->db->getDatabase() . "\n", FILE_APPEND);
        
        if ($this->isPost()) {
            // Validações
            $erros = [];
            
            if (empty($_POST['nome_usuario'])) {
                $erros[] = 'Nome é obrigatório';
            }
            
            if (empty($_POST['login_usuario'])) {
                $erros[] = 'Login é obrigatório';
            } elseif ($this->Usuario->emailExiste($_POST['login_usuario'])) {
                $erros[] = 'Este email já está em uso';
            }
            
            if (empty($_POST['senha_usuario'])) {
                $erros[] = 'Senha é obrigatória';
            }
            
            if (empty($_POST['cd_empresa'])) {
                $erros[] = 'Selecione pelo menos uma empresa';
            }
            
            if (empty($_POST['cd_interface'])) {
                $erros[] = 'Selecione pelo menos uma permissão';
            }
            
            if (!empty($erros)) {
                Session::setFlash(implode('<br>', $erros), 'error');
            } else {
                // Salva usuário
                $cd_usuario = $this->Usuario->getNextCodigo();
                $senhaHash = password_hash($_POST['senha_usuario'], PASSWORD_DEFAULT);
                
                $dados = [
                    'cd_usuario' => $cd_usuario,
                    'nome_usuario' => $_POST['nome_usuario'],
                    'login_usuario' => $_POST['login_usuario'],
                    'senha_usuario' => $senhaHash,
                    'cd_usu_erp' => $_POST['cd_usu_erp'] ?? null
                ];
                
                if ($this->Usuario->salvar($dados)) {
                    // Salva empresas
                    $this->Usuario->salvarEmpresas($cd_usuario, $_POST['cd_empresa']);
                    
                    // Salva permissões
                    $this->Usuario->salvarPermissoes($cd_usuario, $_POST['cd_empresa'], $_POST['cd_interface']);
                    
                    Session::setFlash('Usuário cadastrado com sucesso!', 'success');
                    $this->redirect('usuarios/visualizar');
                } else {
                    Session::setFlash('Erro ao cadastrar usuário!', 'error');
                }
            }
        }
        
        // Carrega dados para o formulário
        $cd_usuario = $this->Usuario->getNextCodigo();
        
        file_put_contents(__DIR__ . '/../debug_view.log', "Buscando empresas...\n", FILE_APPEND);
        $empresas = $this->Empresa->listar();
        file_put_contents(__DIR__ . '/../debug_view.log', "Empresas encontradas: " . count($empresas) . "\n", FILE_APPEND);
        
        file_put_contents(__DIR__ . '/../debug_view.log', "Buscando interfaces...\n", FILE_APPEND);
        $interfaces = $this->Interface->listar();
        file_put_contents(__DIR__ . '/../debug_view.log', "Interfaces encontradas: " . count($interfaces) . "\n", FILE_APPEND);
        
        // DEBUG: Log para verificar se os dados estão sendo carregados
        file_put_contents(__DIR__ . '/../debug_view.log', "CD Usuario: $cd_usuario\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../debug_view.log', "Empresas: " . print_r($empresas, true) . "\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../debug_view.log', "Interfaces: " . print_r($interfaces, true) . "\n", FILE_APPEND);
        
        $this->set([
            'cd_usuario' => $cd_usuario,
            'empresas' => $empresas ?: [],
            'interfaces' => $interfaces ?: []
        ]);
        
        $this->render();
    }
    
    /**
     * Alterar usuário
     */
    public function alterar($cd_usuario = null) {
        $this->requireAuth();
        
        if (!$cd_usuario) {
            Session::setFlash('Usuário não encontrado!', 'error');
            $this->redirect('usuarios/visualizar');
        }
        
        // DEBUG: Log do estado inicial
        file_put_contents(__DIR__ . '/../debug_view.log', "\n=== ALTERAR USUARIO ===\n", FILE_APPEND);
        file_put_contents(__DIR__ . '/../debug_view.log', "Banco atual antes de reconectar: " . $this->db->getDatabase() . "\n", FILE_APPEND);
        
        // Força reconexão ao banco do sistema usando as constantes de config
        $this->db->connect('banco.propasso.systec.ftp.sh', 'bd_propasso', 'admin', 'systec2011.', '5432');
        
        file_put_contents(__DIR__ . '/../debug_view.log', "Banco após reconectar: " . $this->db->getDatabase() . "\n", FILE_APPEND);
        
        $usuario = $this->Usuario->findById($cd_usuario);
        
        if (!$usuario) {
            Session::setFlash('Usuário não encontrado!', 'error');
            $this->redirect('usuarios/visualizar');
        }
        
        if ($this->isPost()) {
            // Validações
            $erros = [];
            
            if (empty($_POST['nome_usuario'])) {
                $erros[] = 'Nome é obrigatório';
            }
            
            if (empty($_POST['login_usuario'])) {
                $erros[] = 'Login é obrigatório';
            } elseif ($this->Usuario->emailExiste($_POST['login_usuario'], $cd_usuario)) {
                $erros[] = 'Este email já está em uso';
            }
            
            if (empty($_POST['cd_empresa'])) {
                $erros[] = 'Selecione pelo menos uma empresa';
            }
            
            if (empty($_POST['cd_interface'])) {
                $erros[] = 'Selecione pelo menos uma permissão';
            }
            
            if (!empty($erros)) {
                Session::setFlash(implode('<br>', $erros), 'error');
            } else {
                // Atualiza usuário
                $dados = [
                    'cd_usuario' => $cd_usuario,
                    'nome_usuario' => $_POST['nome_usuario'],
                    'login_usuario' => $_POST['login_usuario'],
                    'cd_usu_erp' => $_POST['cd_usu_erp'] ?? null
                ];
                
                // Atualiza senha se fornecida
                if (!empty($_POST['senha_usuario'])) {
                    $dados['senha_usuario'] = password_hash($_POST['senha_usuario'], PASSWORD_DEFAULT);
                }
                
                if ($this->Usuario->atualizar($dados)) {
                    // Atualiza empresas
                    $this->Usuario->salvarEmpresas($cd_usuario, $_POST['cd_empresa']);
                    
                    // Atualiza permissões
                    $this->Usuario->salvarPermissoes($cd_usuario, $_POST['cd_empresa'], $_POST['cd_interface']);
                    
                    Session::setFlash('Usuário atualizado com sucesso!', 'success');
                    $this->redirect('usuarios/visualizar');
                } else {
                    Session::setFlash('Erro ao atualizar usuário!', 'error');
                }
            }
        }
        
        // Carrega dados para o formulário
        $empresas = $this->Empresa->listar();
        $interfaces = $this->Interface->listar();
        $empresasUsuario = $this->Usuario->getEmpresasUsuario($cd_usuario);
        $interfacesUsuario = $this->Usuario->getInterfacesUsuario($cd_usuario);
        
        $this->set([
            'usuario' => $usuario,
            'empresas' => $empresas,
            'interfaces' => $interfaces,
            'empresasUsuario' => $empresasUsuario,
            'interfacesUsuario' => $interfacesUsuario
        ]);
        
        $this->render();
    }
    
    /**
     * Excluir usuário
     */
    public function excluir($cd_usuario = null) {
        $this->requireAuth();
        
        if (!$cd_usuario) {
            Session::setFlash('Usuário não encontrado!', 'error');
            $this->redirect('usuarios/visualizar');
        }
        
        // Remove empresas
        $this->db->query("DELETE FROM sysapp_config_user_empresas WHERE cd_usuario = " . (int)$cd_usuario);
        
        // Remove permissões
        $this->db->query("DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = " . (int)$cd_usuario);
        
        // Remove usuário
        if ($this->Usuario->excluir($cd_usuario)) {
            Session::setFlash('Usuário excluído com sucesso!', 'success');
        } else {
            Session::setFlash('Erro ao excluir usuário!', 'error');
        }
        
        $this->redirect('usuarios/visualizar');
    }
    
    /**
     * Verifica email via AJAX
     */
    public function verificaEmail() {
        $this->layout = false;
        
        if ($this->isPost()) {
            $email = $_POST['login_usuario'] ?? '';
            $cd_usuario = $_POST['cd_usuario'] ?? null;
            
            if (empty($email)) {
                echo '';
                exit;
            }
            
            if ($this->Usuario->emailExiste($email, $cd_usuario)) {
                echo 'Email já em uso, escolha outro!';
            } else {
                echo 'Este email pode ser utilizado!';
            }
        }
        
        exit;
    }
    
    /**
     * Adicionar banco de dados
     */
    public function adiciona_database() {
        $this->requireAuth();
        
        // APENAS O USUÁRIO ADMIN PODE ADICIONAR DATABASES
        $cd_usuario_logado = Session::read('Questionarios.cd_usu');
        if ($cd_usuario_logado != 1) {
            Session::setFlash('Apenas o administrador pode adicionar databases!', 'error');
            $this->redirect('relatorios/index');
            return;
        }
        
        if ($this->isPost()) {
            $this->layout = false;
            
            // Validações
            $erros = [];
            
            if (empty($_POST['nome_empresa'])) {
                $erros[] = 'Nome da empresa é obrigatório';
            }
            
            if (empty($_POST['hostname'])) {
                $erros[] = 'Host é obrigatório';
            }
            
            if (empty($_POST['nome_banco'])) {
                $erros[] = 'Nome do banco é obrigatório';
            }
            
            if (empty($_POST['usuario_banco'])) {
                $erros[] = 'Usuário é obrigatório';
            }
            
            if (empty($_POST['senha_banco'])) {
                $erros[] = 'Senha é obrigatória';
            }
            
            if (empty($_POST['porta_banco'])) {
                $erros[] = 'Porta é obrigatória';
            }
            
            if (!empty($erros)) {
                echo "0";
                exit;
            }
            
            // TESTA A CONEXÃO ANTES DE SALVAR
            $testHost = trim($_POST['hostname']);
            $testDb = trim($_POST['nome_banco']);
            $testUser = trim($_POST['usuario_banco']);
            $testPass = $_POST['senha_banco']; // Não faz trim na senha para preservar espaços intencionais
            $testPort = trim($_POST['porta_banco']);
            
            // LOG DETALHADO para debug
            error_log("=== DEBUG CONEXÃO ===");
            error_log("Host recebido: '" . $_POST['hostname'] . "' (length: " . strlen($_POST['hostname']) . ")");
            error_log("DB recebido: '" . $_POST['nome_banco'] . "' (length: " . strlen($_POST['nome_banco']) . ")");
            error_log("User recebido: '" . $_POST['usuario_banco'] . "' (length: " . strlen($_POST['usuario_banco']) . ")");
            error_log("Pass recebido: '" . $_POST['senha_banco'] . "' (length: " . strlen($_POST['senha_banco']) . ")");
            error_log("Port recebido: '" . $_POST['porta_banco'] . "' (length: " . strlen($_POST['porta_banco']) . ")");
            error_log("Host após trim: '" . $testHost . "'");
            error_log("DB após trim: '" . $testDb . "'");
            error_log("User após trim: '" . $testUser . "'");
            error_log("Pass sem trim: '" . $testPass . "'");
            error_log("Port após trim: '" . $testPort . "'");
            
            $connTest = @pg_connect("host=$testHost port=$testPort dbname=$testDb user=$testUser password=$testPass", PGSQL_CONNECT_FORCE_NEW);
            
            if (!$connTest) {
                $lastError = error_get_last();
                $errorMsg = 'Não foi possível conectar ao banco de dados.';
                
                // Adiciona mais detalhes do erro se disponível
                if ($lastError && strpos($lastError['message'], 'pg_connect') !== false) {
                    // Extrai apenas a parte relevante do erro
                    if (preg_match('/FATAL:\s*(.+)$/', $lastError['message'], $matches)) {
                        $errorMsg .= ' ' . trim($matches[1]);
                    } else {
                        $errorMsg .= ' Verifique as credenciais.';
                    }
                }
                
                error_log("Erro de conexão PostgreSQL - Host: $testHost, DB: $testDb, User: $testUser, Port: $testPort");
                if ($lastError) {
                    error_log("Erro completo: " . $lastError['message']);
                }
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $errorMsg,
                    'debug' => [
                        'host' => $testHost,
                        'database' => $testDb,
                        'user' => $testUser,
                        'port' => $testPort,
                        'senha_length' => strlen($testPass)
                    ]
                ]);
                exit;
            }
            
            // Testa se a tabela glb_pessoa existe (validação de banco ERP)
            $testQuery = @pg_query($connTest, "SELECT COUNT(*) as total FROM glb_pessoa LIMIT 1");
            if (!$testQuery) {
                $pgError = pg_last_error($connTest);
                pg_close($connTest);
                
                error_log("Tabela glb_pessoa não encontrada - Erro: $pgError");
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Banco conectado, mas não é um banco ERP válido (tabela glb_pessoa não encontrada). Erro: ' . $pgError
                ]);
                exit;
            }
            
            pg_close($connTest);
            
            // Busca próximo código de empresa
            $cd_empresa = $this->Empresa->getNextCodigo();
            
            // Prepara dados
            $dados = [
                'cd_empresa' => $cd_empresa,
                'nome_empresa' => $_POST['nome_empresa'],
                'hostname' => $testHost,
                'nome_banco' => $testDb,
                'usuario_banco' => $testUser,
                'senha_banco' => Security::encrypt($testPass),
                'porta_banco' => $testPort
            ];
            
            error_log("Tentando salvar empresa com cd_empresa: $cd_empresa");
            
            // Salva banco de dados
            $resultado = $this->Empresa->salvar($dados);
            
            if ($resultado) {
                // Pega o usuário logado
                $cd_usuario = Session::read('Questionarios.cd_usu');
                
                if ($cd_usuario) {
                    try {
                        // Vincula empresa ao usuário logado
                        $sqlVincular = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) 
                                        VALUES ($cd_empresa, $cd_usuario)";
                        $this->db->query($sqlVincular);
                        
                        // Busca todas as interfaces disponíveis
                        $sqlInterfaces = "SELECT cd_interface FROM sysapp_interfaces";
                        $interfaces = $this->db->fetchAll($sqlInterfaces);
                        
                        // Dá todas as permissões para o usuário nesta empresa
                        if ($interfaces) {
                            foreach ($interfaces as $interface) {
                                $cd_interface = (int)$interface['cd_interface'];
                                $sqlPermissao = "INSERT INTO sysapp_config_user_empresas_interfaces 
                                                (cd_empresa, cd_usuario, cd_interface) 
                                                VALUES ($cd_empresa, $cd_usuario, $cd_interface)";
                                $this->db->query($sqlPermissao);
                            }
                        }
                    } catch (Exception $e) {
                        error_log("Erro ao vincular usuário à empresa: " . $e->getMessage());
                    }
                }
                
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Banco de dados cadastrado com sucesso!']);
            } else {
                $dbError = pg_last_error($this->db->getConnection());
                error_log("Falha ao salvar empresa. Erro PostgreSQL: " . $dbError);
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false, 
                    'message' => 'Erro ao salvar banco de dados no sistema. ' . ($dbError ? 'Erro: ' . $dbError : 'Verifique os logs.')
                ]);
            }
            
            exit;
        }
        
        // Busca próximo código de empresa para exibir no form
        $cd_empresa = [
            [
                [
                    'cd_empresa' => $this->Empresa->getNextCodigo()
                ]
            ]
        ];
        
        $this->set('cd_empresa', $cd_empresa);
        $this->render();
    }
}
