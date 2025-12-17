<?php
/**
 * Controller de Administração
 * Gerenciamento de Usuários, Empresas e Permissões
 */

require_once BASE_PATH . '/models/Usuario.php';
require_once BASE_PATH . '/models/Empresa.php';

class AdminController extends Controller {
    private $Usuario;
    private $Empresa;
    
    public function __construct() {
        parent::__construct();
        $this->Usuario = new Usuario();
        $this->Empresa = new Empresa();
    }
    
    /**
     * Dashboard Admin
     */
    public function index() {
        $this->requireAuth();
        
        $stats = [
            'total_usuarios' => $this->Usuario->count(),
            'total_empresas' => $this->Empresa->count(),
            'usuarios_ativos' => $this->Usuario->countAtivos(),
            'empresas_ativas' => $this->Empresa->countAtivas()
        ];
        
        $this->set(['stats' => $stats]);
        $this->render();
    }
    
    /**
     * Lista usuários
     */
    public function usuarios() {
        $this->requireAuth();
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
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
     * Criar/Editar usuário
     */
    public function usuarioForm($cd_usuario = null) {
        $this->requireAuth();
        
        $usuario = null;
        if ($cd_usuario) {
            $usuario = $this->Usuario->findById($cd_usuario);
            if (!$usuario) {
                Session::setFlash('Usuário não encontrado!', 'error');
                $this->redirect('admin/usuarios');
            }
        }
        
        if ($this->isPost()) {
            $dados = [
                'nome_usuario' => $_POST['nome_usuario'],
                'ds_login' => $_POST['ds_login'],
                'ds_email' => $_POST['ds_email'],
                'fg_ativo' => isset($_POST['fg_ativo']) ? 'S' : 'S'  // Sempre ativo por padrão
            ];
            
            // Validação: deve ter pelo menos uma empresa
            $empresas_selecionadas = $_POST['empresas'] ?? [];
            if (empty($empresas_selecionadas)) {
                Session::setFlash('Selecione pelo menos uma empresa para o usuário!', 'error');
                // Recarrega os dados do formulário
                goto render_form;
            }
            
            // Validação: deve ter pelo menos uma permissão
            $permissoes_selecionadas = $_POST['permissoes'] ?? [];
            if (empty($permissoes_selecionadas)) {
                Session::setFlash('Selecione pelo menos uma permissão para o usuário!', 'error');
                goto render_form;
            }
            
            // Se tem senha, atualiza
            if (!empty($_POST['senha_usuario'])) {
                $dados['senha_usuario'] = $_POST['senha_usuario']; // Armazena plain text
            }
            
            $cd_usuario_novo = null;
            
            if ($cd_usuario) {
                // Editar
                $dados['cd_usuario'] = $cd_usuario;
                if ($this->Usuario->update($dados)) {
                    $cd_usuario_novo = $cd_usuario;
                } else {
                    Session::setFlash('Erro ao atualizar usuário!', 'error');
                }
            } else {
                // Criar
                $cd_usuario_novo = $this->Usuario->create($dados);
                if (!$cd_usuario_novo) {
                    Session::setFlash('Erro ao criar usuário!', 'error');
                }
            }
            
            // Se salvou com sucesso, atualiza empresas e permissões
            if ($cd_usuario_novo) {
                // Salva empresas (já validadas acima)
                $this->Usuario->atualizarEmpresas($cd_usuario_novo, $empresas_selecionadas);
                
                // Salva permissões (já validadas acima)
                $this->Usuario->atualizarPermissoes($cd_usuario_novo, $permissoes_selecionadas);
                
                Session::setFlash('Usuário salvo com sucesso!', 'success');
                $this->redirect('admin/usuarios');
            }
        }
        
        render_form:
        // Buscar empresas disponíveis
        $empresas = $this->Empresa->listarTodas();
        
        // DEBUG
        error_log("=== DEBUG USUÁRIO FORM ===");
        error_log("Empresas retornadas: " . count($empresas));
        error_log("Empresas: " . print_r($empresas, true));
        
        // Buscar empresas do usuário (se editando)
        $empresas_usuario = [];
        $permissoes_usuario = [];
        
        if ($cd_usuario) {
            $empUsuario = $this->Usuario->getEmpresas($cd_usuario);
            foreach ($empUsuario as $emp) {
                $empresas_usuario[] = $emp['cd_empresa'];
            }
            
            error_log("Empresas do usuário: " . implode(', ', $empresas_usuario));
            
            $permissoes_usuario = $this->Usuario->getPermissoes($cd_usuario); // Já vem como array de strings
            
            error_log("Permissões do usuário: " . implode(', ', $permissoes_usuario));
        }
        
        $this->set([
            'usuario' => $usuario,
            'empresas' => $empresas,
            'empresas_usuario' => $empresas_usuario,
            'permissoes_usuario' => $permissoes_usuario
        ]);
        
        $this->render();
    }
    
    /**
     * Excluir usuário
     */
    public function usuarioDelete($cd_usuario) {
        $this->requireAuth();
        
        if (!$cd_usuario) {
            Session::setFlash('Usuário não encontrado!', 'error');
            $this->redirect('admin/usuarios');
        }
        
        if ($this->Usuario->delete($cd_usuario)) {
            Session::setFlash('Usuário excluído com sucesso!', 'success');
        } else {
            Session::setFlash('Erro ao excluir usuário!', 'error');
        }
        
        $this->redirect('admin/usuarios');
    }
    
    /**
     * Lista empresas
     */
    public function empresas() {
        $this->requireAuth();
        
        // APENAS O USUÁRIO ADMIN PODE VER EMPRESAS/DATABASES
        $cd_usuario_logado = Session::read('Questionarios.cd_usu');
        if ($cd_usuario_logado != 1) {
            Session::setFlash('Apenas o administrador pode gerenciar empresas/databases!', 'error');
            $this->redirect('relatorios/index');
            return;
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $empresas = $this->Empresa->listar($limit, $offset);
        $total = $this->Empresa->count();
        $totalPages = ceil($total / $limit);
        
        $this->set([
            'empresas' => $empresas,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
        
        $this->render();
    }
    
    /**
     * Criar/Editar empresa
     */
    public function empresaForm($cd_empresa = null) {
        $this->requireAuth();
        
        // APENAS O USUÁRIO ADMIN PODE CRIAR/EDITAR EMPRESAS
        $cd_usuario_logado = Session::read('Questionarios.cd_usu');
        if ($cd_usuario_logado != 1) {
            Session::setFlash('Apenas o administrador pode gerenciar empresas/databases!', 'error');
            $this->redirect('relatorios/index');
            return;
        }
        
        $empresa = null;
        if ($cd_empresa) {
            $empresa = $this->Empresa->findById($cd_empresa);
            if (!$empresa) {
                Session::setFlash('Empresa não encontrada!', 'error');
                $this->redirect('admin/empresas');
            }
        }
        
        if ($this->isPost()) {
            $dados = [
                'nm_empresa' => $_POST['nm_empresa'],
                'nm_banco' => $_POST['nm_banco'],
                'nm_servidor' => $_POST['nm_servidor'],
                'nm_porta' => $_POST['nm_porta'],
                'nm_usuario_banco' => $_POST['nm_usuario_banco'],
                'fg_ativa' => isset($_POST['fg_ativa']) ? 'S' : 'N'
            ];
            
            // Se tem senha, criptografa
            if (!empty($_POST['senha_banco'])) {
                require_once BASE_PATH . '/core/Security.php';
                $dados['senha_banco'] = Security::encrypt($_POST['senha_banco']);
            }
            
            if ($cd_empresa) {
                // Editar
                $dados['cd_empresa'] = $cd_empresa;
                if ($this->Empresa->update($dados)) {
                    Session::setFlash('Empresa atualizada com sucesso!', 'success');
                    $this->redirect('admin/empresas');
                } else {
                    Session::setFlash('Erro ao atualizar empresa!', 'error');
                }
            } else {
                // Criar
                if ($this->Empresa->create($dados)) {
                    Session::setFlash('Empresa criada com sucesso!', 'success');
                    $this->redirect('admin/empresas');
                } else {
                    Session::setFlash('Erro ao criar empresa!', 'error');
                }
            }
        }
        
        $this->set(['empresa' => $empresa]);
        $this->render();
    }
    
    /**
     * Testar conexão da empresa
     */
    public function empresaTestarConexao($cd_empresa) {
        $this->requireAuth();
        $this->layout = false;
        
        header('Content-Type: application/json');
        
        if (!$cd_empresa) {
            echo json_encode(['success' => false, 'error' => 'Empresa não encontrada']);
            exit;
        }
        
        $empresa = $this->Empresa->findById($cd_empresa);
        
        if (!$empresa) {
            echo json_encode(['success' => false, 'error' => 'Empresa não encontrada']);
            exit;
        }
        
        // Tenta conectar
        require_once BASE_PATH . '/core/Security.php';
        $senha = Security::decrypt($empresa['senha_banco']);
        
        $connString = "host={$empresa['nm_servidor']} port={$empresa['nm_porta']} dbname={$empresa['nm_banco']} user={$empresa['nm_usuario_banco']} password={$senha}";
        
        $conn = @pg_connect($connString);
        
        if ($conn) {
            pg_close($conn);
            echo json_encode(['success' => true, 'message' => 'Conexão bem-sucedida!']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha na conexão: ' . pg_last_error()]);
        }
        
        exit;
    }
    
    /**
     * Vincular usuário a empresas
     */
    public function usuarioEmpresasForm($cd_usuario) {
        $this->requireAuth();
        
        if (!$cd_usuario) {
            Session::setFlash('Usuário não encontrado!', 'error');
            $this->redirect('admin/usuarios');
        }
        
        $usuario = $this->Usuario->findById($cd_usuario);
        
        if (!$usuario) {
            Session::setFlash('Usuário não encontrado!', 'error');
            $this->redirect('admin/usuarios');
        }
        
        if ($this->isPost()) {
            $empresas_selecionadas = isset($_POST['empresas']) ? $_POST['empresas'] : [];
            
            if ($this->Usuario->vincularEmpresas($cd_usuario, $empresas_selecionadas)) {
                Session::setFlash('Empresas vinculadas com sucesso!', 'success');
                $this->redirect('admin/usuarios');
            } else {
                Session::setFlash('Erro ao vincular empresas!', 'error');
            }
        }
        
        $empresas = $this->Empresa->listarTodas();
        $empresasUsuario = $this->Usuario->getEmpresas($cd_usuario);
        
        // Criar array de IDs para facilitar o check
        $empresasUsuarioIds = array_column($empresasUsuario, 'cd_empresa');
        
        $this->set([
            'usuario' => $usuario,
            'empresas' => $empresas,
            'empresasUsuarioIds' => $empresasUsuarioIds
        ]);
        
        $this->render();
    }
}
