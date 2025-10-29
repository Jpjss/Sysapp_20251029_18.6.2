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
        error_log("=== MÉTODO LOGIN CHAMADO ===");
        
        // Se já está logado, redireciona
        if (Session::isValid()) {
            error_log("Usuário já logado, redirecionando para relatorios/empresa");
            $this->redirect('relatorios/empresa');
        }
        
        $this->layout = 'login';
        
        if ($this->isPost()) {
            error_log("=== POST RECEBIDO NO LOGIN ===");
            $email = $_POST['email'] ?? '';
            $senha = $_POST['senha'] ?? '';
            
            error_log("Email: " . $email);
            
            if (empty($email) || empty($senha)) {
                error_log("Email ou senha vazios");
                Session::setFlash('Usuário e senha são necessários!', 'error');
                $this->render();
                return;
            }
            
            error_log("Buscando usuário...");
            // Busca usuário
            $configUser = $this->Usuario->findByLogin($email);
            
            error_log("Resultado findByLogin: " . print_r($configUser, true));
            
            if (!$configUser) {
                error_log("ERRO: Usuário não encontrado");
                Session::setFlash('Usuário ou senha incorreta!', 'error');
                $this->render();
                return;
            }
            
            $cd_usuario = $configUser['cd_usuario'];
            error_log("cd_usuario encontrado: " . $cd_usuario);
            
            // Busca dados completos do usuário
            error_log("Buscando dados de autenticação...");
            $usuario = $this->Usuario->findForAuth($cd_usuario);
            
            error_log("Resultado findForAuth: " . print_r($usuario, true));
            
            if (!$usuario) {
                error_log("ERRO: Dados de autenticação não encontrados");
                Session::setFlash('Usuário ou senha incorreta!', 'error');
                $this->render();
                return;
            }
            
            // Verifica senha
            $senhaHash = Security::hash($senha, 'md5', SECURITY_SALT);
            
            error_log("Senha fornecida (hash): " . $senhaHash);
            error_log("Senha no banco: " . $usuario['senha_usuario']);
            
            if ($senhaHash !== $usuario['senha_usuario']) {
                error_log("ERRO: Senha incorreta");
                Session::setFlash('Usuário ou senha incorreta!', 'error');
                $this->render();
                return;
            }
            
            error_log("Senha correta! Buscando empresas...");
            
            // Busca empresas do usuário
            $empresas = $this->Usuario->getEmpresas($cd_usuario);
            
            error_log("Empresas encontradas: " . count($empresas));
            
            if (empty($empresas)) {
                error_log("ERRO: Usuário sem empresas configuradas");
                Session::setFlash('Usuário sem empresas configuradas!', 'error');
                $this->render();
                return;
            }
            
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
            
            // LOG DE DEBUG
            error_log("=== LOGIN BEM-SUCEDIDO ===");
            error_log("cd_usuario: " . $cd_usuario);
            error_log("nome: " . $usuario['nome_usuario']);
            error_log("Quantidade de empresas: " . count($infoDb));
            
            // Se tem múltiplas empresas, salva e redireciona para seleção
            if (count($infoDb) > 1) {
                Session::write('Dados.database', $infoDb);
                error_log("Múltiplas empresas - redirecionando para relatorios/empresa");
                $this->redirect('relatorios/empresa');
            } else {
                error_log("Uma única empresa - configurando e redirecionando para relatorios/index");
                // Uma única empresa, configura direto
                $empresa = $infoDb[0];
                Session::write('Config.database', $empresa['nome_banco']);
                Session::write('Config.databasename', $empresa['nome_banco']);
                Session::write('Config.host', $empresa['hostname_banco']);
                Session::write('Config.user', $empresa['usuario_banco']);
                Session::write('Config.password', Security::decrypt($empresa['senha_banco']));
                Session::write('Config.porta', $empresa['porta_banco']);
                Session::write('Config.empresa', $empresa['nome_empresa']);
                
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
                $senhaHash = Security::hash($_POST['senha_usuario'], 'md5', SECURITY_SALT);
                
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
        $empresas = $this->Empresa->listar();
        $interfaces = $this->Interface->listar();
        
        $this->set([
            'cd_usuario' => $cd_usuario,
            'empresas' => $empresas,
            'interfaces' => $interfaces
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
                    $dados['senha_usuario'] = Security::hash($_POST['senha_usuario'], 'md5', SECURITY_SALT);
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
        
        if ($this->isAjax() && $this->isPost()) {
            $email = $_POST['login_usuario'] ?? '';
            $cd_usuario = $_POST['cd_usuario'] ?? null;
            
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
            
            // Busca próximo código de empresa
            $cd_empresa = $this->Empresa->getNextCodigo();
            
            // Prepara dados
            $dados = [
                'cd_empresa' => $cd_empresa,
                'nome_empresa' => $_POST['nome_empresa'],
                'hostname' => strtolower($_POST['hostname']),
                'nome_banco' => strtolower($_POST['nome_banco']),
                'usuario_banco' => strtolower($_POST['usuario_banco']),
                'senha_banco' => Security::encrypt($_POST['senha_banco']),
                'porta_banco' => $_POST['porta_banco']
            ];
            
            // Salva banco de dados
            if ($this->Empresa->salvar($dados)) {
                echo "1";
            } else {
                echo "0";
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
