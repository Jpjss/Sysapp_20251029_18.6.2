<?php

App::uses('AppModel', 'Model');

/**
 * GlbQuestionarioResposta Model
 *
 */
class Usuario extends AppModel {

    /**
     * Use table
     *
     * @var mixed False or table name
     */
    public $useTable = "vw_login";

    public function trocar_senha($cd_usu, $dados) {

        $SQL = "UPDATE vw_login set senha_usuario= '$dados' WHERE cd_usuario = $cd_usu; ";
		
        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            return pg_send_query($dbconn, $SQL);
        }
    }

    public function buscaCd_usuario() {

        $SQL = "select max(cd_usuario)+1 as cd_usuario from sysapp_config_user;";

        return $this->query($SQL);
    }

    public function buscaCd_empresa() {

        $SQL = "select max(cd_empresa)+1 as cd_empresa from sysapp_config_empresas;";

        return $this->query($SQL);
    }

    public function buscaInfoEmpresas() {

        $SQL = "select cd_empresa, nome_empresa from sysapp_config_empresas order by nome_empresa;";

        return $this->query($SQL);
    }

    public function buscaRelatorios() {

        $SQL = "select cd_interface, nome_interface from sysapp_controle_interface order by nome_interface;";

        return $this->query($SQL);
    }

    public function salvarUsuario($dados) {

        $cd_usuario = $dados['cd_usuario'];
        $nome_usuario = ucwords($dados['nome_usuario']);
        $login_usuario = strtolower($dados['login_usuario']);
        $senha_usuario = Security::hash($dados['senha_usuario'], 'md5', Configure::read('Security.salt'));
        $cd_usu_erp = $dados['cd_usu_erp']; //21957: Configurar acesso por loja WebApp

        $SQL = "INSERT INTO sysapp_config_user (cd_usuario, nome_usuario, login_usuario, senha_usuario, cd_usu_erp) "
                . "VALUES ('$cd_usuario', '$nome_usuario', '$login_usuario', '$senha_usuario', '$cd_usu_erp')";
		
		
        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N&atilde;o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            return pg_send_query($dbconn, $SQL);
        }
    }

    public function adicionarDatabase($dados) {
        $cd_empresa = $dados['cd_empresa'];
        $nome_empresa = ucwords($dados['nome_empresa']);
        $hostname = strtolower($dados['hostname']);
        $nome_banco = strtolower($dados['nome_banco']);
        $usuario_banco = strtolower($dados['usuario_banco']);
        $senha_banco = $this->Crypt(strtolower($dados['senha_banco']));
        $porta_banco = strtolower($dados['porta_banco']);

        $SQL = "INSERT INTO sysapp_config_empresas (cd_empresa, nome_empresa, hostname_banco, nome_banco, usuario_banco, senha_banco, porta_banco) VALUES ('$cd_empresa', '$nome_empresa', '$hostname',
    												 '$nome_banco', '$usuario_banco', '$senha_banco', '$porta_banco')";
													 

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            return pg_send_query($dbconn, $SQL);
        }
    }

    public function salvarUsuarioEmpresa($dados) {
        $cd_usuario = $dados['cd_usuario'];

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        foreach ($dados['cd_empresa'] as $cd_empresa) {
            $SQL = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) VALUES ('$cd_empresa', '$cd_usuario')";
			

            if (!pg_connection_busy($dbconn)) {
                pg_send_query($dbconn, $SQL);
                $result = pg_get_result($dbconn);
            }
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function verificaEmailUsuario($login_usuario) {

        $SQL = "select login_usuario from sysapp_config_user where login_usuario = '$login_usuario';";

        return $this->query($SQL);
    }

    public function salvarUsuarioEmpresaRelatorio($dados) {
        $cd_usuario = $dados['cd_usuario'];

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        foreach ($dados['cd_empresa'] as $cd_empresa) {
            foreach ($dados['cd_interface'] as $cd_interface) {
                $SQL = "INSERT INTO sysapp_config_user_empresas_interfaces (cd_empresa, cd_usuario, cd_interface) VALUES ('$cd_empresa', '$cd_usuario', '$cd_interface')";
                if (!pg_connection_busy($dbconn)) {
                    pg_send_query($dbconn, $SQL);
                    $result = pg_get_result($dbconn);
                }
            }
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function buscaUsuarios() {
        $SQL = "SELECT * from sysapp_config_user ORDER BY nome_usuario;";
        return $this->query($SQL);
    }

    public function buscaUsuarioAlterar($id) {
        $SQL = "SELECT cd_usuario, nome_usuario, login_usuario, cd_usu_erp from sysapp_config_user where cd_usuario = $id ORDER BY nome_usuario;";
        return $this->query($SQL);
    }

    public function buscaCdUsuarioEmpresas($id) {
        $SQL = "SELECT * from sysapp_config_user_empresas where cd_usuario = $id;";
        return $this->query($SQL);
    }

    public function buscaInfoEmpresasAlterar($id) {
        $SQL = "SELECT * from sysapp_config_empresas where cd_empresa IN($id) ORDER BY nome_empresa;";
        return $this->query($SQL);
    }

    public function buscaEmpresas($id) {
        $SQL = "SELECT cd_empresa, nome_empresa from sysapp_config_empresas where cd_empresa IN($id) ORDER BY nome_empresa;";
        return $this->query($SQL);
    }

    public function buscaCdInterface($id) {
        $SQL = "SELECT cd_interface from sysapp_config_user_empresas_interfaces where cd_usuario = $id GROUP BY cd_interface;";
        return $this->query($SQL);
    }

    public function buscaRelatoriosUsuario($id) {
        $SQL = "SELECT cd_interface, nome_interface from sysapp_controle_interface where cd_interface IN($id) ORDER BY nome_interface;";
        return $this->query($SQL);
    }

    public function alteraDadosEmpresa($dados) {
        $cd_empresa = $dados['cd_empresa'];
        $nome_empresa = $dados['nome_empresa'];
        $hostname = $dados['hostname'];
        $nome_banco = $dados['nome_banco'];
        $usuario_banco = $dados['usuario_banco'];
        $senha_banco = $dados['senha_banco'];
        $porta_banco = $dados['porta_banco'];

        $SQL = "UPDATE sysapp_config_empresas SET cd_empresa = '$cd_empresa', nome_empresa = '$nome_empresa', hostname_banco = '$hostname', nome_banco = '$nome_banco', usuario_banco = '$usuario_banco', senha_banco = '$senha_banco', porta_banco = '$porta_banco' WHERE cd_empresa = '$cd_empresa'";

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            return pg_send_query($dbconn, $SQL);
        }
    }

    public function alterarUsuario($dados) {
        App::uses('Security', 'Utility');
        $cd_usuario = $dados['cd_usuario'];
        $nome_usuario = ucwords($dados['nome_usuario']);
        $login_usuario = strtolower($dados['login_usuario']);
        $cd_usu_erp = $dados['cd_usu_erp'];

        if (isset($dados['senha_usuario'])) {
            
			
			$senha_usuario = Security::hash($dados['senha_usuario'], 'md5', Configure::read('Security.salt'));
			

			///23358: Usuário SysApp retornando Usuário ou senha incorreta!	
			if (isset($dados['cd_usu_erp'])) {
			
				$SQL = "UPDATE sysapp_config_user SET cd_usuario = '$cd_usuario', nome_usuario = '$nome_usuario', login_usuario = '$login_usuario', senha_usuario = '$senha_usuario',  cd_usu_erp  = '$cd_usu_erp' WHERE cd_usuario = '$cd_usuario'";
			
			}else{
				
				$SQL = "UPDATE sysapp_config_user SET cd_usuario = '$cd_usuario', nome_usuario = '$nome_usuario', login_usuario = '$login_usuario', senha_usuario = '$senha_usuario' WHERE cd_usuario = '$cd_usuario'";
			
			}
			
			
			           
  } else {

            $SQL = "UPDATE sysapp_config_user SET cd_usuario = '$cd_usuario', nome_usuario = '$nome_usuario', login_usuario = '$login_usuario', cd_usu_erp  = '$cd_usu_erp' WHERE cd_usuario = '$cd_usuario'";
        }
		

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);

            return pg_send_query($dbconn, $SQL);
        }
    }

    public function alterarUsuarioEmpresa($dados) {
        $cd_usuario = $dados['cd_usuario'];

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user_empresas WHERE cd_usuario = '$cd_usuario' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        foreach ($dados['cd_empresa'] as $cd_empresa) {
            $SQL = "INSERT INTO sysapp_config_user_empresas (cd_empresa, cd_usuario) VALUES ('$cd_empresa', '$cd_usuario')";
            if (!pg_connection_busy($dbconn)) {
                pg_send_query($dbconn, $SQL);
                $result = pg_get_result($dbconn);
            }
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function alterarUsuarioEmpresaRelatorio($dados) {
        $cd_usuario = $dados['cd_usuario'];

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = '$cd_usuario' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }

        foreach ($dados['cd_empresa'] as $cd_empresa) {
            foreach ($dados['cd_interface'] as $cd_interface) {
                $SQL = "INSERT INTO sysapp_config_user_empresas_interfaces (cd_empresa, cd_usuario, cd_interface) VALUES ('$cd_empresa', '$cd_usuario', '$cd_interface')";
                if (!pg_connection_busy($dbconn)) {
                    pg_send_query($dbconn, $SQL);
                    $result = pg_get_result($dbconn);
                }
            }
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function excluirUsuario($dados) {
        $cd_usuario = $dados;

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user WHERE cd_usuario = '$cd_usuario' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function excluirDB($dados) {

        $cd_empresa = $dados;

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_empresas WHERE cd_empresa = '$cd_empresa' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function excluirDBUsuario($dados) {

        $cd_empresa = $dados;

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user_empresas WHERE cd_empresa = '$cd_empresa' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function excluirDBUsuarioRelatorio($dados) {

        $cd_empresa = $dados;

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_empresa = '$cd_empresa' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function excluirUsuarioEmpresa($dados) {
        $cd_usuario = $dados;

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user_empresas WHERE cd_usuario = '$cd_usuario' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function excluirUsuarioEmpresaRelatorio($dados) {
        $cd_usuario = $dados;

        App::uses('ConnectionManager', 'Model');
        $ds = ConnectionManager::getDataSource('default');
        $databaseDados = $ds->config;

        $host = $databaseDados['host'];
        $db = $databaseDados['database'];
        $user = $databaseDados['login'];
        $password = $databaseDados['password'];
        $porta = $databaseDados['port'];
        $conn_string = "host=$host port=$porta dbname=$db user=$user password=$password";

        $myconn = pg_connect($conn_string, PGSQL_CONNECT_FORCE_NEW);

        $dbconn = pg_connect($conn_string) or die("N�o foi possivel conectar ao Database");

        if (!pg_connection_busy($dbconn)) {
            $SQL = "DELETE FROM sysapp_config_user_empresas_interfaces WHERE cd_usuario = '$cd_usuario' ";
            pg_send_query($dbconn, $SQL);
            $result = pg_get_result($dbconn);
        }
        return pg_send_query($dbconn, $SQL);
    }

    public function verificaDbUsuario($dados) {
        $cd_empresa = $dados;

        $SQL = "select cd_usuario from sysapp_config_user_empresas where cd_empresa in ($cd_empresa);";
        $usuarios = $this->query($SQL);

        $cd_usuarios = "";
        foreach ($usuarios as $valor) {
            foreach ($valor as $value) {
                $cd_usuarios .= "," . $value['cd_usuario'];
            }
        }
        $cd_usuarios = substr($cd_usuarios, 1);
        if ($cd_usuarios == false) {
            return false;
        } else {
            $SQL = "select cd_usuario, nome_usuario, login_usuario, cd_usu_erp from sysapp_config_user where cd_usuario in ($cd_usuarios);";
            return $this->query($SQL);
        }
    }

    public function usu_filial($cd_emp, $cd_filial) {

        $SQL = "";
        $SQL .= "SELECT   VW_LOGIN.CD_USUARIO , ";
        $SQL .= "         NOME_USUARIO ";
        $SQL .= "FROM     VW_LOGIN, ";
        $SQL .= "         SEGU_USU_FILIAL ";
        $SQL .= "WHERE    VW_SEGU_USU.CD_USU        = SEGU_USU_FILIAL.CD_USU ";
        $SQL .= "AND      SEGU_USU_FILIAL.CD_EMP    = $cd_emp ";
        $SQL .= "AND      SEGU_USU_FILIAL.CD_FILIAL = $cd_filial ";
        $SQL .= "AND      VW_SEGU_USU.STS_USU       = 0 ";
        $SQL .= "ORDER BY NM_USU";

        return $this->query($SQL);
    }

    public function valida_serial($cd_emp, $cd_filial) {
        $SQL = "";
        $SQL .= "SELECT serial    , ";
        $SQL .= "       dt_inicial, ";
        $SQL .= "       dt_final ";
        $SQL .= "FROM   prc_filial_registro ";
        $SQL .= "WHERE  cd_emp    = $cd_emp ";
        $SQL .= "AND    cd_filial = $cd_filial ";
        $SQL .= "AND    '" . date("Y-m-d") . "' BETWEEN dt_inicial AND dt_final";
        return $this->query($SQL);
    }

    public function usuarios_com_permissao() {
        $SQL = "";
        $SQL .= "SELECT   vw_login.nome_usuario, sysapp_config_user.cd_usuario ";
        $SQL .= "FROM     sysapp_config_user ";
        $SQL .= "         INNER JOIN vw_login ";
        $SQL .= "         ON       sysapp_config_user.cd_usuario = VW_LOGIN.CD_USUARIO ";
        $SQL .= "GROUP BY sysapp_config_user.cd_usuario, ";
        $SQL .= "         vw_login.nome_usuario";
        $SQL .= "         ORDER BY nome_usuario";
        return $this->query($SQL);
    }

    public function usuario_permissao($usuario) {
        $SQL = "";
        $SQL .= "SELECT vw_login_empresa_interface.cd_usuario, ";
        $SQL .= "       vw_login_empresa_interface.nome_interface ";
        $SQL .= "FROM   vw_login_empresa_interface ";
        $SQL .= "       INNER JOIN VW_LOGIN ";
        $SQL .= "       ON     vw_login_empresa_interface.cd_usuario = VW_LOGIN.CD_USUARIO ";
        $SQL .= "WHERE  vw_login_empresa_interface.cd_usuario        = $usuario ";
        $SQL .= "ORDER BY vw_login_empresa_interface.nome_interface";
        return $this->query($SQL);
    }

    public function inserirPermissoes($usuarios, array $permissoes) {

        if (is_array($usuarios)) {
            foreach ($usuarios as $value) {
                foreach ($permissoes as $permissao) {
                    $this->query('INSERT INTO segu_ctrl_acesso_webmais (cd_usu, interface) VALUES (' . $value . ", '" . $permissao . "')");
                }
            }
        } else {
            foreach ($permissoes as $permissao) {
                $this->query('INSERT INTO segu_ctrl_acesso_webmais (cd_usu, interface) VALUES (' . $usuarios . ", '" . $permissao . "')");
            }
        }
    }

    public function excluirPermissoes($usuario) {
        $this->query("DELETE FROM segu_ctrl_acesso_webmais WHERE cd_usu = $usuario");
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

}
