<?php

/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class UsuariosController extends AppController {

    public $helpers = array('Funcionalidades', 'Js');
    public $components = array('RequestHandler', 'Funcionalidades', 'Paginator');

    public function listar_combo() {
        $this->layout = false;
        if ($this->RequestHandler->isAjax()) {
            if (isset($this->request->data["cd_filial"])) {
//                $retorno = $this->Usuario->find('list', array('conditions' => array('prc_filial' => $cd_usuario['0']['ConfigUserSysApp']['cd_usuario'], 'sts_usu' => 0), 'fields' => array('nm_usu', 'senha')));
                $usuarios = $this->Usuario->usu_filial($this->request->data["cd_emp"], $this->request->data["cd_filial"]);
                $this->set('listaUsuarios', $usuarios);
            }
        }
    }

    public function login() {

        if ($this->Session->valid()) {
            $this->Session->destroy(); // DestrÃ³i
        }

        if ($this->request->is('post')) {

            if (!empty($this->data["Usuarios"]["email"]) && !empty($this->data["Usuarios"]["senha"])) {
                $this->loadModel('ConfigUserSysApp');
	
                $cd_usuario = $this->ConfigUserSysApp->find('all', array('fields' => 'cd_usuario', 'conditions' => array('login_usuario' => strtolower($this->data["Usuarios"]["email"]))));
		
				//var_dump($cd_usuario);
				//var_dump(strtolower($this->data["Usuarios"]["email"]));
				//exit();
				
		
                if (!empty($cd_usuario)) {

                    if (is_numeric($cd_usuario['0']['ConfigUserSysApp']['cd_usuario'])) {

                        $retorno = $this->Usuario->find('first', array('conditions' => array('cd_usuario' => $cd_usuario['0']['ConfigUserSysApp']['cd_usuario']), 'fields' => array('nome_usuario', 'senha_usuario')));

                        if ($retorno != NULL) {

                            $senhaEncript = Security::hash($this->data["Usuarios"]["senha"], 'md5', Configure::read('Security.salt'));
                                //               	print_r($senhaEncript);
                             ///exit();
							  
							 //print_r( $retorno['Usuario']['senha_usuario'] . "SENHAB");
							  
							    
                            if ($senhaEncript == $retorno['Usuario']['senha_usuario']) {

                                $usuario = $cd_usuario['0']['ConfigUserSysApp']['cd_usuario'];

                                $this->loadModel('ConfigUserEmpresaInterface');

                                $empresa = $this->ConfigUserEmpresaInterface->find('all', array('fields' => 'DISTINCT(cd_empresa)', 'conditions' => array('cd_usuario' => $usuario)));

                                $cd_empresa = '';

                                foreach ($empresa as $key) {
                                    foreach ($key as $chave) {
                                        foreach ($chave as $value) {
                                            $cd_empresa .= "," . $value;
                                        }
                                    }
                                }

                                $cd_empresa = substr($cd_empresa, 1);

                                //retorna as empresas configuradas na tabela sysapp_config_empresas (no teste sao 15)
                                $infoDb = $this->ConfigUserEmpresaInterface->buscaInfoDb($usuario, $cd_empresa);

                                //Levantamento, para cada permissao dada ao usuario (retorno de vw_login_empresa_interface)
                                //parece q eh feita a criação da permissão. Acontece que aqui estou no login, entao a permissao deveria ser criada/revista apenas quando eu clicar em alguma empresa
                                //tentar levantar isso
                                if (isset($infoDb['1'])) {

                                    $this->loadModel("CtrlAcessoSysApp");

                                    //preenche as permisoes, sao 150 no total, pois pega de cada empresa
                                    $permissoes = $this->CtrlAcessoSysApp->find('all', array('conditions' => array('CtrlAcessoSysApp.cd_usuario' => $cd_usuario['0']['ConfigUserSysApp']['cd_usuario']), 'fields' => 'nome_interface'));

                                    //$qtdePermissao = 0;

                                    foreach ($permissoes as $value) {
                                        ///$qtdePermissao++; //150 permissões
                                        $minhasPermissoes[] = $value['CtrlAcessoSysApp']['nome_interface'];
                                    }

                                    if (empty($permissoes)) {
                                        $this->Session->setFlash('Esta p&aacute;gina n&atilde;o existe.');
                                    } else {
                                        $this->Session->write('Questionarios.permissoes', $minhasPermissoes);
                                        $this->Session->write('Questionarios.cd_usu', $cd_usuario['0']['ConfigUserSysApp']['cd_usuario']);
                                        $this->Session->write('Questionarios.nm_usu', $retorno['Usuario']['nome_usuario']);
                                        $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));
                                        $this->Session->write('Questionarios.hora_login', date("d/m/Y") . " as " . $hora);
                                        $this->Session->write('Dados.database', $infoDb);

                                        //entra aqui e "chama" o relatoriosController, funcao empresa
                                        //se retirar essa parte, nao sairmos do login. nao carrega a parte referente as empresas
                                        //achamos que é aqui
                                        $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
                                        
                                    }
                                } else {

                                    $this->loadModel("CtrlAcessoSysApp");

                                    $permissoes = $this->CtrlAcessoSysApp->find('all', array('conditions' => array('CtrlAcessoSysApp.cd_usuario' => $cd_usuario['0']['ConfigUserSysApp']['cd_usuario']), 'fields' => 'nome_interface'));

                                    foreach ($permissoes as $value) {
                                        $minhasPermissoes[] = $value['CtrlAcessoSysApp']['nome_interface'];
                                    }
                                    if (empty($permissoes)) {
                                        $this->Session->setFlash('Esta p&aacute;gina n&atilde;o existe.');
                                    } else {
                                        $this->Session->write('Questionarios.permissoes', $minhasPermissoes);
                                        $this->Session->write('Questionarios.cd_usu', $cd_usuario['0']['ConfigUserSysApp']['cd_usuario']);
                                        $this->Session->write('Questionarios.nm_usu', $retorno['Usuario']['nome_usuario']);
                                        $hora = date("H:i:s", mktime(gmdate("H") - 2, gmdate("i"), gmdate("s")));
                                        $this->Session->write('Questionarios.hora_login', date("d/m/Y") . " as " . $hora);
                                        foreach ($infoDb as $key) {
                                            foreach ($key as $value) {
                                                $hostname = $value['hostname_banco'];
                                                $nome_empresa = $value['nome_empresa'];
                                                $nome_banco = $value['nome_banco'];
                                                $usuario_banco = $value['usuario_banco'];
                                                $senha_banco = $this->DeCrypt($value['senha_banco']);
                                                $porta_banco = $value['porta_banco'];
                                            }
                                        }
                                        $this->Session->write('Config.database', $nome_banco);
                                        $this->Session->write('Config.databasename', $nome_banco);
                                        $this->Session->write('Config.host', $hostname);
                                        $this->Session->write('Config.user', $usuario_banco);
                                        $this->Session->write('Config.password', $senha_banco);
                                        $this->Session->write('Config.porta', $porta_banco);
                                        $this->redirect(array('controller' => 'Relatorios'));
                                    }
                                }
                            } else {
                                $this->Session->setFlash('Usu&aacute;rio ou senha incorreta!');
                            }
                        } else {
                            $this->Session->setFlash(utf8_encode('Usu&aacute;rio ou senha incorreta!'));
                        }
                    }
                } else {
                    $this->Session->setFlash('Usu&aacute;rio ou senha incorreta!');
                }
            } else {
                $this->Session->setFlash('Usu&aacute;rio e senha s&atilde;o necess&aacute;rios!');
            }
        }

        $this->layout = 'login';
        $this->render();
    }

    Public Function logout() {
        if ($this->Session->valid()) {
            $this->Session->destroy();
            $this->redirect(array('action' => 'login'));
        }
    }

    public function geraHtml($dados) {
        $nome_usuario = ucwords($dados['nome_usuario']);
        $login_usuario = strtolower($dados['login_usuario']);
        $senha_usuario = $dados['senha_usuario'];

        $html = "";
        $html .= "Parabéns $nome_usuario, você acaba de possuir acesso ao melhor sistema de Relat&oacute;rios, abaixo os dados de acesso: <br/>";
        $html .= "Login ID: " . $login_usuario . "<br/>";
        $html .= "Senha: " . $senha_usuario . "<br/>";
        $html .= "Acesse webapp.systec.ftp.sh e informe seus dados de login.<br/>";
        $html .= "<br/> Equipe <b>SysApp</b> agradece!  :)";

        return $html;
    }

    public function confirma_cadastro($email) {

        App::import('Vendor', 'PHPMailer', array('file' => 'PHPMailer' . DS . 'class.phpmailer.php'));

        $html = $this->geraHtml($email);

        $mail = new PHPMailer();

        $mail->IsSMTP(); // Define que a mensagem será SMTP
        $mail->Host = "smtp.systecinfo.com.br"; // Endereço do servidor SMTP
        $mail->SMTPAuth = true; // Usa autenticação SMTP? (opcional)
        $mail->Port = 587;
        $mail->Username = 'cadastro@systecinfo.com.br'; // Usuário do servidor SMTP
        $mail->Password = '56thjm,.'; // Senha do servidor SMTP
        // Define o remetente
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->From = "cadastro@systecinfo.com.br"; // Seu e-mail
        $mail->FromName = "SysApp"; // Seu nome

        $mail->AddAddress($email['login_usuario']);
        $mail->AddBCC('cadastro@systecinfo.com.br'); // Cópia Oculta

        $mail->IsHTML(true); // Define que o e-mail será enviado como HTML
        $mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)

        $mail->Subject = "Confirmação de Cadastro no SysApp"; // Assunto da mensagem
        $mail->Body = $html;
        $mail->AltBody = $html;

        // Define os anexos (opcional)
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //$mail->AddAttachment("c:/temp/documento.pdf", "novo_nome.pdf");  // Insere um anexo

        $enviado = $mail->Send();

        $mail->ClearAllRecipients();
        $mail->ClearAttachments();

        if ($enviado) {
            return "E-mail enviado com sucesso!";
        } else {
            return "Não foi possível enviar o e-mail. ";
            return "<b>Informações do erro:</b> " . $mail->ErrorInfo;
        }
    }

    public function change_password() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        $sessao = $this->Session->read();
        if ($this->request->is("POST")) {
            $senhaEncript = Security::hash($this->request->data["senha_usuario"], 'md5', Configure::read('Security.salt'));
            $senhaEncriptNova = Security::hash($this->request->data["prox_senha_usuario"], 'md5', Configure::read('Security.salt'));
            $senhaEncriptConfirm = Security::hash($this->request->data["prox_senha_usuario_confirm"], 'md5', Configure::read('Security.salt'));
            $senhaRetorno = $this->Usuario->find('first', array('conditions' => array('cd_usuario' => $sessao['Questionarios']['cd_usu']), 'fields' => array('nome_usuario', 'senha_usuario')));
            if ($senhaEncript == $senhaRetorno['Usuario']['senha_usuario']) {
                if ($senhaEncriptNova == $senhaEncriptConfirm) {
                    $dados = $senhaEncript = Security::hash($this->request->data['prox_senha_usuario'], 'md5', Configure::read('Security.salt'));
                    $this->Usuario->trocar_senha($sessao['Questionarios']['cd_usu'], $dados);
                    $this->Session->setFlash("Senha atualizada com sucesso !", 'default', array(), 'good');
                } else {
                    $this->Session->setFlash("Nova senha ou confirmação incorreta !", 'default', array(), 'bad');
                }
            } else {
                $this->Session->setFlash(utf8_encode("Senha incorreta !"), 'default', array(), 'bad');
            }
        }
    }

    public function novo_usuario() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        $cd_usuario = $this->Usuario->buscaCd_usuario();
        $cd_usuario = $cd_usuario[0][0]['cd_usuario'];

        $infoEmpresas = $this->Usuario->buscaInfoEmpresas();

        $relatorios = $this->Usuario->buscaRelatorios();

        $this->set(compact('cd_usuario', 'infoEmpresas', 'relatorios'));

        if ($this->request->is("POST")) {
            if ($this->Usuario->salvarUsuario($this->request->data) == TRUE) {
                if ($this->Usuario->salvarUsuarioEmpresa($this->request->data) == TRUE) {
                    if ($this->Usuario->salvarUsuarioEmpresaRelatorio($this->request->data) == TRUE) {
                        $this->confirma_cadastro($this->request->data);
                        $result = "1";
                        echo $result;
                    }
                }
            }
        }
    }

    public function adiciona_database() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        $cd_empresa = $this->Usuario->buscaCd_empresa();
        $this->set(compact('cd_empresa'));

        if ($this->request->is("POST")) {
            if ($this->Usuario->adicionarDatabase($this->request->data) == TRUE) {
                echo $result = "1";
            }
        }
    }

    public function listar_database() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        $this->loadModel("ConfigAcessoSysApp");
        $this->Paginator->settings = array(
            'fields' => array('cd_empresa', 'nome_empresa'),
            'order' => 'nome_empresa',
            'limit' => 10
        );

        $dados = $this->paginate('ConfigAcessoSysApp');

        $this->set('totalinsc', $this->ConfigAcessoSysApp->find('count'));

        $this->set(compact('dados'));
    }

    public function visualizar() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        $this->loadModel("SysAppUser");
        $this->Paginator->settings = array(
            'fields' => array('cd_usuario', 'nome_usuario', 'login_usuario', 'cd_usu_erp'),
            'order' => 'nome_usuario',
            'limit' => 10
        );

        $dados = $this->paginate('SysAppUser');

        $this->set('totalinsc', $this->SysAppUser->find('count'));

        $this->set(compact('dados'));
    }

    public function alterar($id) {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        if (!isset($id)) {
            $id = $this->request->data['cd_usuario'];
        }
        //---------------------BUSCA INFO do USUÁRIO----------------------------
        $dadosUsuario = $this->Usuario->buscaUsuarioAlterar($id);

        //----------------------BUSCA CD_EMPRESA do USUÁRIO------------------------
        $dadosCdEmpresas = $this->Usuario->buscaCdUsuarioEmpresas($id);
        $cdUsuarioEmpresas = Array();
        foreach ($dadosCdEmpresas as $chave) {
            foreach ($chave as $value) {
                $cdUsuarioEmpresas[] = $value['cd_empresa'];
            }
        }

        //---------------------BUSCA CD_INTERFACE do RELATÓRIO--------------------------
        $dadosCdInterface = $this->Usuario->buscaCdInterface($id);
        $cdRelatorios = Array();
        foreach ($dadosCdInterface as $chave) {
            foreach ($chave as $value) {
                $cdRelatorios[] = $value['cd_interface'];
            }
        }

        //------------------Busca Todos Relatorios e Empresas para Comparar na View---------------------
        $infoEmpresas = $this->Usuario->buscaInfoEmpresas();
        $relatorios = $this->Usuario->buscaRelatorios();

        $this->set(compact('dadosUsuario', 'cdUsuarioEmpresas', 'cdRelatorios', 'infoEmpresas', 'relatorios'));
    }

    public function alterarDatabase($id) {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
        if (!isset($id)) {
            $id = $this->request->data['cd_usuario'];
        }
        $dadosCdEmpresas = $this->Usuario->buscaInfoEmpresasAlterar($id);
        if (isset($dadosCdEmpresas)) {
            $dadosCdEmpresas[0][0]['senha_banco'] = $this->DeCrypt($dadosCdEmpresas[0][0]['senha_banco']);
        }
        $this->set(compact("dadosCdEmpresas"));
    }

    public function alterarInfoDatabase() {
        if ($this->request->is("POST")) {
            $dados = $this->request->data;
            $dados['senha_banco'] = $this->Crypt($dados['senha_banco']);
            if ($this->Usuario->alteraDadosEmpresa($dados)) {
                echo $result = "1";
            }
            $this->render('alterarDatabase');
        }
    }

    public function alterarInfosUsuario() {
        if ($this->request->is("POST")) {
            $dados = Array();

            $dados['cd_usuario'] = $this->request->data['cd_usuario'];
            $dados['nome_usuario'] = $this->request->data['nome_usuario'];
            $dados['login_usuario'] = $this->request->data['login_usuario'];
            $dados['cd_empresa'] = $this->request->data['cd_empresa'];
            $dados['cd_interface'] = $this->request->data['cd_interface'];
            $dados['cd_usu_erp'] = $this->request->data['cd_usu_erp'];

            if ($this->request->data['senha_usuario'] != "" || $this->request->data['prox_senha_usuario_confirm'] != "") {
                if ($this->request->data['senha_usuario'] == $this->request->data["prox_senha_usuario_confirm"]) {
                    $dados['senha_usuario'] = $this->request->data['senha_usuario'];
                }
            }

            if ($this->Usuario->alterarUsuario($dados) == TRUE) {
                if ($this->Usuario->alterarUsuarioEmpresa($dados) == TRUE) {
                    if ($this->Usuario->alterarUsuarioEmpresaRelatorio($dados) == TRUE) {
                        $result = "1";
                        echo $result;
                    }
                }
            }
            $this->render('alterar');
        }
    }

    public function excluir() {
        $this->render(FALSE);
        $this->layout = FALSE;
        if ($this->Usuario->excluirUsuario($this->request->data['cd_usuario']) == TRUE) {
            if ($this->Usuario->excluirUsuarioEmpresa($this->request->data['cd_usuario']) == TRUE) {
                if ($this->Usuario->excluirUsuarioEmpresaRelatorio($this->request->data['cd_usuario']) == TRUE) {
                    $result = "1";
                    echo $result;
                }
            }
        }
    }

    public function excluirDatabase() {

        if ($this->RequestHandler->isAjax()) {
            $usuarios = $this->Usuario->verificaDbUsuario($this->request->data['cd_empresa']);
            $cd_empresa = $this->request->data['cd_empresa'];
            if ($usuarios != FALSE) {
                $this->set(compact('usuarios', 'cd_empresa'));
                echo $result = "0";
            } else {
                if ($this->Usuario->excluirDB($this->request->data['cd_empresa']) == TRUE) {
                    $result = "1";
                    echo $result;
                }
            }
        }
    }

    public function excluirDatabaseConfirmado() {
        $this->render(FALSE);
        $this->layout = FALSE;
        if ($this->Usuario->excluirDB($this->request->data['cd_empresa']) == TRUE) {
            if ($this->Usuario->excluirDBUsuario($this->request->data['cd_empresa']) == TRUE) {
                if ($this->Usuario->excluirDBUsuarioRelatorio($this->request->data['cd_empresa']) == TRUE) {
                    $result = "1";
                    echo $result;
                }
            }
        }
    }

    public function verifica_email() {

        $this->render('novo_usuario');

        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }

        if ($this->request->is("POST")) {
            $login_usuario = strtolower($this->request->data['login_usuario']);
            $verificaEmail = $this->Usuario->verificaEmailUsuario($login_usuario);
            if (isset($verificaEmail[0][0]['login_usuario'])) {
                $result = "Email ja em uso, escolha outro !";
                echo $result;
                //$this->Session->setFlash("E-mail já cadastrado, por favor escolha outro !", 'default', array(), 'good');
            } else {
                $result = "Este email pode ser utilizado!";
                echo $result;
            }
        }
    }

    public function permissoes() {
        $usuarios = $this->Usuario->usuarios_com_permissao();
        $this->set(compact('usuarios'));
    }

    public function addPermissoes() {
        if ($this->request->is('post')) {
            if (isset($this->request->data['Usuarios']['usuarios']) && isset($this->request->data['Usuarios']['permissoes'])) {
                $this->Usuario->inserirPermissoes($this->request->data['Usuarios']['usuarios'], $this->request->data['Usuarios']['permissoes']);
                $this->Session->setFlash('A permissÃ£o foi salva com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'permissoes'));
            } else {
                $this->Session->setFlash('VocÃª deve selecionar no mÃ­nimo um usuÃ¡rio e uma permissÃ£o!');
            }
        }
        $usuarios = $this->Usuario->usu_filial(1, 0);
        $this->set(compact('usuarios'));
    }

    public function editPermissoes($id) {
        if ($this->request->is('post')) {
            if (isset($this->request->data['Usuarios']['cd_usu']) && isset($this->request->data['Usuarios']['permissoes'])) {
                $this->Usuario->excluirPermissoes($this->request->data['Usuarios']['cd_usu']);
                $this->Usuario->inserirPermissoes($this->request->data['Usuarios']['cd_usu'], $this->request->data['Usuarios']['permissoes']);
                $this->Session->setFlash('A permissÃ£o foi salva com sucesso!', 'sucesso');
                $this->redirect(array('action' => 'permissoes'));
            } else {
                $this->Session->setFlash('VocÃª deve selecionar no mÃ­nimo uma permissÃ£o!');
            }
        }
        $this->loadModel("SeguCtrlAcessoWebmais");
        $options = array('conditions' => array('SeguCtrlAcessoWebmais.cd_usu' => $id), 'fields' => 'interface');
        $permissoes = $this->SeguCtrlAcessoWebmais->find('all', $options);
        foreach ($permissoes as $value) {
            $minhasPermissoes[] = $value['SeguCtrlAcessoWebmais']['interface'];
        }
        $usuario = $this->Usuario->find('first', array('conditions' => array('cd_usu' => $id), 'fields' => array('nm_usu', 'cd_usu')));
        $this->set(compact('usuario', 'minhasPermissoes'));
    }

    public function deletePermissoes($id) {
        $this->Usuario->excluirPermissoes($id);
        $this->Session->setFlash('A permissÃ£o foi excluÃ­da com sucesso!', 'sucesso');
        $this->redirect(array('action' => 'permissoes'));
    }

    public function viewPermissoes($id) {
        $nomesPermissoes = array('atendimento' => 'Atendimentos', 'permissoes' => 'PermissÃµes', 'filtros' => 'Filtros', 'parametros' => 'ParÃ¢metros', 'perguntas' => 'Perguntas', 'pesquisas' => 'Pesquisas', 'pesquisaParametro' => 'Relacionar Pesquisas e ParÃ¢metros');
        $usuario = $this->Usuario->find('first', array('conditions' => array('cd_usuario' => $id), 'fields' => array('nome_usuario', 'cd_usuario')));
        $permissoes = $this->Usuario->usuario_permissao($id);
        $this->set(compact('permissoes', 'usuario', 'nomesPermissoes'));
    }

    Public Function DeCrypt($texto) {
        $G = 0;
        $salasana = 0;
        $Decrypted = '';
        for ($tt = 0; $tt < strlen($texto); $tt++) {
            $sana = ord(substr($texto, $tt, 1));
            $G = $G + 1;
            if ($G == 6) {
                $G = 0;
            }
            $X1 = 0;
            if ($G == 0) {
                $X1 = $sana + ($salasana - 2);
            }
            if ($G == 1) {
                $X1 = $sana - ($salasana - 5);
            }
            if ($G == 2) {
                $X1 = $sana + ($salasana - 4);
            }
            if ($G == 3) {
                $X1 = $sana - ($salasana - 2);
            }
            if ($G == 4) {
                $X1 = $sana + ($salasana - 3);
            }
            if ($G == 5) {
                $X1 = $sana - ($salasana - 5);
            }
            $X1 = $X1 - $G;
            $Decrypted = $Decrypted . chr($X1);
        }
        return $Decrypted;
    }

    Public Function Crypt($texto) {
        $G = 0;
        $salasana = 0;
        $Encrypted = '';
        for ($tt = 0; $tt < strlen($texto); $tt++) {
            $sana = ord(substr($texto, $tt, 1));
            $G = $G + 1;
            if ($G == 6) {
                $G = 0;
            }
            $X1 = 0;
            if ($G == 0) {
                $X1 = $sana - ($salasana - 2);
            }
            if ($G == 1) {
                $X1 = $sana + ($salasana - 5);
            }
            if ($G == 2) {
                $X1 = $sana - ($salasana - 4);
            }
            if ($G == 3) {
                $X1 = $sana + ($salasana - 2);
            }
            if ($G == 4) {
                $X1 = $sana - ($salasana - 3);
            }
            if ($G == 5) {
                $X1 = $sana + ($salasana - 5);
            }
            $X1 = $X1 + $G;
            $Encrypted = $Encrypted . chr($X1);
        }
        return $Encrypted;
    }

    public function modulos() {

        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('VocÃª nÃ£o estÃ¡ logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }
    }

}
