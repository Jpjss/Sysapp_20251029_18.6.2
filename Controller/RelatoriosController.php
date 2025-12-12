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
class RelatoriosController extends AppController {

    public $helpers = array('Formatacao', 'Funcionalidades', 'Js');
    public $components = array('Funcionalidades', 'RequestHandler');

    public function beforeFilter() {
        if (!$this->Session->check('Questionarios.nm_usu')) {
            $this->Session->setFlash('Você não está logado.');
            $this->redirect(array('controller' => 'usuarios', 'action' => 'login'));
        }

        App::uses('ConnectionManager', 'Model');
        $databases = $this->Session->read('Dados.database');
        if ($this->Session->read("Config.host") == NULL) {
            if ($this->Session->read('Dados.database') != null) {
                foreach ($databases as $valor) {
                    foreach ($valor as $value) {
                        $settings = array(
                            'datasource' => 'Database/Postgres',
                            'persistent' => false,
                            'host' => $value['hostname_banco'],
                            'login' => $value['usuario_banco'],
                            'password' => $this->DeCrypt($value['senha_banco']),
                            'database' => $value['nome_banco'],
                            'port' => $value['porta_banco']
                        );
                        try {
                            if ($this->Session->check('Config.databasename')) {
                                ConnectionManager::create($value['nome_banco'], $settings);
                            }
                        } catch (Exception $e) {
                            
                        }
                    }
                }
            } else {
                $settings = array(
                    'datasource' => 'Database/Postgres',
                    'persistent' => false,
                    'host' => $this->Session->read("Config.host"),
                    'login' => $this->Session->read("Config.user"),
                    'password' => $this->Session->read("Config.password"),
                    'database' => $this->Session->read("Config.database"),
                    'port' => $this->Session->read("Config.porta")
                );
                try {
                    if ($this->Session->check('Config.databasename')) {
                        ConnectionManager::create($this->Session->read("Config.database"), $settings);
                    }
                } catch (Exception $e) {
                    
                }
            }
        } else {
            $settings = array(
                'datasource' => 'Database/Postgres',
                'persistent' => false,
                'host' => $this->Session->read("Config.host"),
                'login' => $this->Session->read("Config.user"),
                'password' => $this->Session->read("Config.password"),
                'database' => $this->Session->read("Config.database"),
                'port' => $this->Session->read("Config.porta")
            );
            try {
                if ($this->Session->check('Config.databasename')) {
                    ConnectionManager::create($this->Session->read("Config.database"), $settings);
                }
            } catch (Exception $e) {
                
            }
        }
    }

    public function controle_emails_informativo() {
        $this->loadModel("ConfigUserSysApp");
        $usuarios = $this->Relatorio->usuarios_para_permissao();
        $this->set(compact('usuarios'));

        $this->loadModel("CtrlEmailSysApp");
        $usuariosPermitidos = $this->CtrlEmailSysApp->find('all', array('fields' => array('cd_usuario', 'nome_usuario', 'email_usuario'), 'order' => 'email_usuario'));
        $this->set(compact('usuarios', 'usuariosPermitidos'));

        if ($this->request->is("POST")) {
            if (isset($this->request->data['emails'])) {
                $resultado = $this->Relatorio->salva_emails_para_informativo($this->request->data);
            }
            if (isset($this->request->data['emailsPermitidos'])) {
                $resultado = $this->Relatorio->remove_emails_do_informativo($this->request->data);
            }
        }
    }

    public function empresa() {
        $sessao = $_SESSION['Dados']['database'];
        $hostname = array();
        $nome_empresa = array();
        $nome_banco = array();
        $usuario_banco = array();
        $senha_banco = array();
        $porta_banco = array();

        foreach ($sessao as $key) {
            foreach ($key as $value) {
                $arrayFinal[] = $value;
            }
        }

        $this->set(compact('arrayFinal')); //monta o vetor com os 15 bancos existentes

        if ($this->request->is("POST")) {

            $nome_db = $this->request->data['nome_db'];

            if (isset($this->request->data['nome_db'])) {

                $this->loadModel("ConfigAcessoSysApp"); //tabela sysapp_config_empresas onde tem as empresas cadastradas

                $infoConnectDb = $this->ConfigAcessoSysApp->find('all', array('conditions' => array('nome_banco' => $nome_db)));

                foreach ($infoConnectDb as $key) {
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
                $this->Session->write('Conexao.Ativa', $nome_empresa);
            }
        }
    }

    public function conexoes() {
        $conexoes = $this->Relatorio->pega_conexoes();
        $this->set(compact('conexoes'));
    }

    public function vendas_por_vendedor() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }

        /*     	C�digo para pegar o nome da p�gina atual
         * 		
         * 		$pagina = $_SERVER['PHP_SELF'];
         *   	$tmp = explode("/", $pagina);
         *   	$pagina = end($tmp); 
         */

        if (!in_array('Vendas por Vendedor', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        //21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

        $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        $this->loadModel('Funcoes');
        $this->Funcoes->setDataSource($_SESSION['Config']['database']);
        $this->set('funcoes', $this->Funcoes->find('all', array('fields' => array('ds_funcao', 'tp_funcao'), 'group' => array('ds_funcao', 'tp_funcao'), 'order' => 'ds_funcao')));

        if ($this->request->is('post')) {

            define('__ROOT__', dirname(dirname(__FILE__)));
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/PHPJasperXML.inc.php');
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/tcpdf/tcpdf.php');

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

            if (empty($this->request->data['Relatorios']['per_ini_vendas'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['Relatorios']['per_fim_vendas'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            $cifrao = "R$";

            if (isset($this->request->data['Relatorios']['vlr_tac'])) {
                $vlr_tac = $this->request->data['Relatorios']['vlr_tac'];
            } else {
                $vlr_tac = "0";
            }

            $parametros_relatorio = Array($param_cd_filial, $param_dt_final, $param_dt_inicial, $data_formatada_final, $data_formatada_inicial, $vlr_tac);

            $dadosRelatorio = $this->Relatorio->relatorio_vendas_vendedor($parametros_relatorio);
            //var_dump($dadosRelatorio);

            if ($dadosRelatorio != FALSE) {
                $filialAnterior = -1;
                $totalPorFilial = Array();
                $totalGeralValorVendido = "";
                $totalPorFilialVlrLanc = "";
                $totalPorFilialItens = "";
                $totalPorFilialItensTroca = "";
                $totalPorFilialItensSaldo = "";
                $totalPorFilialVendas = "";
                $totalPorFilialMetas = "";
                $totalPorFilialPercentReal = "";
                $totalPorFilialVlrDesconto = "";
                $totalPorFilialVendasTrocas = "";
                $totalPorFilialNmFant = "";
                $totalPorFilialTicketMedio = "";
                $totalPorFilialVMedioProd = "";
                $totalPorFilialNmUsu = "";

                foreach ($dadosRelatorio as $key => $value) {

                    $totalGeralValorVendido += $value['vlr_lanc'];

                    if ($filialAnterior === $value['nm_fant']) {
                        $totalPorFilialVlrLanc += $value['vlr_lanc'];
                        $totalPorFilialItens += $value['itens'];
                        $totalPorFilialItensTroca += $value['itens_troca'];
                        $totalPorFilialItensSaldo += $value['itens_saldo'];
                        $totalPorFilialVendas += $value['vendas'];
                        $totalPorFilialMetas += $value['metas'];
                        $totalPorFilialPercentReal += $value['percent_real'];
                        $totalPorFilialVlrDesconto += $value['vlr_desconto'];
                        $totalPorFilialVendasTrocas += $value['vendas_trocas'];
                        $totalPorFilialNmFant = $value['nm_fant'];
                        $totalPorFilialTicketMedio = $value['ticket_medio'];
                        $totalPorFilialVMedioProd = $value['vlr_medio_prod'];
                    } else {
                        if ($totalPorFilialVlrLanc != "") {
                            $dadosRelatorioFilial[] = array('total_ticket_medio' => $totalPorFilialTicketMedio, 'total_vlr_medio_prod' => $totalPorFilialVMedioProd, 'total_vlr_lanc' => $totalPorFilialVlrLanc, 'total_itens' => $totalPorFilialItens, 'total_itens_troca' => $totalPorFilialItensTroca,
                                'total_itens_saldo' => $totalPorFilialItensSaldo, 'total_vendas' => $totalPorFilialVendas, 'total_metas' => $totalPorFilialMetas,
                                'total_percent_real' => $totalPorFilialPercentReal, 'total_vlr_desconto' => $totalPorFilialVlrDesconto, 'total_vendas_trocas' => $totalPorFilialVendasTrocas,
                                'total_nm_fant' => $totalPorFilialNmFant);
                        }
                        $totalPorFilialVlrLanc = $value['vlr_lanc'];
                        $totalPorFilialItens = $value['itens'];
                        $totalPorFilialItensTroca = $value['itens_troca'];
                        $totalPorFilialItensSaldo = $value['itens_saldo'];
                        $totalPorFilialVendas = $value['vendas'];
                        $totalPorFilialMetas = $value['metas'];
                        $totalPorFilialPercentReal = $value['percent_real'];
                        $totalPorFilialVlrDesconto = $value['vlr_desconto'];
                        $totalPorFilialVendasTrocas = $value['vendas_trocas'];
                        $totalPorFilialTicketMedio = $value['ticket_medio'];
                        $totalPorFilialVMedioProd = $value['vlr_medio_prod'];
                        $totalPorFilialNmFant = $value['nm_fant'];
                        $filialAnterior = $value['nm_fant'];
                    }
                }

                $dadosRelatorioFilial[] = array('total_ticket_medio' => $totalPorFilialTicketMedio, 'total_vlr_medio_prod' => $totalPorFilialVMedioProd, 'total_vlr_lanc' => $totalPorFilialVlrLanc, 'total_itens' => $totalPorFilialItens, 'total_itens_troca' => $totalPorFilialItensTroca,
                    'total_itens_saldo' => $totalPorFilialItensSaldo, 'total_vendas' => $totalPorFilialVendas, 'total_metas' => $totalPorFilialMetas,
                    'total_percent_real' => $totalPorFilialPercentReal, 'total_vlr_desconto' => $totalPorFilialVlrDesconto, 'total_vendas_trocas' => $totalPorFilialVendasTrocas,
                    'total_nm_fant' => $totalPorFilialNmFant);
            }

            $this->set(compact('totalGeralValorVendido', 'dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'totalPorFilial'));
            $this->render('relatorio_vendas_por_vendedor');

            /*     		$PHPJasperXML = new PHPJasperXML();
              $PHPJasperXML->arrayParameter=array("cifrao" => $cifrao,"data_formatada_inicial" => $data_formatada_inicial, "data_formatada_final" => $data_formatada_final ,"param_cd_filial"=> $param_cd_filial, "param_dt_inicial" => $param_dt_inicial, "param_dt_final" => $param_dt_final);
              $xml = simplexml_load_file('relatorio_vendas_por_vendedor_filial_analitico.jrxml'); //file name
              $PHPJasperXML->xml_dismantle($xml);
              $host = $_SESSION['Config']['host'];
              $db = $_SESSION['Config']['databasename'];
              $user = $_SESSION['Config']['user'];
              $password = $_SESSION['Config']['password'];
              $PHPJasperXML->transferDBtoArray("'".$host."'", $user, $password, "'".$db."'", "psql");//$PHPJasperXML->transferDBtoArray(url,dbuser,dbpassword,db);
              //print_r($PHPJasperXML);
              $PHPJasperXML->outpage("I"); */
        }
    }

    public function vendas_por_loja() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }

        if (!in_array('Vendas por Loja', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

             $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));
             
        $this->set(compact('filiais'));

        $this->loadModel('Funcoes');
        $this->Funcoes->setDataSource($_SESSION['Config']['database']);
        $this->set('funcoes', $this->Funcoes->find('all', array('fields' => array('ds_funcao', 'tp_funcao'), 'group' => array('ds_funcao', 'tp_funcao'), 'order' => 'ds_funcao')));

        if ($this->request->is('post')) {

            define('__ROOT__', dirname(dirname(__FILE__)));
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/PHPJasperXML.inc.php');
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/tcpdf/tcpdf.php');

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

            if (empty($this->request->data['Relatorios']['per_ini_vendas'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['Relatorios']['per_fim_vendas'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }
            
            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']) > $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas'])) {
                
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['vlr_tac'])) {
                $vlr_tac = $this->request->data['Relatorios']['vlr_tac'];
            } else {
                $vlr_tac = "0";
            }

            $parametros_relatorio = Array($param_cd_filial, $param_dt_final, $param_dt_inicial, $data_formatada_final, $data_formatada_inicial, $vlr_tac);

            $dadosRelatorioFilial = $this->Relatorio->relatorio_vendas_por_filial($parametros_relatorio);

            $totalGeralValorVendido = "";

            if ($dadosRelatorioFilial != FALSE) {
                foreach ($dadosRelatorioFilial as $value) {
                    $totalGeralValorVendido += $value['vlr_lanc'];
                }
            }

            $this->set(compact('dadosRelatorioFilial', 'data_formatada_final', 'data_formatada_inicial', 'totalGeralValorVendido'));

            $this->render('relatorio_vendas_por_loja');


            /*     		
              $PHPJasperXML = new PHPJasperXML();
              $PHPJasperXML->arrayParameter=array("data_formatada_inicial" => $data_formatada_inicial, "data_formatada_final" => $data_formatada_final ,"param_cd_filial"=> $param_cd_filial, "param_dt_inicial" => $param_dt_inicial, "param_dt_final" => $param_dt_final);
              $xml = simplexml_load_file('relatorio_vendas_por_filial_sintetico.jrxml'); //file name
              $PHPJasperXML->xml_dismantle($xml);
              $host = $_SESSION['Config']['host'];
              $db = $_SESSION['Config']['databasename'];
              $user = $_SESSION['Config']['user'];
              $password = $_SESSION['Config']['password'];
              $PHPJasperXML->transferDBtoArray("'".$host."'", $user, $password, "'".$db."'", "psql");//$PHPJasperXML->transferDBtoArray(url,dbuser,dbpassword,db);
              //print_r($PHPJasperXML);
              $PHPJasperXML->outpage("I"); */
        }
    }

    public function fluxo_recebimento_parcela_hora() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Fluxo Recebimento Parcela Hora', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

          $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is('post')) {

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            if (empty($this->request->data['Relatorios']['per_ini_fluxo'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_fluxo']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_fluxo']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['Relatorios']['per_fim_fluxo'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_fluxo']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_fluxo']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }
            
           //30252: Data de parâmetro invertida
           if ($funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_fluxo']) > $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_fluxo'])) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_fluxo']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_fluxo']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_fluxo']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_fluxo']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            $parametros = array('cd_filial' => $param_cd_filial, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);

            $dadosRelatorio = $this->Relatorio->busca_recebimento_parcela($parametros);


            if ($dadosRelatorio != FALSE) {
                $filialAnterior = -1;
                $totalGeralQuantidadeFilial = "";
                $totalGeralVlrRecebidoFilial = "";
                $totalGeralVlrMedioFilial = "";
                $totalPorFilialNmFant = "";

                foreach ($dadosRelatorio as $key => $value) {
                    //$totalGeralVlrRecebido += $value['valor_recebido'];
                    if ($filialAnterior === $value['nm_fant']) {
                        $totalGeralQuantidadeFilial += $value['quantidade'];
                        $totalGeralVlrRecebidoFilial += $value['valor_recebido'];
                        $totalPorFilialNmFant = $value['nm_fant'];
                    } else {
                        if ($totalGeralVlrRecebidoFilial != "") {
                            $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_valor_recebido' => $totalGeralVlrRecebidoFilial, 'total_nm_fant' => $totalPorFilialNmFant
                            );
                        }
                        $totalGeralVlrRecebidoFilial = $value['valor_recebido'];
                        $totalGeralQuantidadeFilial = $value['quantidade'];
                        $totalPorFilialNmFant = $value['nm_fant'];
                        $filialAnterior = $value['nm_fant'];
                    }
                }

                $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_valor_recebido' => $totalGeralVlrRecebidoFilial, 'total_nm_fant' => $totalPorFilialNmFant
                );
            }

            $this->set(compact('dadosRelatorio', 'periodo', 'data_formatada_inicial', 'data_formatada_final', 'dadosRelatorioFilial'));
            $this->render('relatorio_fluxo_recebimento_parcela_hora');
        }
    }

    public function fluxo_vendas_hora() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Fluxo Vendas Hora', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

         $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is('post')) {

            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            $parametros = array('cd_filial' => $param_cd_filial, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);

            $dadosRelatorio = $this->Relatorio->fluxo_vendas_hora($parametros);

            if ($dadosRelatorio != FALSE) {
                $filialAnterior = -1;
                $totalGeralQuantidadeFilial = "";
                $totalGeralTotalPedido = "";
                $totalGeralVlrMedioFilial = "";
                $totalPorFilialNmFant = "";

                foreach ($dadosRelatorio as $key => $value) {
                    if ($filialAnterior === $value['cd_filial']) {
                        $totalGeralQuantidadeFilial += $value['quantidade'];
                        $totalGeralTotalPedido += $value['vlr_total_pedido'];
                        $totalPorFilialNmFant = $value['cd_filial'];
                    } else {
                        if ($totalGeralTotalPedido != "") {
                            $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_vlr_total_pedido' => $totalGeralTotalPedido, 'total_cd_filial' => $totalPorFilialNmFant
                            );
                        }
                        $totalGeralTotalPedido = $value['vlr_total_pedido'];
                        $totalGeralQuantidadeFilial = $value['quantidade'];
                        $filialAnterior = $value['cd_filial'];
                    }
                }

                $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_vlr_total_pedido' => $totalGeralTotalPedido, 'total_cd_filial' => $totalPorFilialNmFant
                );
            }

            $this->set(compact('dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final'));

            $this->render("relatorio_fluxo_vendas_hora");
        }
    }

    ///11814 - Criar Relat�rio "Previs�o Financeira a Receber" para SysApp.
    public function prev_finaceira_receber() {

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->loadModel("Documento");
        $this->Documento->setDataSource($_SESSION['Config']['database']);
        $documentos = $this->Documento->find('all', array('fields' => array('cd_tipo_pgto', 'ds_tipo_pgto'), 'conditions' => array('cd_tipo_pgto' => array('2', '3', '5', '11')), 'order' => array('ds_tipo_pgto')));

        $this->set(compact('filiais', 'documentos'));

        //visualizar
        if ($this->request->is("POST")) {

            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filial = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filial .= "," . $value;
                }
                $param_cd_filial = substr($cod_filial, 1);
            }

            if (isset($this->request->data['Relatorios']['documento'])) {
                $cod_documento = '';
                foreach ($this->request->data['Relatorios']['documento'] as $value) {
                    $cod_documento .= "," . $value;
                }
                $param_cd_documento = substr($cod_documento, 1);
            }

            //define o tipo do relatorio dia/mes/ano
            if ($this->request->data['tipo_relatorio'] == 0) {
                $tipo_relatorio = "dia";
            } elseif ($this->request->data['tipo_relatorio'] == 1) {
                $tipo_relatorio = "mes";
            } else {
                $tipo_relatorio = "ano";
            }

            //define os agrupamentos
            if (isset($this->request->data['Relatorios']['agrupamento'])) {

                $agrupaPorFilial;
                $agrupaPorTpPgto;

                foreach ($this->request->data['Relatorios']['agrupamento'] as $value) {
                    if ($value == 1) {
                        $agrupaPorFilial = 1;
                    } elseif ($value == 2) {
                        $agrupaPorTpPgto = 1;
                    }
                }
            }

            //faz a consulta na Model atravez dos arrays
            $parametros = array('agrupaFilial' => isset($agrupaPorFilial), 'agrupaTpPgto' => isset($agrupaPorTpPgto), 'tp_relatorio' => $tipo_relatorio, 'cd_filial' => $param_cd_filial, 'cd_tipo_pgto' => $param_cd_documento, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);

            $dadosRelatorio = $this->Relatorio->prev_financeira_receber($parametros);

            ////////////////////////////////TESTE


            $dadosRelatorioFilial = array();
            $dadosRelatorioMes = array();
            $dadosRelatorioAno = array();
            $dadosRelatorioPgto = array();
            $dadosRelatorioDia = array();


            if ($dadosRelatorio != FALSE) {

                $filialAnterior = -1;
                $mesAnterior = -1;
                $anoAnterior = -1;
                $pgtoAnterior = -1;
                $diaAnterior = -1;

                //variaveis totalizadoras
                //De Ano
                $valorTotAnoVencto = "";
                $valorTotAnoPago = "";
                $valorTotAnoVlrAberto = "";
                $valorTotAnoQtde = "";
                $anoCorrente = "";

                //De Filial
                $valorTotFilialVencto = "";
                $valorTotFilialPago = "";
                $valorTotFilialVlrAberto = "";
                $valorTotFilialQtde = "";
                $filialCorrente = "";

                //Tipo TPGTO
                $valorTotPGTOVencto = "";
                $valorTotPGTOPago = "";
                $valorTotPGTOVlrAberto = "";
                $valorTotPGTOQtde = "";
                $pgtoCorrente = "";
                $dsPgtoCorrente = "";

                //De Mes
                $valorTotMESVencto = "";
                $valorTotMESPago = "";
                $valorTotMESVlrAberto = "";
                $valorTotMESQtde = "";
                $mesCorrente = "";
                $dsMes = "";
                $diaCorrente = "";

                $vez = "";


                foreach ($dadosRelatorio as $value) {


                    //filtro por Ano
                    if ($anoAnterior == $value['ano']) {

                        $valorTotAnoPago += $value['valor_pago_principal'];
                        $valorTotAnoQtde += $value['quantidade'];
                        $valorTotAnoVencto += $value['valor_total'];
                        $valorTotAnoVlrAberto += $value['valor_devedor'];
                        $anoCorrente = $value['ano'];

                        ///Por Filial
                        if (isset($agrupaPorFilial) == true) {

                            if ($filialAnterior == $value['cd_filial'] && $anoAnterior == $value['ano']) {

                                $valorTotFilialPago += $value['valor_pago_principal'];
                                $valorTotFilialQtde += $value['quantidade'];
                                $valorTotFilialVencto += $value['valor_total'];
                                $valorTotFilialVlrAberto += $value['valor_devedor'];
                                $filialCorrente = $value['cd_filial'];
                            } else {

                                if ($valorTotFilialPago != "") {
                                    $dadosRelatorioFilial[] = array('valorTotFilialPago' => $valorTotFilialPago, 'valorTotFilialQtde' => $valorTotFilialQtde, 'valorTotFilialVencto' => $valorTotFilialVencto, 'valorTotFilialVlrAberto' => $valorTotFilialVlrAberto, 'cd_filial' => $filialCorrente, 'ano' => $anoAnterior);
                                }

                                $valorTotFilialPago = $value['valor_pago_principal'];
                                $valorTotFilialQtde = $value['quantidade'];
                                $valorTotFilialVencto = $value['valor_total'];
                                $valorTotFilialVlrAberto = $value['valor_devedor'];
                                $filialCorrente = $value['cd_filial'];
                                $filialAnterior = $value['cd_filial'];
                            }
                        }///fim por filial
                        // ----->>>>>> PGTO <<<<-----
                        if (isset($agrupaPorTpPgto) == true) {

                            if ($pgtoAnterior == $value['cd_tipo_pgto']) {

                                $valorTotPGTOPago += $value['valor_pago_principal'];
                                $valorTotPGTOQtde += $value['quantidade'];
                                $valorTotPGTOVencto += $value['valor_total'];
                                $valorTotPGTOVlrAberto += $value['valor_devedor'];
                                $pgtoCorrente = $value['cd_tipo_pgto'];
                                $dsPgtoCorrente = $value['ds_tipo_pgto'];
                            } else {
                                if ($valorTotPGTOPago != "") {
                                    //vefirifica se existe agrupamento com filial
                                    if (isset($agrupaPorFilial) == false) {
                                        $dadosRelatorioPgto[] = array('valorTotPGTOPago' => $valorTotPGTOPago, 'valorTotPGTOQtde' => $valorTotPGTOQtde, 'valorTotPGTOVencto' => $valorTotPGTOVencto, 'valorTotPGTOVlrAberto' => $valorTotPGTOVlrAberto, 'cd_tipo_pgto' => $pgtoCorrente, 'ds_tipo_pgto' => $dsPgtoCorrente, 'ano' => $anoAnterior);
                                    } else {
                                        $dadosRelatorioPgto[] = array('valorTotPGTOPago' => $valorTotPGTOPago, 'valorTotPGTOQtde' => $valorTotPGTOQtde, 'valorTotPGTOVencto' => $valorTotPGTOVencto, 'valorTotPGTOVlrAberto' => $valorTotPGTOVlrAberto, 'cd_tipo_pgto' => $pgtoCorrente, 'ds_tipo_pgto' => $dsPgtoCorrente, 'cd_filial' => $filialCorrente, 'ano' => $anoAnterior);
                                    }
                                }
                                $valorTotPGTOPago = $value['valor_pago_principal'];
                                $valorTotPGTOQtde = $value['quantidade'];
                                $valorTotPGTOVencto = $value['valor_total'];
                                $valorTotPGTOVlrAberto = $value['valor_devedor'];
                                $pgtoCorrente = $value['cd_tipo_pgto'];
                                $dsPgtoCorrente = $value['ds_tipo_pgto'];
                            }
                        }//
                        /// ---->> MES
                        if ($tipo_relatorio == "mes") {

                            if ($mesAnterior == $value['mes']) {

                                $valorTotMESPago += $value['valor_pago_principal'];
                                $valorTotMESQtde += $value['quantidade'];
                                $valorTotMESVencto += $value['valor_total'];
                                $valorTotMESVlrAberto += $value['valor_devedor'];
                                $mesCorrente = $value['mes'];
                                $dsMes = "";
                            } else {

                                if ($valorTotMESPago != "") {

                                    //vefirifica se existe agrupamento com filial
                                    if (isset($agrupaPorFilial) == false) {
                                        $dadosRelatorioMes[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior);
                                    } else {
                                        $dadosRelatorioMes[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior, 'cd_filial' => $filialCorrente);
                                    }
                                }

                                $valorTotMESPago = $value['valor_pago_principal'];
                                $valorTotMESQtde = $value['quantidade'];
                                $valorTotMESVencto = $value['valor_total'];
                                $valorTotMESVlrAberto = $value['valor_devedor'];
                                $mesCorrente = $value['mes'];
                                $dsMes = "";
                            }
                        } ///---->> MES
                        /// ---> DIA
                        if ($tipo_relatorio = "dia") {

                            if ($mesAnterior == $value['mes'] && $diaAnterior = !$value['dia']) {

                                $valorTotMESPago += $value['valor_pago_principal'];
                                $valorTotMESQtde += $value['quantidade'];
                                $valorTotMESVencto += $value['valor_total'];
                                $valorTotMESVlrAberto += $value['valor_devedor'];
                                $dsMes = "";
                            } else {

                                if ($valorTotMESPago != "") {

                                    //vefirifica se existe agrupamento com filial
                                    if (isset($agrupaPorFilial) == false) {
                                        $dadosRelatorioDia[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'dia' => $diaCorrente, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior);
                                    } else {
                                        $dadosRelatorioDia[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'dia' => $diaCorrente, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior, 'cd_filial' => $filialCorrente);
                                    }
                                }

                                $valorTotMESPago = $value['valor_pago_principal'];
                                $valorTotMESQtde = $value['quantidade'];
                                $valorTotMESVencto = $value['valor_total'];
                                $valorTotMESVlrAberto = $value['valor_devedor'];
                                $diaCorrente = $value['dia'];
                                $mesCorrente = $value['mes'];
                                $dsMes = "";
                            }
                        }
                    } else {//quando o ano for diferente
                        if ($valorTotAnoPago != "") {
                            $dadosRelatorioAno[] = array('valorTotAnoPago' => $valorTotAnoPago, 'valorTotAnoQtde' => $valorTotAnoQtde, 'valorTotAnoVencto' => $valorTotAnoVencto, 'valorTotAnoVlrAberto' => $valorTotAnoVlrAberto, 'ano' => $anoCorrente);
                        }
                        $valorTotAnoPago = $value['valor_pago_principal'];
                        $valorTotAnoQtde = $value['quantidade'];
                        $valorTotAnoVencto = $value['valor_total'];
                        $valorTotAnoVlrAberto = $value['valor_devedor'];
                        $anoCorrente = $value['ano'];


                        ///Por Filial
                        if (isset($agrupaPorFilial) == true) {
                            if ($filialAnterior == $value['cd_filial'] && $anoAnterior == $value['ano']) {
                                $valorTotFilialPago += $value['valor_pago_principal'];
                                $valorTotFilialQtde += $value['quantidade'];
                                $valorTotFilialVencto += $value['valor_total'];
                                $valorTotFilialVlrAberto += $value['valor_devedor'];
                                $filialCorrente = $value['cd_filial'];
                            } else {
                                if ($valorTotFilialPago != "") {
                                    $dadosRelatorioFilial[] = array('valorTotFilialPago' => $valorTotFilialPago, 'valorTotFilialQtde' => $valorTotFilialQtde, 'valorTotFilialVencto' => $valorTotFilialVencto, 'valorTotFilialVlrAberto' => $valorTotFilialVlrAberto, 'cd_filial' => $filialCorrente, 'ano' => $anoAnterior);
                                }
                                $valorTotFilialPago = $value['valor_pago_principal'];
                                $valorTotFilialQtde = $value['quantidade'];
                                $valorTotFilialVencto = $value['valor_total'];
                                $valorTotFilialVlrAberto = $value['valor_devedor'];
                                $filialCorrente = $value['cd_filial'];
                                $filialAnterior = $value['cd_filial'];
                            }
                        }///fim por filial
                        // ----->>>>>> PGTO <<<<-----
                        if (isset($agrupaPorTpPgto) == true) {

                            if ($pgtoAnterior == $value['cd_tipo_pgto']) {

                                $valorTotPGTOPago += $value['valor_pago_principal'];
                                $valorTotPGTOQtde += $value['quantidade'];
                                $valorTotPGTOVencto += $value['valor_total'];
                                $valorTotPGTOVlrAberto += $value['valor_devedor'];
                                $pgtoCorrente = $value['cd_tipo_pgto'];
                                $dsPgtoCorrente = $value['ds_tipo_pgto'];
                            } else {
                                if ($valorTotPGTOPago != "") {
                                    //vefirifica se existe agrupamento com filial
                                    if (isset($agrupaPorFilial) == false) {
                                        $dadosRelatorioPgto[] = array('valorTotPGTOPago' => $valorTotPGTOPago, 'valorTotPGTOQtde' => $valorTotPGTOQtde, 'valorTotPGTOVencto' => $valorTotPGTOVencto, 'valorTotPGTOVlrAberto' => $valorTotPGTOVlrAberto, 'cd_tipo_pgto' => $pgtoCorrente, 'ds_tipo_pgto' => $dsPgtoCorrente, 'ano' => $anoAnterior);
                                    } else {
                                        $dadosRelatorioPgto[] = array('valorTotPGTOPago' => $valorTotPGTOPago, 'valorTotPGTOQtde' => $valorTotPGTOQtde, 'valorTotPGTOVencto' => $valorTotPGTOVencto, 'valorTotPGTOVlrAberto' => $valorTotPGTOVlrAberto, 'cd_tipo_pgto' => $pgtoCorrente, 'ds_tipo_pgto' => $dsPgtoCorrente, 'cd_filial' => $filialCorrente, 'ano' => $anoAnterior);
                                    }
                                }
                                $valorTotPGTOPago = $value['valor_pago_principal'];
                                $valorTotPGTOQtde = $value['quantidade'];
                                $valorTotPGTOVencto = $value['valor_total'];
                                $valorTotPGTOVlrAberto = $value['valor_devedor'];
                                $pgtoCorrente = $value['cd_tipo_pgto'];
                                $dsPgtoCorrente = $value['ds_tipo_pgto'];
                            }
                        }// ----->>>>>> PGTO <<<<-----
                        /// ---->> MES
                        if ($tipo_relatorio == "mes") {

                            if ($mesAnterior == $value['mes']) {

                                $valorTotMESPago += $value['valor_pago_principal'];
                                $valorTotMESQtde += $value['quantidade'];
                                $valorTotMESVencto += $value['valor_total'];
                                $valorTotMESVlrAberto += $value['valor_devedor'];
                                $mesCorrente = $value['mes'];
                                $dsMes = "";
                            } else {

                                if ($valorTotMESPago != "") {
                                    //vefirifica se existe agrupamento com filial

                                    if (isset($agrupaPorFilial) == false) {
                                        $dadosRelatorioMes[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior);
                                    } else {
                                        $dadosRelatorioMes[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior, 'cd_filial' => $filialCorrente);
                                    }
                                }

                                $valorTotMESPago = $value['valor_pago_principal'];
                                $valorTotMESQtde = $value['quantidade'];
                                $valorTotMESVencto = $value['valor_total'];
                                $valorTotMESVlrAberto = $value['valor_devedor'];
                                $mesCorrente = $value['mes'];
                                $dsMes = "";
                            }
                        } ///---->> MES
                        /// ---> DIA
                        if ($tipo_relatorio == "dia") {

                            if ($mesAnterior == $value['mes'] && $diaAnterior = !$value['dia']) {

                                $valorTotMESPago += $value['valor_pago_principal'];
                                $valorTotMESQtde += $value['quantidade'];
                                $valorTotMESVencto += $value['valor_total'];
                                $valorTotMESVlrAberto += $value['valor_devedor'];
                                $mesCorrente = $value['mes'];
                                $diaCorrente = $value['dia'];
                                $dsMes = "";
                            } else {

                                if ($valorTotMESPago != "") {

                                    //vefirifica se existe agrupamento com filial
                                    if (isset($agrupaPorFilial) == false) {
                                        $dadosRelatorioDia[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'dia' => $diaAnterior, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior);
                                    } else {
                                        $dadosRelatorioDia[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'dia' => $diaAnterior, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior, 'cd_filial' => $filialCorrente);
                                    }
                                }

                                $valorTotMESPago = $value['valor_pago_principal'];
                                $valorTotMESQtde = $value['quantidade'];
                                $valorTotMESVencto = $value['valor_total'];
                                $valorTotMESVlrAberto = $value['valor_devedor'];
                                $diaCorrente = $value['dia'];
                                $mesCorrente = $value['mes'];
                                $dsMes = "";
                            }
                        }//---> DIA
                    }//fim filtro por ano


                    $anoAnterior = $value['ano']; //importante
                    $mesAnterior = $value['mes'];
                    $diaAnterior = $value['dia'];
                }

                ///Finaliza os totalizadores

                $dadosRelatorioFilial[] = array('valorTotFilialPago' => $valorTotFilialPago, 'valorTotFilialQtde' => $valorTotFilialQtde, 'valorTotFilialVencto' => $valorTotFilialVencto, 'valorTotFilialVlrAberto' => $valorTotFilialVlrAberto, 'cd_filial' => $filialCorrente, 'ano' => $anoAnterior);

                $dadosRelatorioAno[] = array('valorTotAnoPago' => $valorTotAnoPago, 'valorTotAnoQtde' => $valorTotAnoQtde, 'valorTotAnoVencto' => $valorTotAnoVencto, 'valorTotAnoVlrAberto' => $valorTotAnoVlrAberto, 'ano' => $anoAnterior);

                //verifica os agrupametos juntos com a filial
                if (isset($agrupaPorTpPgto) == true && isset($agrupaPorFilial) == true) {
                    $dadosRelatorioPgto[] = array('valorTotPGTOPago' => $valorTotPGTOPago, 'valorTotPGTOQtde' => $valorTotPGTOQtde, 'valorTotPGTOVencto' => $valorTotPGTOVencto, 'valorTotPGTOVlrAberto' => $valorTotPGTOVlrAberto, 'cd_tipo_pgto' => $pgtoCorrente, 'ds_tipo_pgto' => $dsPgtoCorrente, 'cd_filial' => $filialCorrente, 'ano' => $anoAnterior);
                    //agrupamento de pagamento sem filial
                } elseif (isset($agrupaPorTpPgto) == true && isset($agrupaPorFilial) == false) {
                    $dadosRelatorioPgto[] = array('valorTotPGTOPago' => $valorTotPGTOPago, 'valorTotPGTOQtde' => $valorTotPGTOQtde, 'valorTotPGTOVencto' => $valorTotPGTOVencto, 'valorTotPGTOVlrAberto' => $valorTotPGTOVlrAberto, 'cd_tipo_pgto' => $pgtoCorrente, 'ds_tipo_pgto' => $dsPgtoCorrente, 'ano' => $anoAnterior);
                }

                if ($tipo_relatorio == "mes") {
                    if (isset($agrupaPorFilial) == false) {
                        $dadosRelatorioMes[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior);
                    } else {
                        $dadosRelatorioMes[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior, 'cd_filial' => $filialCorrente);
                    }
                }

                if ($tipo_relatorio = "dia") {
                    if (isset($agrupaPorFilial) == false) {
                        $dadosRelatorioDia[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'dia' => $diaCorrente, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior);
                    } else {
                        $dadosRelatorioDia[] = array('valorTotMESPago' => $valorTotMESPago, 'valorTotMESQtde' => $valorTotMESQtde, 'valorTotMESVencto' => $valorTotMESVencto, 'valorTotMESVlrAberto' => $valorTotMESVlrAberto, 'dia' => $diaCorrente, 'mes' => $mesCorrente, 'ds_mes' => $dsMes, 'ano' => $anoAnterior, 'cd_filial' => $filialCorrente);
                    }
                }
            }

            var_dump($dadosRelatorioDia);
            exit();



            //$this->set(compact('dadosRelatorio','$dadosRelatorioFilial','data_formatada_inicial','data_formatada_final'));
            $this->set(compact('dadosRelatorio', 'data_formatada_inicial', 'data_formatada_final'));

            $this->render("relatorio_prev_financeira_receber_ano");
        }
    }

    public function pedido_compras() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            define('__ROOT__', dirname(dirname(__FILE__)));
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/PHPJasperXML.inc.php');
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/tcpdf/tcpdf.php');

            //monta a string com as filiais selecionadas
            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }

                $param_cd_filial = substr($cod_filiais, 1);
            }

            $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

            if (empty($this->request->data['Relatorios']['per_ini_vendas'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }

            if (empty($this->request->data['Relatorios']['per_fim_vendas'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']) > $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas'])) {
            //if ($this->request->data['Relatorios']['per_ini_vendas'] > $this->request->data['Relatorios']['per_fim_vendas']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //Pedidos pendentes. sim, nao e ambos
            if ($this->request->data['opt_ped_pendente'] === "sim") {
                $optPedPendente = "SIM";
            } elseif ($this->request->data['opt_ped_pendente'] === "nao") {
                $optPedPendente = "NAO";
            } else {
                $optPedPendente = "AMBOS";
            }

            //tipo de perido
            if ($this->request->data['opt_tipo_periodo'] == "faturamento") {
                $optTipoPeriodo = "FATURAMENTO";
            } elseif ($this->request->data['opt_tipo_periodo'] == "entrada") {
                $optTipoPeriodo = "ENTRADA";
            }

            $arrayFiltroAdicional = null;

            //filtro adicional de pesquisa
            if (trim($this->request->data['txt_desc_produto']) != "") {

                if ($this->request->data['opt_filtro_adicional'] === "produto") {
                    $arrayFiltroAdicional = array('tipo_filtro' => "produto", 'valor_campo_pesquisa' => trim(strtoupper($this->request->data['txt_desc_produto'])));
                } else {
                    $arrayFiltroAdicional = array('tipo_filtro' => "linha", 'valor_campo_pesquisa' => trim(strtoupper($this->request->data['txt_desc_produto'])));
                }
            }

            //dessa forma, montamos o array com um "alias". se nao for assim, teremos que usar posiçoes [0], [1], [2], etc
            $parametros = array('cd_filial' => $param_cd_filial, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final, 'opt_pedidos_pendentes' => $optPedPendente, 'opt_tp_periodo' => $optTipoPeriodo, 'param_filtro_adicional' => $arrayFiltroAdicional);

            $dadosRelatorioFilial = $this->Relatorio->relatorio_pedido_compras($parametros);

            ///TOTALIZADORES
            $strFilialAnterior = "";

            $QtdeTotalProduto = 0;
            $QtdeTotalItens = 0;
            $TotalValor = 0;
            $TotalSaldo = 0;
            $TotalMarkup = 0;
            $intQtdeRegistros = 0; //usado para dividir o markup. quantidade de registros por filial
            ///FIM TOTALIZADORES

            if ($dadosRelatorioFilial != FALSE) {

                foreach ($dadosRelatorioFilial as $value) {

                    if ($strFilialAnterior == "") {
                        $strFilialAnterior = $value['nm_fant'];
                    }

                    if ($strFilialAnterior != $value['nm_fant']) {

                        if ($intQtdeRegistros == 0) {
                            $TotalMarkup = 0;
                        } else {
                            $TotalMarkup = $TotalMarkup / $intQtdeRegistros;
                        }

                        $arrayQtdeTotalProduto[] = array('nm_fant' => $strFilialAnterior, 'QtdeTotalProduto' => $QtdeTotalProduto, 'QtdeTotalItens' => $QtdeTotalItens, 'TotalValor' => $TotalValor, 'TotalSaldo' => $TotalSaldo, 'TotalMarkup' => $TotalMarkup);

                        $QtdeTotalProduto = 0;
                        $QtdeTotalItens = 0;
                        $TotalValor = 0;
                        $TotalSaldo = 0;
                        $TotalMarkup = 0;
                        $intQtdeRegistros = 0;
                    }

                    $strFilialAnterior = $value['nm_fant'];
                    $QtdeTotalProduto += $value['qtde_pro_z'];
                    $QtdeTotalItens += $value['qtde_produto_itens'];
                    $TotalValor += $value['vlr_tot_produt_itens'];
                    $TotalSaldo += $value['qtde_sald_itens_restante'];
                    $TotalMarkup += $value['markup'];
                    $intQtdeRegistros++;
                }

                if ($strFilialAnterior != "") {

                    if ($intQtdeRegistros == 0) {
                        $TotalMarkup = 0;
                    } else {
                        $TotalMarkup = $TotalMarkup / $intQtdeRegistros;
                    }

                    $arrayQtdeTotalProduto[] = array('nm_fant' => $strFilialAnterior, 'QtdeTotalProduto' => $QtdeTotalProduto, 'QtdeTotalItens' => $QtdeTotalItens, 'TotalValor' => $TotalValor, 'TotalSaldo' => $TotalSaldo, 'TotalMarkup' => $TotalMarkup);
                }
            }

            //var_dump($arrayQtdeTotalProduto);
            //exit;

            $this->set(compact('dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'arrayQtdeTotalProduto', 'optTipoPeriodo'));

            //nome do arquivo referente ao relatorio
            $this->render('relatorio_pedido_compras');
        }
    }

    public function controle_vendas() {
        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }

        //21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            define('__ROOT__', dirname(dirname(__FILE__)));
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/PHPJasperXML.inc.php');
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/tcpdf/tcpdf.php');

            //monta a string com as filiais selecionadas
            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }

                $param_cd_filial = substr($cod_filiais, 1);
            }

            $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

            if (empty($this->request->data['Relatorios']['per_ini_vendas'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }

            if (empty($this->request->data['Relatorios']['per_fim_vendas'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }
            
            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']) > $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas'])) {
            //if ($this->request->data['Relatorios']['per_ini_vendas'] > $this->request->data['Relatorios']['per_fim_vendas']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            $arrayFiltroAdicional = null;
            $arrayFiltroVendedor = null;

            //filtro adicional de pesquisa
            if (trim($this->request->data['txt_desc_produto']) != "") {

                if ($this->request->data['opt_filtro_adicional'] === "produto") {
                    $arrayFiltroAdicional = array('tipo_filtro' => "produto", 'valor_campo_pesquisa' => trim(strtoupper($this->request->data['txt_desc_produto'])));
                } else {
                    $arrayFiltroAdicional = array('tipo_filtro' => "linha", 'valor_campo_pesquisa' => trim(strtoupper($this->request->data['txt_desc_produto'])));
                }
            }

            //filtro de vendedor
            if (trim($this->request->data['txt_desc_vendedor']) != "") {
                $arrayFiltroVendedor = array('valor_campo_pesquisa' => trim(strtoupper($this->request->data['txt_desc_vendedor'])));
            }

            //dessa forma, montamos o array com um "alias". se nao for assim, teremos que usar posiçoes [0], [1], [2], etc
            $parametros = array('cd_filial' => $param_cd_filial, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final, 'param_filtro_adicional' => $arrayFiltroAdicional, 'param_filtro_vendedor' => $arrayFiltroVendedor);

            $dadosRelatorioFilial = $this->Relatorio->relatorio_controle_vendas($parametros);

            ///TOTALIZADORES FILIAL
            $strFilialAnterior = "";

            $QtdeTotalEstoque = 0;
            $QtdeTotalVendida = 0;
            $TotalValorVitrine = 0;
            $TotalValorVenda = 0;
            $TotalValorVendido = 0;
            ///FIM TOTALIZADORES FILIAL

            if ($dadosRelatorioFilial != FALSE) {

                foreach ($dadosRelatorioFilial as $value) {

                    if ($strFilialAnterior == "") {
                        $strFilialAnterior = $value['nm_fant'];
                    }

                    if ($strFilialAnterior != $value['nm_fant']) {

                        $arrayQtdeTotalProduto[] = array('nm_fant' => $strFilialAnterior, 'QtdeTotalEstoque' => $QtdeTotalEstoque, 'QtdeTotalVendida' => $QtdeTotalVendida, 'TotalValorVitrine' => $TotalValorVitrine, 'TotalValorVenda' => $TotalValorVenda, 'TotalValorVendido' => $TotalValorVendido);

                        $QtdeTotalEstoque = 0;
                        $QtdeTotalVendida = 0;
                        $TotalValorVitrine = 0;
                        $TotalValorVenda = 0;
                        $TotalValorVendido = 0;
                    }

                    $strFilialAnterior = $value['nm_fant'];
                    $QtdeTotalEstoque += $value['qtde_estoque'];
                    $QtdeTotalVendida += $value['qtde_vendida'];
                    $TotalValorVitrine += $value['vlr_prazo'];
                    $TotalValorVenda += $value['vlr_venda'];
                    $TotalValorVendido += $value['vlr_vendido'];
                }

                if ($strFilialAnterior != "") {

                    $arrayQtdeTotalProduto[] = array('nm_fant' => $strFilialAnterior, 'QtdeTotalEstoque' => $QtdeTotalEstoque, 'QtdeTotalVendida' => $QtdeTotalVendida, 'TotalValorVitrine' => $TotalValorVitrine, 'TotalValorVenda' => $TotalValorVenda, 'TotalValorVendido' => $TotalValorVendido);
                }
            }

            ///TOTALIZADORES VENDEDOR
            $strVendedorAnterior = "";

            $QtdeTotalEstoqueVendedor = 0;
            $QtdeTotalVendidaVendedor = 0;
            $TotalValorVitrineVendedor = 0;
            $TotalValorVendaVendedor = 0;
            $TotalValorVendidoVendedor = 0;
            ///FIM TOTALIZADORES VENDEDOR

            if ($dadosRelatorioFilial != FALSE) {

                foreach ($dadosRelatorioFilial as $value) {

                    if ($strVendedorAnterior == "") {
                        $strVendedorAnterior = $value['nm_usu'];
                    }

                    if ($strVendedorAnterior != $value['nm_usu']) {

                        $arrayQtdeTotalProdutoVendedor[] = array('nm_usu' => $strVendedorAnterior, 'QtdeTotalEstoqueVendedor' => $QtdeTotalEstoqueVendedor,
                            'QtdeTotalVendidaVendedor' => $QtdeTotalVendidaVendedor,
                            'TotalValorVitrineVendedor' => $TotalValorVitrineVendedor,
                            'TotalValorVendaVendedor' => $TotalValorVendaVendedor, 'TotalValorVendidoVendedor' => $TotalValorVendidoVendedor);

                        $QtdeTotalEstoqueVendedor = 0;
                        $QtdeTotalVendidaVendedor = 0;
                        $TotalValorVitrineVendedor = 0;
                        $TotalValorVendaVendedor = 0;
                        $TotalValorVendidoVendedor = 0;
                    }

                    $strVendedorAnterior = $value['nm_usu'];
                    $QtdeTotalEstoqueVendedor += $value['qtde_estoque'];
                    $QtdeTotalVendidaVendedor += $value['qtde_vendida'];
                    $TotalValorVitrineVendedor += $value['vlr_prazo'];
                    $TotalValorVendaVendedor += $value['vlr_venda'];
                    $TotalValorVendidoVendedor += $value['vlr_vendido'];
                }

                if ($strVendedorAnterior != "") {

                    $arrayQtdeTotalProdutoVendedor[] = array('nm_usu' => $strVendedorAnterior, 'QtdeTotalEstoqueVendedor' => $QtdeTotalEstoqueVendedor,
                        'QtdeTotalVendidaVendedor' => $QtdeTotalVendidaVendedor,
                        'TotalValorVitrineVendedor' => $TotalValorVitrineVendedor,
                        'TotalValorVendaVendedor' => $TotalValorVendaVendedor, 'TotalValorVendidoVendedor' => $TotalValorVendidoVendedor);
                }
            }
            //var_dump($arrayQtdeTotalProduto);
            //exit;

            $this->set(compact('dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'arrayQtdeTotalProduto', 'arrayFiltroVendedor', 'arrayQtdeTotalProdutoVendedor'));

            //nome do arquivo referente ao relatorio
            $this->render('relatorio_controle_vendas');
        }
    }

    public function vendas_condicao_pagamento() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        $this->loadModel("GlbTpPgto");
        $this->GlbTpPgto->setDataSource($_SESSION['Config']['database']);
        $pagamentos = $this->GlbTpPgto->find('all', array('fields' => array('cd_emp', 'cd_tipo_pgto', 'ds_tipo_pgto'), 'conditions' => array('status' => '0'), 'order' => array('ds_tipo_pgto')));

        $this->set(compact('pagamentos'));

        if ($this->request->is("POST")) {

            define('__ROOT__', dirname(dirname(__FILE__)));
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/PHPJasperXML.inc.php');
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/tcpdf/tcpdf.php');

            //monta a string com as filiais selecionadas
            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }

                $param_cd_filial = substr($cod_filiais, 1);
            }

            //monta a string com os tipos de pagamento selecionados
            if (isset($this->request->data['Relatorios']['pagamentos'])) {
                $cod_pagamentos = '';
                foreach ($this->request->data['Relatorios']['pagamentos'] as $value) {
                    $cod_pagamentos .= "," . $value;
                }

                $param_cd_tp_pgto = substr($cod_pagamentos, 1);
            }

            $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

            if (empty($this->request->data['Relatorios']['per_ini_vendas'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }

            if (empty($this->request->data['Relatorios']['per_fim_vendas'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']) > $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas'])) {
            //if ($this->request->data['Relatorios']['per_ini_vendas'] > $this->request->data['Relatorios']['per_fim_vendas']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_vendas']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_fim_vendas']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_vendas']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['Relatorios']['per_ini_vendas']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['vlr_tac'])) {
                $booUsarVlrTac = true;
            } else {
                $booUsarVlrTac = false;
            }

            //Pedidos pendentes. sim, nao e ambos
            if ($this->request->data['opt_agrupar'] === "condicao_pagamento") {
                $optAgrupar = "condicao_pagamento";
            } elseif ($this->request->data['opt_agrupar'] === "tipo_pagamento") {
                $optAgrupar = "tipo_pagamento";
            }

            //dessa forma, montamos o array com um "alias". se nao for assim, teremos que usar posiçoes [0], [1], [2], etc
            $parametros = array('cd_filial' => $param_cd_filial, 'cd_tp_pgto' => $param_cd_tp_pgto, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final, 'booUsarVlrTac' => $booUsarVlrTac, 'optAgrupar' => $optAgrupar);

            $dadosRelatorioFilial = $this->Relatorio->relatorio_vendas_condicao_pagamento($parametros);

            ///TOTALIZADORES
            $strFilialAnterior = "";

            $TotalValorVenda = 0;
            $TotalValorEntrada = 0;
            $TotalQuantidade = 0;
            $TotalPrazoMedio = 0;

            $TotalQuantidadeGeral = 0; //a soma da quantidade de todas as filiais
            ///FIM TOTALIZADORES

            if ($dadosRelatorioFilial != FALSE) {

                foreach ($dadosRelatorioFilial as $value) {

                    if ($strFilialAnterior == "") {
                        $strFilialAnterior = $value['nm_fant'];
                    }

                    if ($strFilialAnterior != $value['nm_fant']) {

                        $TotalQuantidadeGeral += $TotalQuantidade;

                        $arrayQtdeTotalProduto[] = array('nm_fant' => $strFilialAnterior, 'TotalValorVenda' => $TotalValorVenda, 'TotalValorEntrada' => $TotalValorEntrada, 'TotalQuantidade' => $TotalQuantidade, 'TotalPrazoMedio' => $TotalPrazoMedio);

                        $TotalValorVenda = 0;
                        $TotalValorEntrada = 0;
                        $TotalQuantidade = 0;
                        $TotalPrazoMedio = 0;
                    }

                    $strFilialAnterior = $value['nm_fant'];
                    $TotalValorVenda += $value['vlr_vd'];
                    $TotalValorEntrada += $value['vlr_ent'];
                    $TotalQuantidade += $value['qtde_vendas'];
                    $TotalPrazoMedio += $value['prazo_medio'];
                }

                if ($strFilialAnterior != "") {

                    $TotalQuantidadeGeral += $TotalQuantidade;

                    $arrayQtdeTotalProduto[] = array('nm_fant' => $strFilialAnterior, 'TotalValorVenda' => $TotalValorVenda, 'TotalValorEntrada' => $TotalValorEntrada, 'TotalQuantidade' => $TotalQuantidade, 'TotalPrazoMedio' => $TotalPrazoMedio);
                }
            }

            $strFilialAnterior = "";
            $dblPrazoPonderadoIndividual = 0; //prazo medio individual
            $dblPrazoPonderadoFilial = 0; //prazo medio por filial
            $dblVlrPrazoMedio = 0; //prazo medio por registro
            //prazo medio
            if ($dadosRelatorioFilial != FALSE) {

                foreach ($dadosRelatorioFilial as $value) {

                    if ($strFilialAnterior == "") {
                        $strFilialAnterior = $value['nm_fant'];
                    }

                    if ($strFilialAnterior != $value['nm_fant']) {

                        $arrayPrazoMedio[] = array('nm_fant' => $strFilialAnterior, 'dblPrazoPonderadoFilial' => $dblPrazoPonderadoFilial);

                        $dblPrazoPonderadoIndividual = 0;
                        $dblPrazoPonderadoFilial = 0;
                    } //if ($strFilialAnterior != $value['nm_fant']) {

                    $strFilialAnterior = $value['nm_fant'];

                    if ($value['prazo_medio'] == 0) {
                        $dblVlrPrazoMedio = 1;
                    } else {
                        $dblVlrPrazoMedio = $value['prazo_medio'];
                    }

                    $dblPrazoPonderadoIndividual = ($value['qtde_vendas'] / $TotalQuantidadeGeral) * $dblVlrPrazoMedio;

                    $dblPrazoPonderadoFilial += $dblPrazoPonderadoIndividual;
                }

                if ($strFilialAnterior != "") {

                    $arrayPrazoMedio[] = array('nm_fant' => $strFilialAnterior, 'dblPrazoPonderadoFilial' => $dblPrazoPonderadoFilial);
                }
            }

            //var_dump($arrayQtdeTotalProduto);
            //exit;
            //parametros que passo diretamente pro relatorio
            $this->set(compact('dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'arrayQtdeTotalProduto', 'arrayPrazoMedio', 'optAgrupar'));

            //nome do arquivo referente ao relatorio
            $this->render('relatorio_vendas_condicao_pagamento');
        }
    }

    public function analise_lucros() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Analise Lucros', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->loadModel("Despesa");
        $this->Despesa->setDataSource($_SESSION['Config']['database']);
        $contas = $this->Despesa->find('all', array('fields' => array('ds_conta', 'cd_conta'), 'conditions' => array('sts_conta' => '1'), 'order' => array('ds_conta')));

        $this->loadModel("Categoria");
        $this->Categoria->setDataSource($_SESSION['Config']['database']);
        $categorias = $this->Categoria->find('all', array('fields' => array('cd_hist', 'ds_hist'), 'order' => array('ds_hist')));

        $this->set(compact('filiais', 'contas', 'categorias'));

        if ($this->request->is("POST")) {

            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            if (isset($this->request->data['Relatorios']['despesa'])) {
                $cod_despesas = '';
                foreach ($this->request->data['Relatorios']['despesa'] as $value) {
                    $cod_despesas .= "," . $value;
                }
                $param_cd_despesa = substr($cod_despesas, 1);
            }
            if (isset($this->request->data['Relatorios']['categoria'])) {
                $cod_categoria = '';
                foreach ($this->request->data['Relatorios']['categoria'] as $value) {
                    $cod_categoria .= "," . $value;
                }
                $param_cd_categoria = substr($cod_categoria, 1);
            }

            $parametros = array('cd_filial' => $param_cd_filial, 'cd_despesa' => $param_cd_despesa, 'cd_categoria' => $param_cd_categoria, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);

            $dadosRelatorio = $this->Relatorio->analise_lucros($parametros);
            $dadosRelatorioFilial = $this->Relatorio->analise_lucros_filial($parametros);

            $this->set(compact('dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final'));

            $this->render("relatorio_analise_lucro");
        }
    }

    public function orcamento_venda() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Grafico Orcamento Vendas', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

         $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            $parametros = array('cd_filial' => $param_cd_filial, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);

            //$dadosRelatorio = $this->Relatorio->orcamento_venda($parametros);
            $dadosRelatorio = $this->Relatorio->fluxo_orcamento_venda_hora($parametros);


            $cores = array('FF0F00', 'FF6600', 'FCD202', 'B0DE09', '04D215', '320000', 'FF009A', '9A00FF', '000034', '008B8B', 'CDFFFE', '6601FF', '660032', '9A0000', '01CC34', '339967', 'FFD8C9', 'A582E6', 'B1B9AE', '81FE00', '993300', 'FF9933', 'C59D08', '436EEE', '0000EE', '1874CD', '36648B', '00688B', '6CA6CD', '4A708B', '607B8B', '00868B', '008B8B', '528B8B', '008B45', '008B00', '8B8B7A', '828282', 'CFCFCF', '4F4F4F', '008B8B', '4876FF', '104E8B', '00688B', '9FB6CD', '00B2EE', '4F94CD',
                'FF8C00', 'FFA500', 'FFD700', 'FFFF00', 'FFFFE0', 'FFFACD', 'FAFAD2', 'FFEFD5', 'FFE4B5', 'FFDAB9', 'EEE8AA', 'F0E68C', '8B008B', '800080', '4B0082', '6A5ACD', '483D8B', '7B68EE', 'ADFF2F', '7FFF00', '7CFC00', '00FF00', '32CD32', '98FB98', '90EE90', '00FA9A', '00FF7F', '3CB371', '2E8B57', '228B22', '008000', '006400', '9ACD32', '6B8E23', '808000', '556B2F', '66CDAA', '8FBC8B', '20B2AA',
                'DB7093', 'FFA07A', 'FF7F50', 'FF6347', 'FF4500', 'BDB76B', 'E6E6FA', 'D8BFD8', 'DDA0DD', 'EE82EE', 'DA70D6', 'F08080', 'FA8072', 'E9967A', 'FFA07A', 'DC143C', 'FF0000', 'B22222', '8B0000', 'FFB6C1', 'FF69B4', 'FF1493', 'C71585', 'FF00FF', 'FF00FF', 'BA55D3', '9370DB', '663399', '8A2BE2', '9400D3', '9932CC', '008B8B', '008080', 'FFC0CB');


            $cor = 0;
            if ($dadosRelatorio) {
                foreach ($dadosRelatorio as $key => $value) {
                    @$dadosHoras[$value['hora']]['quantidade'] = @$dadosHoras[$value['hora']]['quantidade'] + $value['quantidade'];
                    @$dadosHoras[$value['hora']]['horas'] = $value['hora'];
                }
                $variavel = "";
                foreach ($dadosHoras as $key => $valor) {
                    $variavel .= '{' . '"hora":' . '"' . $key . 'h"' . ',"quantidade":' . $valor["quantidade"] . ',"color":"#' . $cores[$cor] . '"},';
                    $cor++;
                }
                $variavel = "[" . $variavel . "]";

                $dadosRelatorioFilial = $this->Relatorio->fluxo_vendas_hora_filial($parametros);

                $resultFilial = array();
                foreach ($dadosRelatorioFilial as $key => $dadosFilial) {
                    $resultFilial[$key]['quantidade'] = (int) $dadosFilial['quantidade'];
                    $resultFilial[$key]['filial'] = "Filial " . $dadosFilial['cd_filial'];
                    $resultFilial[$key]['color'] = "#" . $cores[$cor];
                    $cor++;
                }
                $dadosRelatorioFilial = $resultFilial;
                $dadosFilial = json_encode($dadosRelatorioFilial);
            }

            $this->set(compact('dadosFilial', 'variavel', 'data_formatada_inicial', 'data_formatada_final'));
            $this->render("relatorio_orcamento_venda");
        }
    }

    public function comparativo_vendas() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Comparativo Vendas', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //PER�ODO 2
            if (empty($this->request->data['dt_in_2'])) {
                $param_dt_inicial_2 = date('Y-m-d');
                $data_formatada_inicial_2 = date('d-m-Y');
            } else {
                $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";
            }
            if (empty($this->request->data['dt_fi_2'])) {
                $param_dt_final_2 = date('Y-m-d');
                $data_formatada_final_2 = date('d-m-Y');
            } else {
                $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in_2']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi_2'])) {
            //if ($this->request->data['dtn_i_2'] > $this->request->data['dt_fi_2']) {
                $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";

                $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
            }


            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            $parametros = array('cd_filial' => $param_cd_filial, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final, 'param_dt_inicial_2' => $param_dt_inicial_2, 'param_dt_final_2' => $param_dt_final_2);
            $dadosRelatorio = $this->Relatorio->comparativo_vendas($parametros);

            if ($dadosRelatorio) {
                $totalGeralPeriodo1 = "";
                $totalGeralPeriodo2 = "";
                foreach ($dadosRelatorio as $value) {
                    $totalGeralPeriodo1 += $value['total_venda_1'];
                    $totalGeralPeriodo2 += $value['total_venda_2'];
                }
            }

            $this->set(compact('dadosRelatorio', 'data_formatada_inicial', 'data_formatada_final', 'data_formatada_inicial_2', 'data_formatada_final_2', 'totalGeralPeriodo1', 'totalGeralPeriodo2'));
            $this->render("relatorio_comparativo_vendas");
        }
    }

    public function entradas_x_vendas() {

        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Entrada x Vendas', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            // PER�ODO Vendas
            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_negativo']) && !isset($this->request->data['qtde_estoque_positivo'])) {
                $param_estoque = "AND qtde_estoque <= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "AND qtde_estoque >= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "AND qtde_estoque = 0 ";
            }
            if (isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo']) && !isset($this->request->data['qtde_estoque_zerado'])) {
                $param_estoque = "AND qtde_estoque <> 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "AND qtde_estoque < 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "AND qtde_estoque > 0 ";
            }

            //PER�ODO Entrada
            if (!empty($this->request->data['dt_in_2'])) {
                if (empty($this->request->data['dt_in_2'])) {
                    $param_dt_inicial_2 = "'" . date('Y-m-d') . "'";
                    $data_formatada_inicial_2 = date('d-m-Y');
                } else {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";
                }
                if (empty($this->request->data['dt_fi_2'])) {
                    $param_dt_final_2 = "'" . date('Y-m-d') . "'";
                    $data_formatada_final_2 = date('d-m-Y');
                } else {
                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }

                //30252: Data de parâmetro invertida
                if ($funcionalidades->formatarDataBd($this->request->data['dt_in_2']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi_2'])) {
                //dt_fi_2if ($this->request->data['dt_in_2'] > $this->request->data['dt_fi_2']) {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";

                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final, 'param_dt_entrada_inicial_2' => $param_dt_inicial_2, 'param_dt_entrada_final_2' => $param_dt_final_2);
            } else {
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final);
            }

            $dadosRelatorio = $this->Relatorio->entrada_x_vendas($parametros);

            if ($dadosRelatorio != FALSE) {
                $filialAnterior = -1;
                $totalQtdeEntradaFilial = 0;
                $totalQtdeEstoqueFilial = 0;
                $totalVlrEstoqueFilial = 0;
                $totalQtdeEstoqueRealFilial = 0;
                $totalQtdeVendaFilial = 0;
                $totalVlrVendaFilial = 0;
                $totalPorFilialPCusto = 0.0;
                $totalPorFilialPVenda = 0.0;
                $totalPorFilialNmFant = -1; //recebe o cd_filial

                $totalPorFilialPercentEstoque = 0; //% qtde estoque filial
                $totalPorFilialPercentVenda = 0; //% qtde venda filial
                $totalPorFilialPercentVlrEstoque = 0; //% vlr estoque filial
                $totalPorFilialPercentVlrVenda = 0; //% vlr venda filial

                $countPCusto = 0;
                $countPVenda = 0;
                $countPCustoGeral = 0;
                $countPVendaGeral = 0;

                //Total Geral
                $totalQtdeEntrada = 0;
                $totalQtdeEstoque = 0;
                $totalVlrEstoque = 0;
                $totalQtdeEstoqueReal = 0;
                $totalQtdeVenda = 0;
                $totalVlrVenda = 0;
                $totalPCusto = 0;
                $totalPVenda = 0;
                $totalPercentEstoque = 0;

                //TOTALIZADORES GERAIS
                foreach ($dadosRelatorio as $key => $value) {
                    $totalQtdeEstoqueReal += $value['qtde_estoque_real'];
                    $totalQtdeVenda += $value['qtde_venda'];
                    $totalVlrEstoque += $value['vlr_estoque'];
                    $totalVlrVenda += $value['vlr_venda'];
                    $totalQtdeEntrada += $value['qtde_entrada'];
                    $totalPCusto += $value['pcusto'];
                    $totalPVenda += $value['pvenda'];
                    $totalPercentEstoque += $value['perc_qtde_estoque'];
                    $totalQtdeEstoque += $value['qtde_estoque'];

                    if ($value['pcusto'] != 0) {
                        $countPCustoGeral++;
                    }
                    if ($value['qtde_venda'] != 0) {
                        $countPVendaGeral++;
                    }
                }

                foreach ($dadosRelatorio as $key => $value) {

                    //Total Geral
                    //$totalQtdeEntrada += $value['qtde_entrada'];
                    //$totalVlrEstoque += $value['vlr_estoque'];
                    //$totalQtdeEstoqueReal += $value['qtde_estoque_real'];
                    //$totalQtdeVenda += $value['qtde_venda'];
                    //$totalVlrVenda += $value['vlr_venda'];
                    //$totalPCusto += $value['pcusto'];
                    //$totalPVenda += $value['pvenda'];
                    //$totalPercentEstoque += $value['perc_qtde_estoque'];

                    /* if ($value['pcusto'] != 0) {
                      $countPCustoGeral++;
                      }
                      if ($value['qtde_venda'] != 0) {
                      $countPVendaGeral++;
                      } */

                    if ($filialAnterior === $value['cd_filial']) {

                        $totalQtdeEntradaFilial += $value['qtde_entrada'];
                        $totalQtdeEstoqueFilial += $value['qtde_estoque'];
                        $totalVlrEstoqueFilial += $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial += $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial += $value['qtde_venda'];
                        $totalVlrVendaFilial += $value['vlr_venda'];

                        if ($value['qtde_venda'] == 0) {
                            $PcustoCorreto = number_format(floatval($value['pcusto']), 2);
                        } else {

                            $dadosCusto = $this->Relatorio->RetornaCustoCorreto($parametros, $value['cd_marca']);

                            foreach ($dadosCusto as $key_custo => $value_custo) {
                                $PcustoCorreto = $value_custo['pcusto'];
                            }
                        }

                        $value['pcusto'] = number_format(floatval($PcustoCorreto), 2);

                        if ($value['pcusto'] > 0) {
                            $totalPorFilialPCusto += $value['pcusto']; //soma o preco de custo 
                        }

                        if ($value['pcusto'] > 0) {
                            $countPCusto++;
                        }

                        $totalPorFilialPVenda += $value['pvenda'];
                        //$totalPorFilialPercentEstoque += $value['perc_qtde_estoque'];
                        $totalPorFilialNmFant = $value['cd_filial'];

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%QTDE ESTOQUE"
                        if ($totalQtdeEstoqueReal != 0) {
                            $totalPorFilialPercentEstoque += (($value['qtde_estoque_real'] / $totalQtdeEstoqueReal) * 100);
                        } else {
                            $totalPorFilialPercentEstoque += 0;
                        }

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%QTDE VENDA"
                        if ($totalQtdeVenda != 0) {
                            $totalPorFilialPercentVenda += (($value['qtde_venda'] / $totalQtdeVenda) * 100);
                        } else {
                            $totalPorFilialPercentVenda += 0;
                        }

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%VLR ESTOQUE"
                        if ($totalVlrEstoque != 0) {
                            $totalPorFilialPercentVlrEstoque += (($value['vlr_estoque'] / $totalVlrEstoque) * 100);
                        } else {
                            $totalPorFilialPercentVlrEstoque += 0;
                        }

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%VLR venda filial"
                        if ($totalVlrVenda != 0) {
                            $totalPorFilialPercentVlrVenda += (($value['vlr_venda'] / $totalVlrVenda) * 100);
                        } else {
                            $totalPorFilialPercentVlrVenda += 0;
                        }

                        if ($value['qtde_venda'] != 0) {
                            $countPVenda++;
                        }
                    } else {

                        //$totalVlrVendaFilial += $value['vlr_venda'];

                        if ($totalVlrVendaFilial != "") {

//                            if ($value['pcusto'] != 0) {
//                                $countPCusto++;
                            //}

                            /* if ($value['qtde_venda'] != 0) {
                              $countPVenda++;
                              } */

                            $dadosRelatorioFilial[] = array('total_qtde_entrada' => $totalQtdeEntradaFilial, 'total_qtde_estoque' => $totalQtdeEstoqueFilial,
                                'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                                'total_vlr_venda' => $totalVlrVendaFilial, 'totalPorFilialPercentEstoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda,
                                'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant,
                                'totalPorFilialPercentVenda' => $totalPorFilialPercentVenda, 'totalPorFilialPercentVlrEstoque' => $totalPorFilialPercentVlrEstoque,
                                'totalPorFilialPercentVlrVenda' => $totalPorFilialPercentVlrVenda);

                            $totalPorFilialPCusto = 0.0;
                            $countPCusto = 0;
                            $countPVenda = 0;
                        }

                        $totalQtdeEntradaFilial = $value['qtde_entrada'];
                        $totalQtdeEstoqueFilial = $value['qtde_estoque'];
                        $totalVlrEstoqueFilial = $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial = $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial = $value['qtde_venda'];
                        $totalVlrVendaFilial = $value['vlr_venda'];

                        if ($value['qtde_venda'] == 0) {
                            $PcustoCorreto = number_format(floatval($value['pcusto']), 2);
                        } else {

                            $dadosCusto = $this->Relatorio->RetornaCustoCorreto($parametros, $value['cd_marca']);

                            foreach ($dadosCusto as $key_custo => $value_custo) {
                                $PcustoCorreto = $value_custo['pcusto'];
                            }
                        }

                        $value['pcusto'] = number_format(floatval($PcustoCorreto), 2);

                        if ($value['pcusto'] > 0) {
                            $totalPorFilialPCusto += $value['pcusto']; //soma o preco de custo 
                        }

                        if ($value['pcusto'] > 0) {
                            $countPCusto++;
                        }

                        $totalPorFilialPVenda = $value['pvenda'];

                        if ($value['pvenda'] > 0) {
                            $countPVenda++;
                        }

                        $totalPorFilialPercentEstoque = $value['perc_qtde_estoque'];
                        $totalPorFilialNmFant = $value['cd_filial'];
                        $filialAnterior = $value['cd_filial'];

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%QTDE ESTOQUE"
                        if ($totalQtdeEstoqueReal != 0) {
                            $totalPorFilialPercentEstoque = (($value['qtde_estoque_real'] / $totalQtdeEstoqueReal) * 100);
                        } else {
                            $totalPorFilialPercentEstoque = 0;
                        }

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%QTDE VENDA"
                        if ($totalQtdeVenda != 0) {
                            $totalPorFilialPercentVenda = (($value['qtde_venda'] / $totalQtdeVenda) * 100);
                        } else {
                            $totalPorFilialPercentVenda = 0;
                        }

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%VLR ESTOQUE"
                        if ($totalVlrEstoque != 0) {
                            $totalPorFilialPercentVlrEstoque = (($value['vlr_estoque'] / $totalVlrEstoque) * 100);
                        } else {
                            $totalPorFilialPercentVlrEstoque = 0;
                        }

                        //TOTAL PORCETANGEM POR FILIAL DO CAMPO "%VLR venda filial"
                        if ($totalVlrVenda != 0) {
                            $totalPorFilialPercentVlrVenda = (($value['vlr_venda'] / $totalVlrVenda) * 100);
                        } else {
                            $totalPorFilialPercentVlrVenda = 0;
                        }
                    }
                }

                $dadosRelatorioFilial[] = array('total_qtde_entrada' => $totalQtdeEntradaFilial, 'total_qtde_estoque' => $totalQtdeEstoqueFilial,
                    'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                    'total_vlr_venda' => $totalVlrVendaFilial, 'totalPorFilialPercentEstoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda,
                    'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant,
                    'totalPorFilialPercentVenda' => $totalPorFilialPercentVenda, 'totalPorFilialPercentVlrEstoque' => $totalPorFilialPercentVlrEstoque,
                    'totalPorFilialPercentVlrVenda' => $totalPorFilialPercentVlrVenda);
            }

            $this->set(compact('totalPercentEstoque', 'totalPVenda', 'countPVendaGeral', 'totalPCusto', 'countPCustoGeral', 'totalVlrVenda', 'totalQtdeVenda', 'totalQtdeEstoqueReal', 'totalVlrEstoque', 'totalQtdeEntrada', 'dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'data_formatada_inicial_2', 'data_formatada_final_2', 'totalGeralQtdeEntrada', 'totalGeralQtdeEstoque', 'totalGeralVlrEstoque', 'totalGeralQtdeEstoqueReal', 'totalGeralQtdeVenda', 'totalGeralVlrVenda'));
            $this->render("relatorio_entrada_x_vendas");
        }
    }

    public function vendas_estoque_marca() {
        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Vendas Estoque Marca', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            // PER�ODO Vendas
            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = " ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = " AND  qtde_estoque <= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo'])) {
                $param_estoque = " AND  qtde_estoque >= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = " AND  qtde_estoque = 0 ";
            }
            if (isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = " AND  qtde_estoque <> 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = " AND  qtde_estoque < 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = " AND  qtde_estoque > 0 ";
            }

            //PER�ODO Entrada
            if (!empty($this->request->data['dt_in_2']) && !empty($this->request->data['dt_fi_2'])) {
                if (empty($this->request->data['dt_in_2'])) {
                    $param_dt_inicial_2 = date('Y-m-d');
                    $data_formatada_inicial_2 = date('d-m-Y');
                } else {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";
                }
                if (empty($this->request->data['dt_fi_2'])) {
                    $param_dt_final_2 = date('Y-m-d');
                    $data_formatada_final_2 = date('d-m-Y');
                } else {
                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }

                //30252: Data de parâmetro invertida
                if ($funcionalidades->formatarDataBd($this->request->data['dt_in_2']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi_2'])) {
                //if ($this->request->data['dt_in_2'] > $this->request->data['dt_fi_2']) {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";

                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final, 'param_dt_entrada_inicial_2' => $param_dt_inicial_2, 'param_dt_entrada_final_2' => $param_dt_final_2);
            } else {
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final);
            }

            $dadosRelatorio = $this->Relatorio->vendas_estoque_marca($parametros);

            if ($dadosRelatorio != FALSE) {

                $filialAnterior = -1;
                $totalQtdeEstoqueFilial = "";
                $totalVlrEstoqueFilial = "";
                $totalQtdeEstoqueRealFilial = "";
                $totalQtdeVendaFilial = "";
                $totalVlrVendaFilial = "";
                $totalPorFilialPCusto = "";
                $totalPorFilialPVenda = "";
                $totalPorFilialPercentEstoque = "";
                $totalPorFilialNmFant = "";

                $countPCusto = 0;
                $countPVenda = 0;
                $countPCustoGeral = 0;
                $countPVendaGeral = 0;

                //Total Geral
                $totalQtdeEstoque = "";
                $totalVlrEstoque = "";
                $totalQtdeEstoqueReal = "";
                $totalQtdeVenda = "";
                $totalVlrVenda = "";
                $totalPCusto = "";
                $totalPVenda = "";
                $totalPercentEstoque = "";
                $PercentQtdeEstoqueTotal = 0;
                //<?php echo number_format(($value['qtde_estoque_real']/$totalQtdeEstoqueReal)*100,2, ',' , '.');

                foreach ($dadosRelatorio as $key => $value) {

                    //Total Geral
                    $totalVlrEstoque += $value['vlr_estoque'];
                    $totalQtdeEstoqueReal += $value['qtde_estoque_real'];
                    $totalQtdeVenda += $value['qtde_venda'];
                    $totalVlrVenda += $value['vlr_venda'];
                    $totalPCusto += $value['pcusto'];
                    $totalPVenda += $value['pvenda'];
                    $totalPercentEstoque += $value['perc_qtde_estoque'];

                    if ($value['pcusto'] != 0) {
                        $countPCustoGeral++;
                    }
                    if ($value['qtde_venda'] != 0) {
                        $countPVendaGeral++;
                    }

                    if ($filialAnterior === $value['cd_filial']) {
                        $totalQtdeEstoqueFilial += $value['qtde_estoque'];
                        $totalVlrEstoqueFilial += $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial += $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial += $value['qtde_venda'];
                        $totalVlrVendaFilial += $value['vlr_venda'];
                        $totalPorFilialPCusto += $value['pcusto'];
                        $totalPorFilialPVenda += $value['pvenda'];
                        $totalPorFilialPercentEstoque += $value['perc_qtde_estoque'];
                        $totalPorFilialNmFant = $value['cd_filial'];

                        if ($value['pcusto'] != 0) {
                            $countPCusto++;
                        }
                        if ($value['qtde_venda'] != 0) {
                            $countPVenda++;
                        }
                    } else {

                        if ($totalVlrVendaFilial != "") {
                            if ($value['pcusto'] != 0) {
                                $countPCusto++;
                            }
                            if ($value['qtde_venda'] != 0) {
                                $countPVenda++;
                            }

                            $dadosRelatorioFilial[] = array('total_qtde_estoque' => $totalQtdeEstoqueFilial,
                                'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                                'total_vlr_venda' => $totalVlrVendaFilial, 'total_perc_estoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda, 'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant
                            );
                            $countPCusto = 0;
                            $countPVenda = 0;
                            $countPCusto++;
                            $countPVenda++;
                        }

                        $totalQtdeEstoqueFilial = $value['qtde_estoque'];
                        $totalVlrEstoqueFilial = $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial = $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial = $value['qtde_venda'];
                        $totalVlrVendaFilial = $value['vlr_venda'];
                        $totalPorFilialPCusto = $value['pcusto'];
                        $totalPorFilialPVenda = $value['pvenda'];
                        $totalPorFilialPercentEstoque = $value['perc_qtde_estoque'];
                        $filialAnterior = $value['cd_filial'];
                    }
                }

                $dadosRelatorioFilial[] = array('total_qtde_estoque' => $totalQtdeEstoqueFilial,
                    'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                    'total_vlr_venda' => $totalVlrVendaFilial, 'total_perc_estoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda,
                    'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant);
            }

            $this->set(compact('totalPercentEstoque', 'totalPVenda', 'countPVendaGeral', 'totalPCusto', 'countPCustoGeral', 'totalVlrVenda', 'totalQtdeVenda', 'totalQtdeEstoqueReal', 'totalVlrEstoque', 'dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'data_formatada_inicial_2', 'data_formatada_final_2', 'totalGeralQtdeEstoque', 'totalGeralVlrEstoque', 'totalGeralQtdeEstoqueReal', 'totalGeralQtdeVenda', 'totalGeralVlrVenda'));
            $this->render("relatorio_vendas_estoque_marca");
        }
    }

    public function vendas_estoque_grupo() {
        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Vendas Estoque Marca', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            // PER�ODO Vendas
            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_negativo']) && !isset($this->request->data['qtde_estoque_positivo'])) {
                $param_estoque = "qtde_estoque <= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque >= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque = 0 ";
            }
            if (isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo']) && !isset($this->request->data['qtde_estoque_zerado'])) {
                $param_estoque = "qtde_estoque <> 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque < 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque > 0 ";
            }
            //PER�ODO Entrada
            if (!empty($this->request->data['dt_in_2'])) {
                if (empty($this->request->data['dt_in_2'])) {
                    $param_dt_inicial_2 = "'" . date('Y-m-d') . "'";
                    $data_formatada_inicial_2 = date('d-m-Y');
                } else {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";
                }
                if (empty($this->request->data['dt_fi_2'])) {
                    $param_dt_final_2 = "'" . date('Y-m-d') . "'";
                    $data_formatada_final_2 = date('d-m-Y');
                } else {
                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }

                //30252: Data de parâmetro invertida
                if ($funcionalidades->formatarDataBd($this->request->data['dt_in_2']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi_2'])) {
                //if ($this->request->data['dt_in_2'] > $this->request->data['dt_fi_2']) {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";

                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final, 'param_dt_entrada_inicial_2' => $param_dt_inicial_2, 'param_dt_entrada_final_2' => $param_dt_final_2);
            } else {
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final);
            }

            $dadosRelatorio = $this->Relatorio->vendas_estoque_grupo($parametros);

            if ($dadosRelatorio != FALSE) {

                $filialAnterior = -1;
                $totalQtdeEstoqueFilial = "";
                $totalVlrEstoqueFilial = "";
                $totalQtdeEstoqueRealFilial = "";
                $totalQtdeVendaFilial = "";
                $totalVlrVendaFilial = "";
                $totalPorFilialPCusto = "";
                $totalPorFilialPVenda = "";
                $totalPorFilialPercentEstoque = "";
                $totalPorFilialNmFant = "";

                $countPCusto = 0;
                $countPVenda = 0;
                $countPCustoGeral = 0;
                $countPVendaGeral = 0;

                //Total Geral
                $totalQtdeEstoque = "";
                $totalVlrEstoque = "";
                $totalQtdeEstoqueReal = "";
                $totalQtdeVenda = "";
                $totalVlrVenda = "";
                $totalPCusto = "";
                $totalPVenda = "";
                $totalPercentEstoque = "";

                foreach ($dadosRelatorio as $key => $value) {

                    //Total Geral
                    $totalVlrEstoque += $value['vlr_estoque'];
                    $totalQtdeEstoqueReal += $value['qtde_estoque_real'];
                    $totalQtdeVenda += $value['qtde_venda'];
                    $totalVlrVenda += $value['vlr_venda'];
                    $totalPCusto += $value['pcusto'];
                    $totalPVenda += $value['pvenda'];
                    $totalPercentEstoque += $value['perc_qtde_estoque'];

                    if ($value['pcusto'] != 0) {
                        $countPCustoGeral++;
                    }
                    if ($value['qtde_venda'] != 0) {
                        $countPVendaGeral++;
                    }

                    if ($filialAnterior === $value['cd_filial']) {
                        $totalQtdeEstoqueFilial += $value['qtde_estoque'];
                        $totalVlrEstoqueFilial += $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial += $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial += $value['qtde_venda'];
                        $totalVlrVendaFilial += $value['vlr_venda'];
                        $totalPorFilialPCusto += $value['pcusto'];
                        $totalPorFilialPVenda += $value['pvenda'];
                        $totalPorFilialPercentEstoque += $value['perc_qtde_estoque'];
                        $totalPorFilialNmFant = $value['cd_filial'];

                        if ($value['pcusto'] != 0) {
                            $countPCusto++;
                        }

                        if ($value['qtde_venda'] != 0) {
                            $countPVenda++;
                        }
                    } else {
                        if ($totalVlrVendaFilial != "") {
                            if ($value['pcusto'] != 0) {
                                $countPCusto++;
                            }
                            if ($value['qtde_venda'] != 0) {
                                $countPVenda++;
                            }

                            $dadosRelatorioFilial[] = array('total_qtde_estoque' => $totalQtdeEstoqueFilial,
                                'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                                'total_vlr_venda' => $totalVlrVendaFilial, 'total_perc_estoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda, 'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant
                            );
                            $countPCusto = 0;
                            $countPVenda = 0;
                            $countPCusto++;
                            $countPVenda++;
                        }

                        $totalQtdeEstoqueFilial = $value['qtde_estoque'];
                        $totalVlrEstoqueFilial = $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial = $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial = $value['qtde_venda'];
                        $totalVlrVendaFilial = $value['vlr_venda'];
                        $totalPorFilialPCusto = $value['pcusto'];
                        $totalPorFilialPVenda = $value['pvenda'];
                        $totalPorFilialPercentEstoque = $value['perc_qtde_estoque'];
                        $filialAnterior = $value['cd_filial'];
                    }
                } //foreach ($dadosRelatorio as $key => $value) {

                $dadosRelatorioFilial[] = array('total_qtde_estoque' => $totalQtdeEstoqueFilial,
                    'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                    'total_vlr_venda' => $totalVlrVendaFilial, 'total_perc_estoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda, 'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant
                );
            }

            $this->set(compact('totalPercentEstoque', 'totalPVenda', 'countPVendaGeral', 'totalPCusto', 'countPCustoGeral', 'totalVlrVenda', 'totalQtdeVenda', 'totalQtdeEstoqueReal', 'totalVlrEstoque', 'dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'data_formatada_inicial_2', 'data_formatada_final_2', 'totalGeralQtdeEstoque', 'totalGeralVlrEstoque', 'totalGeralQtdeEstoqueReal', 'totalGeralQtdeVenda', 'totalGeralVlrVenda'));
            $this->render("relatorio_vendas_estoque_grupo");
        }
    }

    public function vendas_estoque_familia() {
        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }
        if (!in_array('Vendas Estoque Marca', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

        $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            // PER�ODO Vendas
            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }
            
            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_negativo']) && !isset($this->request->data['qtde_estoque_positivo'])) {
                $param_estoque = "qtde_estoque <= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque >= 0 ";
            }
            if (isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque = 0 ";
            }
            if (isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo']) && !isset($this->request->data['qtde_estoque_zerado'])) {
                $param_estoque = "qtde_estoque <> 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && !isset($this->request->data['qtde_estoque_positivo']) && isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque < 0 ";
            }
            if (!isset($this->request->data['qtde_estoque_zerado']) && isset($this->request->data['qtde_estoque_positivo']) && !isset($this->request->data['qtde_estoque_negativo'])) {
                $param_estoque = "qtde_estoque > 0 ";
            }


            //PER�ODO Entrada
            if (!empty($this->request->data['dt_in_2'])) {
                if (empty($this->request->data['dt_in_2'])) {
                    $param_dt_inicial_2 = "'" . date('Y-m-d') . "'";
                    $data_formatada_inicial_2 = date('d-m-Y');
                } else {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";
                }
                if (empty($this->request->data['dt_fi_2'])) {
                    $param_dt_final_2 = "'" . date('Y-m-d') . "'";
                    $data_formatada_final_2 = date('d-m-Y');
                } else {
                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }

                //30252: Data de parâmetro invertida
                if ($funcionalidades->formatarDataBd($this->request->data['dt_in_2']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi_2'])) {
                //if ($this->request->data['dt_in_2'] > $this->request->data['dt_fi_2']) {
                    $param_dt_inicial_2 = $funcionalidades->formatarDataBd($this->request->data['dt_fi_2']);
                    $data_formatada_inicial_2 = $funcionalidades->formatarDataAp($this->request->data['dt_fi_2']);
                    $param_dt_inicial_2 = "'" . $param_dt_inicial_2 . "'";

                    $param_dt_final_2 = $funcionalidades->formatarDataBd($this->request->data['dt_in_2']);
                    $data_formatada_final_2 = $funcionalidades->formatarDataAp($this->request->data['dt_in_2']);
                    $param_dt_final_2 = "'" . $param_dt_final_2 . "'";
                }
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final, 'param_dt_entrada_inicial_2' => $param_dt_inicial_2, 'param_dt_entrada_final_2' => $param_dt_final_2);
            } else {
                $parametros = array('param_estoque' => $param_estoque, 'cd_filial' => $param_cd_filial, 'param_dt_vendas_inicial' => $param_dt_inicial, 'param_dt_vendas_final' => $param_dt_final);
            }

            $dadosRelatorio = $this->Relatorio->vendas_estoque_familia($parametros);

            if ($dadosRelatorio != FALSE) {

                $filialAnterior = -1;
                $totalQtdeEstoqueFilial = "";
                $totalVlrEstoqueFilial = "";
                $totalQtdeEstoqueRealFilial = "";
                $totalQtdeVendaFilial = "";
                $totalVlrVendaFilial = "";
                $totalPorFilialPCusto = "";
                $totalPorFilialPVenda = "";
                $totalPorFilialPercentEstoque = "";
                $totalPorFilialNmFant = "";

                $countPCusto = 0;
                $countPVenda = 0;
                $countPCustoGeral = 0;
                $countPVendaGeral = 0;

                //Total Geral
                $totalQtdeEstoque = "";
                $totalVlrEstoque = "";
                $totalQtdeEstoqueReal = "";
                $totalQtdeVenda = "";
                $totalVlrVenda = "";
                $totalPCusto = "";
                $totalPVenda = "";
                $totalPercentEstoque = "";

                foreach ($dadosRelatorio as $key => $value) {

                    //Total Geral
                    $totalVlrEstoque += $value['vlr_estoque'];
                    $totalQtdeEstoqueReal += $value['qtde_estoque_real'];
                    $totalQtdeVenda += $value['qtde_venda'];
                    $totalVlrVenda += $value['vlr_venda'];
                    $totalPCusto += $value['pcusto'];
                    $totalPVenda += $value['pvenda'];
                    $totalPercentEstoque += $value['perc_qtde_estoque'];

                    if ($value['pcusto'] != 0) {
                        $countPCustoGeral++;
                    }
                    if ($value['qtde_venda'] != 0) {
                        $countPVendaGeral++;
                    }

                    if ($filialAnterior === $value['cd_filial']) {
                        $totalQtdeEstoqueFilial += $value['qtde_estoque'];
                        $totalVlrEstoqueFilial += $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial += $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial += $value['qtde_venda'];
                        $totalVlrVendaFilial += $value['vlr_venda'];
                        $totalPorFilialPCusto += $value['pcusto'];
                        $totalPorFilialPVenda += $value['pvenda'];
                        $totalPorFilialPercentEstoque += $value['perc_qtde_estoque'];
                        $totalPorFilialNmFant = $value['cd_filial'];

                        if ($value['pcusto'] != 0) {
                            $countPCusto++;
                        }
                        if ($value['qtde_venda'] != 0) {
                            $countPVenda++;
                        }
                    } else {

                        if ($totalVlrVendaFilial != "") {
                            if ($value['pcusto'] != 0) {
                                $countPCusto++;
                            }
                            if ($value['qtde_venda'] != 0) {
                                $countPVenda++;
                            }

                            $dadosRelatorioFilial[] = array('total_qtde_estoque' => $totalQtdeEstoqueFilial,
                                'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                                'total_vlr_venda' => $totalVlrVendaFilial, 'total_perc_estoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda, 'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant
                            );
                            $countPCusto = 0;
                            $countPVenda = 0;
                            $countPCusto++;
                            $countPVenda++;
                        }

                        $totalQtdeEstoqueFilial = $value['qtde_estoque'];
                        $totalVlrEstoqueFilial = $value['vlr_estoque'];
                        $totalQtdeEstoqueRealFilial = $value['qtde_estoque_real'];
                        $totalQtdeVendaFilial = $value['qtde_venda'];
                        $totalVlrVendaFilial = $value['vlr_venda'];
                        $totalPorFilialPCusto = $value['pcusto'];
                        $totalPorFilialPVenda = $value['pvenda'];
                        $totalPorFilialPercentEstoque = $value['perc_qtde_estoque'];
                        $filialAnterior = $value['cd_filial'];
                    }
                }

                $dadosRelatorioFilial[] = array('total_qtde_estoque' => $totalQtdeEstoqueFilial,
                    'total_vlr_estoque' => $totalVlrEstoqueFilial, 'total_qtde_estoque_real' => $totalQtdeEstoqueRealFilial, 'total_qtde_venda' => $totalQtdeVendaFilial,
                    'total_vlr_venda' => $totalVlrVendaFilial, 'total_perc_estoque' => $totalPorFilialPercentEstoque, 'total_p_venda' => $totalPorFilialPVenda, 'qtde_p_venda' => $countPVenda, 'total_p_custo' => $totalPorFilialPCusto, 'qtde_p_custo' => $countPCusto, 'total_filial' => $totalPorFilialNmFant
                );
            }

            $this->set(compact('totalPercentEstoque', 'totalPVenda', 'countPVendaGeral', 'totalPCusto', 'countPCustoGeral', 'totalVlrVenda', 'totalQtdeVenda', 'totalQtdeEstoqueReal', 'totalVlrEstoque', 'dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final', 'data_formatada_inicial_2', 'data_formatada_final_2', 'totalGeralQtdeEstoque', 'totalGeralVlrEstoque', 'totalGeralQtdeEstoqueReal', 'totalGeralQtdeVenda', 'totalGeralVlrVenda'));
            $this->render("relatorio_vendas_estoque_familia");
        }
    }

    public function prev_financeira_pagar() {
        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }

        /* if (!in_array('Vendas Estoque Marca', $this->Session->read('Questionarios.permissoes'))) {
          $this->Session->setFlash(__('Esta p&aacute;gina n&atilde;o existe!'));
          echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
          } */

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        //21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

        $filiais = $this->PrcFilial->find("all", 
        array(
            'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
            'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
            "joins" => array(
                array(
                    "table" => "segu_usu_filial",
                    "alias" => "usuario",
                    "type" => "INNER",
                    "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
            ));

        $this->loadModel("Categoria");
        $this->Categoria->setDataSource($_SESSION['Config']['database']);
        $categorias = $this->Categoria->find('all', array('fields' => array('cd_hist', 'ds_hist'), 'order' => array('ds_hist')));

        $this->set(compact("filiais", "categorias"));

        if ($this->request->is("POST")) {
            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }
                    
            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }
            if (isset($this->request->data['Relatorios']['categoria'])) {
                $cod_categoria = '';
                foreach ($this->request->data['Relatorios']['categoria'] as $value) {
                    $cod_categoria .= "," . $value;
                }
                $param_cd_categoria = substr($cod_categoria, 1);
            }

            if ($this->request->data['tp_periodo'] == 1) {
                $tp_periodo = "dup_dt_emis";
            } else {
                $tp_periodo = "dup_dt_vencto";
            }

            $parametros = array('tp_periodo' => $tp_periodo, 'cd_filial' => $param_cd_filial, 'cd_categoria' => $param_cd_categoria, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);

            $dadosRelatorio = $this->Relatorio->relatorio_prev_financeira_pagar($parametros);
            $dadosRelatorioFilial = $this->Relatorio->prev_financeira_pagar_filial($parametros);

            if ($dadosRelatorio != FALSE) {

                $dup_vlr_pdata = "";
                $dup_vlr_desc_pdata = "";
                $dup_vlr_desc_provi_pdata = "";
                $dup_vlr_juros_pdata = "";
                $dup_vlr_multa_pdata = "";
                $dup_vlr_custas_pdata = "";
                $dup_vlr_pagto_pdata = "";
                $dup_vlr_saldo_pdata = "";

                $filialAnterior = -1;
                $dataVenctoAnterior = "";

                foreach ($dadosRelatorio as $key => $value) {

                    if ($filialAnterior === -1) {
                        $filialAnterior = $value['cd_filial'];
                        $dataVenctoAnterior = $value['dup_dt_vencto'];
                    }

                    if ($filialAnterior === $value['cd_filial']) {

                        if ($dataVenctoAnterior === $value['dup_dt_vencto']) {

                            //agrupado por filial e data, vamos somar e no ultimo registro deve ficar apenas uma linha (filial, data vencimento, totalizadores)
                            $dup_vlr_pdata += $value['dup_vlr'];
                            $dup_vlr_desc_pdata += $value['dup_vlr_desc'];
                            $dup_vlr_desc_provi_pdata += $value['dup_vlr_desc_provi'];
                            $dup_vlr_juros_pdata += $value['dup_vlr_juros'];
                            $dup_vlr_multa_pdata += $value['dup_vlr_multa'];
                            $dup_vlr_custas_pdata += $value['dup_vlr_custas'];
                            $dup_vlr_pagto_pdata += $value['dup_vlr_pagto'];
                            $dup_vlr_saldo_pdata += $value['dup_vlr_saldo'];
                        } else {

                            $dadosRelatorioTotalData[] = array('codigo_filial' => $filialAnterior,
                                'data_vencimento' => $dataVenctoAnterior,
                                'dup_vlr_pdata' => $dup_vlr_pdata,
                                'dup_vlr_desc_pdata' => $dup_vlr_desc_pdata,
                                'dup_vlr_desc_provi_pdata' => $dup_vlr_desc_provi_pdata,
                                'dup_vlr_juros_pdata' => $dup_vlr_juros_pdata,
                                'dup_vlr_multa_pdata' => $dup_vlr_multa_pdata,
                                'dup_vlr_custas_pdata' => $dup_vlr_custas_pdata,
                                'dup_vlr_pagto_pdata' => $dup_vlr_pagto_pdata,
                                'dup_vlr_saldo_pdata' => $dup_vlr_saldo_pdata
                            );

                            $dup_vlr_pdata = "";
                            $dup_vlr_desc_pdata = "";
                            $dup_vlr_desc_provi_pdata = "";
                            $dup_vlr_juros_pdata = "";
                            $dup_vlr_multa_pdata = "";
                            $dup_vlr_custas_pdata = "";
                            $dup_vlr_pagto_pdata = "";
                            $dup_vlr_saldo_pdata = "";

                            $dup_vlr_pdata += $value['dup_vlr'];
                            $dup_vlr_desc_pdata += $value['dup_vlr_desc'];
                            $dup_vlr_desc_provi_pdata += $value['dup_vlr_desc_provi'];
                            $dup_vlr_juros_pdata += $value['dup_vlr_juros'];
                            $dup_vlr_multa_pdata += $value['dup_vlr_multa'];
                            $dup_vlr_custas_pdata += $value['dup_vlr_custas'];
                            $dup_vlr_pagto_pdata += $value['dup_vlr_pagto'];
                            $dup_vlr_saldo_pdata += $value['dup_vlr_saldo'];

                            $filialAnterior = $value['cd_filial'];
                            $dataVenctoAnterior = $value['dup_dt_vencto'];
                        }
                    } else {

                        $dadosRelatorioTotalData[] = array('codigo_filial' => $filialAnterior,
                                'data_vencimento' => $dataVenctoAnterior,
                                'dup_vlr_pdata' => $dup_vlr_pdata,
                                'dup_vlr_desc_pdata' => $dup_vlr_desc_pdata,
                                'dup_vlr_desc_provi_pdata' => $dup_vlr_desc_provi_pdata,
                                'dup_vlr_juros_pdata' => $dup_vlr_juros_pdata,
                                'dup_vlr_multa_pdata' => $dup_vlr_multa_pdata,
                                'dup_vlr_custas_pdata' => $dup_vlr_custas_pdata,
                                'dup_vlr_pagto_pdata' => $dup_vlr_pagto_pdata,
                                'dup_vlr_saldo_pdata' => $dup_vlr_saldo_pdata
                            );

                            $dup_vlr_pdata = "";
                            $dup_vlr_desc_pdata = "";
                            $dup_vlr_desc_provi_pdata = "";
                            $dup_vlr_juros_pdata = "";
                            $dup_vlr_multa_pdata = "";
                            $dup_vlr_custas_pdata = "";
                            $dup_vlr_pagto_pdata = "";
                            $dup_vlr_saldo_pdata = "";

                            $dup_vlr_pdata += $value['dup_vlr'];
                            $dup_vlr_desc_pdata += $value['dup_vlr_desc'];
                            $dup_vlr_desc_provi_pdata += $value['dup_vlr_desc_provi'];
                            $dup_vlr_juros_pdata += $value['dup_vlr_juros'];
                            $dup_vlr_multa_pdata += $value['dup_vlr_multa'];
                            $dup_vlr_custas_pdata += $value['dup_vlr_custas'];
                            $dup_vlr_pagto_pdata += $value['dup_vlr_pagto'];
                            $dup_vlr_saldo_pdata += $value['dup_vlr_saldo'];

                            $filialAnterior = $value['cd_filial'];
                            $dataVenctoAnterior = $value['dup_dt_vencto'];
                            
                    }
                    
                } //foreach ($dadosRelatorio as $key => $value) {

                $dadosRelatorioTotalData[] = array('codigo_filial' => $filialAnterior,
                    'data_vencimento' => $dataVenctoAnterior,
                    'dup_vlr_pdata' => $dup_vlr_pdata,
                    'dup_vlr_desc_pdata' => $dup_vlr_desc_pdata,
                    'dup_vlr_desc_provi_pdata' => $dup_vlr_desc_provi_pdata,
                    'dup_vlr_juros_pdata' => $dup_vlr_juros_pdata,
                    'dup_vlr_multa_pdata' => $dup_vlr_multa_pdata,
                    'dup_vlr_custas_pdata' => $dup_vlr_custas_pdata,
                    'dup_vlr_pagto_pdata' => $dup_vlr_pagto_pdata,
                    'dup_vlr_saldo_pdata' => $dup_vlr_saldo_pdata
                );
            }

            $this->set(compact('dadosRelatorio', 'dadosRelatorioFilial', 'data_formatada_inicial', 'data_formatada_final','dadosRelatorioTotalData'));

            $this->render("relatorio_prev_financeira_pagar");
        }
    }

    public function fluxo_caixa() {
        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        if ($this->request->is("POST")) {

            //Trata cd_filial para consulta
            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            }

            //Trata cd_cx para consulta
            if (isset($this->request->data['Relatorios']['caixas'])) {
                $cd_caixas = '';
                foreach ($this->request->data['Relatorios']['caixas'] as $value) {
                    $cd_caixas .= "," . $value;
                }
                $param_cd_caixas = substr($cd_caixas, 1);
            }

            //Trata data inicial para consulta
            if (empty($this->request->data['dt_in'])) {
                $param_dt_inicial = date('Y-m-d');
                $data_formatada_inicial = date('d-m-Y');
            } else {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";
            }

            //Trata data final para consulta
            if (empty($this->request->data['dt_fi'])) {
                $param_dt_final = date('Y-m-d');
                $data_formatada_final = date('d-m-Y');
            } else {
                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            //Verifica se data inicial � maior que data final
            //30252: Data de parâmetro invertida
            if ($funcionalidades->formatarDataBd($this->request->data['dt_in']) > $funcionalidades->formatarDataBd($this->request->data['dt_fi'])) {
            //if ($this->request->data['dt_in'] > $this->request->data['dt_fi']) {
                $param_dt_inicial = $funcionalidades->formatarDataBd($this->request->data['dt_fi']);
                $data_formatada_inicial = $funcionalidades->formatarDataAp($this->request->data['dt_fi']);
                $param_dt_inicial = "'" . $param_dt_inicial . "'";

                $param_dt_final = $funcionalidades->formatarDataBd($this->request->data['dt_in']);
                $data_formatada_final = $funcionalidades->formatarDataAp($this->request->data['dt_in']);
                $param_dt_final = "'" . $param_dt_final . "'";
            }

            $parametros = array('cd_filial' => $param_cd_filial, 'cd_cx' => $param_cd_caixas, 'param_dt_inicial' => $param_dt_inicial, 'param_dt_final' => $param_dt_final);
            $dadosRelatorio = $this->Relatorio->fluxo_caixa($parametros);

            if ($dadosRelatorio != FALSE) {
                $totalEntradasCaixa = "";
                $totalDocsCaixa = "";
                $totalRetiradaCaixa = "";
                $totaisTesteArray = array();
                $dataAnterior = -1;
                foreach ($dadosRelatorio as $value) {
                    if ($dataAnterior == $value['dt_mov']) {

                        //Total de Entrada Caixa
                        if ($value['mov_caixa'] == 1 && $value['tp_transacao'] == 0) {
                            $totalEntradasCaixa += $totalEntradasCaixa + $value['vlr_mov'];

                            //Documentos Pag. a Vista
                            if ($value['mov_caixa'] == 1 && $value['totaliza_venda'] == 1) {
                                $totalDocsCaixa += $totalDocsCaixa + $value['vlr_mov'];
                            }
                            //Fim Documentos Pag. a Vista
                        }
                        //Fim Total de Entrada Caixa
                        //Retirada de Caixa
                        if ($value['mov_caixa'] == 1 && $value['tp_transacao'] == 1) {
                            $totalRetiradaCaixa += $totalRetiradaCaixa + $value['vlr_mov'] * -1;
                        }
                        //Fim Retirada de Caixa
                        //Documentos em Geral
                        if ($value['mov_caixa'] == 0 && $value['totaliza_venda'] == 1) {
                            $totalDocsCaixa += $totalDocsCaixa + $value['vlr_mov'];
                        }
                        //Fim Documentos em Geral

                        $dataAnterior = $value['dt_mov'];
                    } else {
                        if ($totalRetiradaCaixa != "") {
                            $totaisTesteArray[] = array('totalRetiradaCaixa' => $totalRetiradaCaixa, 'totalEntradasCaixa' => $totalEntradasCaixa, 'totalDocsCaixa' => $totalDocsCaixa, 'dt_mov' => $value['dt_mov']);
                        }
                        $totalEntradasCaixa = "";
                        $totalDocsCaixa = "";
                        $totalRetiradaCaixa = "";
                        $dataAnterior = $value['dt_mov'];
                    }
                }
            }

            $this->set(compact('dadosRelatorio', 'data_formatada_inicial', 'data_formatada_final'));
            $this->render("relatorio_fluxo_caixa");
        }
    }

    public function carrega_checkbox_caixa() {
        if ($this->request->is('POST')) {
            if (isset($this->request->data['cd_filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['cd_filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
                $resultadoConsulta = $this->Relatorio->retornaCaixa($param_cd_filial);
                $this->set(array('listaCaixas' => $resultadoConsulta));
                $this->render('carrega_checkbox_caixa');
            }
        }
    }

    public function envia_informativo_diario() {
        $this->layout = FALSE;
        $this->autoRender = FALSE;
        setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        date_default_timezone_set('America/Sao_Paulo');

        //Formata data
        $data_inicial = date("01/m/Y");
        $data_final = date("d/m/Y");

        //Busca emails das pessoas que receber�o Informativo
        $busca_emails_para_informativo = $this->Relatorio->emails_informativo();
        $parametroEmail = "";
        foreach ($busca_emails_para_informativo as $value) {
            foreach ($value as $email) {

                //Busca cd_usuario das pessoas que receber�o Informativo
                $busca_cd_usuario_para_email_para_informativo = $this->Relatorio->busca_cd_usuario_informativo($email['email_usuario']);
                $parametroCdUsuario = "";
                foreach ($busca_cd_usuario_para_email_para_informativo as $value) {
                    foreach ($value as $valor) {
                        $parametroCdUsuario .= "," . $valor['cd_usuario'];
                    }
                }
                $parametroCdUsuario = substr($parametroCdUsuario, 1);

                //Busca que empresa pertence a aquele usu�rio
                $busca_empresa_para_informativo = $this->Relatorio->busca_empresa_informativo($parametroCdUsuario);
                $parametroCdEmpresa = "";
                foreach ($busca_empresa_para_informativo as $value) {
                    foreach ($value as $valor) {
                        $parametroCdEmpresa .= "," . $valor['cd_empresa'];
                    }
                }
                $parametroCdEmpresa = substr($parametroCdEmpresa, 1);

                $emailMes = "";
                $emailDia = "";

                //Busca info dos bancos de dados das empresas
                $busca_dados_empresa_para_informativo = $this->Relatorio->busca_dados_empresa_informativo($parametroCdEmpresa);
                foreach ($busca_dados_empresa_para_informativo as $key) {
                    foreach ($key as $value) {
                        $nome_empresa = $value['nome_empresa'];
                        $host = $value['hostname_banco'];
                        $db = $value['nome_banco'];
                        $user = $value['usuario_banco'];
                        $password = $value['senha_banco'];
                        $porta = $value['porta_banco'];

                        //Vendas at� o momento
                        $dadosRelatorioVendaAcumulada = $this->Relatorio->dados_vendas_acumuladas($nome_empresa, $host, $db, $user, $password, $porta);
                        if ($dadosRelatorioVendaAcumulada != FALSE) {
                            $verificaValoresZerados = "";
                            foreach ($dadosRelatorioVendaAcumulada as $valor) {
                                $verificaValoresZerados += $valor['vendas'];
                            }
                            if ($verificaValoresZerados == 0) {
                                $dadosRelatorioVendaAcumulada = FALSE;
                            }
                            if ($dadosRelatorioVendaAcumulada != FALSE) {
                                $htmlVendaAcumulada = $this->page_footer_relatorios();
                                $htmlVendaAcumulada .= $this->geraHtmlVendaAcumulada($dadosRelatorioVendaAcumulada, $data_inicial, $data_final);
                                $informativo = $value['nome_empresa'] . " - Informativo de " . ucwords(strftime('%B de %Y', strtotime('today'))) . " - Vendas";
                                $emailMes .= $htmlVendaAcumulada;
                            }
                        }

                        //Recebimentos at� o momento
                        $dadosRelatorioRecebimentoAcumulado = $this->Relatorio->dados_vendas_recebimento_acumulado($nome_empresa, $host, $db, $user, $password, $porta);
                        if ($dadosRelatorioRecebimentoAcumulado != FALSE) {
                            $verificaValoresZerados = "";
                            foreach ($dadosRelatorioRecebimentoAcumulado as $valor) {
                                $verificaValoresZerados += $valor['quantidade'];
                            }
                            if ($verificaValoresZerados == 0) {
                                $dadosRelatorioRecebimentoAcumulado = FALSE;
                            }
                            if ($dadosRelatorioRecebimentoAcumulado != FALSE) {
                                $htmlRecebimentoAcumulado = $this->geraHtmlRecebimentoAcumulado($dadosRelatorioRecebimentoAcumulado, $data_inicial, $data_final);
                                $informativo = $value['nome_empresa'] . " - Informativo de " . ucwords(strftime('%B de %Y', strtotime('today'))) . " - Vendas/Recebimentos";
                                $emailMes .= $htmlRecebimentoAcumulado;
                            }
                        }

                        if (!$dadosRelatorioRecebimentoAcumulado == FALSE && !$dadosRelatorioVendaAcumulada == FALSE) {
                            $resultado = $this->enviarEmail($emailMes, $informativo, $email['email_usuario']);
                            var_dump($resultado);
                            //print_r($htmlRecebimentoAcumulado);
                            $emailMes = "";
                        }
                        if (!$dadosRelatorioVendaAcumulada == FALSE && $dadosRelatorioRecebimentoAcumulado == FALSE) {
                            $resultado = $this->enviarEmail($emailMes, $informativo, $email['email_usuario']);
                            var_dump($resultado);
                            //print_r($htmlRecebimentoAcumulado);
                            $emailMes = "";
                        }

                        //Vendas do �ltimo dia
                        $dadosRelatorioVendasUltimoDia = $this->Relatorio->dados_vendas_ultimo_dia($nome_empresa, $host, $db, $user, $password, $porta);
                        if ($dadosRelatorioVendasUltimoDia != FALSE) {
                            $verificaValoresZerados = "";
                            foreach ($dadosRelatorioVendasUltimoDia as $valor) {
                                $verificaValoresZerados += $valor['vendas'];
                            }
                            if ($verificaValoresZerados == 0) {
                                $dadosRelatorioVendasUltimoDia = FALSE;
                            }
                            if ($dadosRelatorioVendasUltimoDia != FALSE) {
                                $htmlVendasUltimoDia = $this->page_footer_relatorios();
                                $htmlVendasUltimoDia .= $this->geraHtmlVendasUltimoDia($dadosRelatorioVendasUltimoDia);
                                $informativo = $value['nome_empresa'] . " - Informativo do Dia " . date("d/m/Y", strtotime("-1 day")) . " - Vendas";
                                $emailDia .= $htmlVendasUltimoDia;
                            }
                        }

                        //Recebimento do �ltimo dia
                        $dadosRelatorioRecebimentoUltimoDia = $this->Relatorio->dados_vendas_recebimento_ultimo_dia($nome_empresa, $host, $db, $user, $password, $porta);
                        if ($dadosRelatorioRecebimentoUltimoDia != FALSE) {
                            $verificaValoresZerados = "";
                            foreach ($dadosRelatorioRecebimentoUltimoDia as $valor) {
                                $verificaValoresZerados += $valor['quantidade'];
                            }
                            if ($verificaValoresZerados == 0) {
                                $dadosRelatorioRecebimentoUltimoDia = FALSE;
                            }
                            if ($dadosRelatorioRecebimentoUltimoDia != FALSE) {
                                $htmlRecebimentoAcumuladoUltimoDia = $this->geraHtmlRecebimentoAcumuladoUltimoDia($dadosRelatorioRecebimentoUltimoDia);
                                $informativo = $value['nome_empresa'] . " - Informativo do Dia " . date("d/m/Y", strtotime("-1 day")) . " - Vendas/Recebimentos";
                                $emailDia .= $htmlRecebimentoAcumuladoUltimoDia;
                            }
                        }
                        if (!$dadosRelatorioRecebimentoUltimoDia == FALSE && !$dadosRelatorioVendasUltimoDia == FALSE) {
                            $resultado = $this->enviarEmail($emailDia, $informativo, $email['email_usuario']);
                            var_dump($resultado);
                            //print_r($htmlRecebimentoAcumuladoUltimoDia);
                            $emailDia = "";
                        }
                        if (!$dadosRelatorioVendasUltimoDia == FALSE && $dadosRelatorioRecebimentoUltimoDia == FALSE) {
                            $resultado = $this->enviarEmail($emailDia, $informativo, $email['email_usuario']);
                            var_dump($resultado);
                            //print_r($htmlRecebimentoAcumuladoUltimoDia);
                            $emailDia = "";
                        }
                        var_dump(date('h:i:s') . "\n");
                        $espera = sleep(10);
                        var_dump(date('h:i:s') . "\n");
                        var_dump($espera);
                    }
                }
            }
        }
    }

    public function page_footer_relatorios() {
        $html = "";
        $html .= "<table class='info' width='100%'cellspacing=0 style='border:#ccc 1px solid;  -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; -moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;'>
   					<tr>
   						<td style='text-align:center;'>
   				   			<img alt='SysApp' src='http://i.imgur.com/c7xz3E3.png'>
   							<b><h2 style='color:black'>� " . date('Y') . " Systec - Intelig&ecirc;ncia da Informa&ccedil;&atilde;o</h2></b>
   						</td>
   					</tr>
   					<tr>
		            	<td style='text-align:center;'>
   							<font size='2'> Telefone: (62) 3521.9940</font><br>
   						</td>
   					</tr>
   					<tr>
		            	<td style='text-align:center;'>
   							<a href='http://www.systecinfo.com.br' target='_blank'><font size='3'>www.systecinfo.com.br</font></a> | <a href='mailto:contato@systecinfo.com.br'><font size='3'>contato@systecinfo.com.br</font></a><br>
		        		</td>
   					</tr>  
   				</table>";
        return $html;
    }

    public function geraHtmlVendaAcumulada($dadosRelatorioVendaAcumulada, $data_inicial, $data_final) {
        if ($dadosRelatorioVendaAcumulada != FALSE) {
            $totalGeralValorVendido = "";
            if (isset($dadosRelatorioVendaAcumulada)) {
                foreach ($dadosRelatorioVendaAcumulada as $value) {
                    $totalGeralValorVendido += $value['vlr_lanc'];
                }
            }
        }

        $html = "";
        $html .= "<br/><br/><div id='pai'>
					<body id='voltarTopo'>
					<table id='cabecalhoTabela' width='100%' border='0' cellspacing='0' cellpadding='0' style='text-align: center;'>
					    <tr>
					        <td style='font-size: 24px; font-family: verdana;'>Relat&oacute;rio Vendas por Loja Mensal<br></td>
					    </tr>
					    <tr style='background-color:#F9F9F9;'>
					    	<td id='cabecalhoEmissao' style='text-align: right;	padding-right: 150px; font-family: verdana; font-size: 12px; border:1px #DDDDDD;'><b>Emiss&atilde;o:" . date('d/m/Y H:i:s') . "</b><br>
					    	<b>Per&iacute;odo:" . $data_inicial . " a " . $data_final . "</b></td>
					    </tr>
					</table>
					<br>
					<div class='contentRelatorio'>
					    <table class='info' width='100%'cellspacing=0 style='border:#ccc 1px solid;  -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; -moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;'>
					        <tr style='text-align: center;height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
					        	<td style=' -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;	background-color: #59a6d6;'></td>
					            <td style='background-color: #59a6d6;'>Pecas</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Devol.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Saldo</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Qtd. Vd.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Meta R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>V. Vendido R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>% Part.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>% Real</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Desc. R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>P/S</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Trocas</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Ticket M.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>V. Medio Prod.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; background-color: #59a6d6;'>Filial</td>
					        </tr>
		";
        $totalGeralVlrLanc = "";
        $totalGeralItens = "";
        $totalGeralVlrItensTroca = "";
        $totalGeralVlrItensSaldo = "";
        $totalGeralVendas = "";
        $totalGeralMetas = "";
        $totalGeralPercentReal = "";
        $totalGeralVlrDesconto = "";
        $totalGeralVendasTrocas = "";
        $totalGeralTicketM = "";
        $totalGeralVMedioProd = "";

        if ($dadosRelatorioVendaAcumulada != FALSE) {
            if (isset($dadosRelatorioVendaAcumulada)) {
                foreach ($dadosRelatorioVendaAcumulada as $chave) {
                    $totalGeralVlrLanc += $chave['vlr_lanc'];
                    $totalGeralItens += $chave['itens'];
                    $totalGeralVlrItensTroca += $chave['itens_troca'];
                    $totalGeralVlrItensSaldo += $chave['itens_saldo'];
                    $totalGeralVendas += $chave['vendas'];
                    $totalGeralMetas += $chave['metas'];
                    $totalGeralPercentReal += $chave['percent_real'];
                    $totalGeralVlrDesconto += $chave['vlr_desconto'];
                    $totalGeralVendasTrocas += $chave['vendas_trocas'];
                    $totalGeralTicketM += $chave['ticket_medio'];
                    $totalGeralVMedioProd += $chave['vlr_medio_prod'];
                    if ($chave['vendas'] != 0) {
                        $html .= "	<tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352; padding-left: 10px; padding-right:10px;'>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        			" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($chave['nm_fant'], 0, 20)) . "<br>
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['itens'], 0, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . $chave['itens_troca'] . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['itens_saldo'], 0, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vendas'], 0, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['metas'], 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vlr_lanc'], 2, ',', '.') . "
			        	</td>";
                        if ($totalGeralValorVendido != 0) {
                            $html .= "<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format(($chave['vlr_lanc'] / $totalGeralValorVendido * 100), 2, ',', '.') . "
			        	</td>";
                        } else {
                            $html .= "<td style=' padding-left: 10px; padding-right:10px;'>0,00</td>";
                        }
                        $html .= "<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format(($chave['percent_real']), 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vlr_desconto'], 2, ',', '.') . "
			        	</td>";
                        if ($chave['vendas'] != 0) {
                            $html .= "<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . (number_format($chave['itens'] / $chave['vendas'], 2, ',', '.')) . "
			        	</td>";
                        } else {
                            $html .= "<td style=' padding-left: 10px; padding-right:10px;'>0,00</td>";
                        }
                        $html .= "<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . $chave['vendas_trocas'] . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['ticket_medio'], 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vlr_medio_prod'], 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($chave['nm_fant'], 0, 20)) . "<br>
			        	</td>
			        </tr>";
                    }
                }

                $html .= "  <tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
			        	<td style='-moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px;	background-color: #BBE42F;'>Total Geral :</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralItens, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrItensTroca, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrItensSaldo, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVendas, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralMetas, 2, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrLanc, 2, ',', '.') . "</td>";
                if ($totalGeralValorVendido != 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(($totalGeralVlrLanc / $totalGeralValorVendido * 100), 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>0,00</td>";
                }
                if (!$totalGeralMetas == 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(($totalGeralVlrLanc / $totalGeralMetas * 100), 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>0,00</td>";
                }
                $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrDesconto, 2, ',', '.') . "</td>";
                if ($totalGeralVendas) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralItens / $totalGeralVendas, 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>0,00</td>";
                }
                $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVendasTrocas) . "</td>";
                if (($totalGeralVendas - $totalGeralVendasTrocas) != 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrLanc / ($totalGeralVendas - $totalGeralVendasTrocas), 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>0,00</td>";
                }
                if ($totalGeralVlrItensSaldo != 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrLanc / $totalGeralVlrItensSaldo, 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>0,00</td>";
                }
                $html .= "<td style=' -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; background-color: #BBE42F; 	'></td>
			        </tr>
			</table>
		</div>
	</body>
</div><br/><br/><br/><br/>";
            }
        }
        return $html;
    }

    public function geraHtmlVendasUltimoDia($dadosRelatorioVendasUltimoDia) {

        $data_inicial = date("Y-m-d", strtotime("-1 day"));
        $data_final = date("Y-m-d", strtotime("-1 day"));

        if ($dadosRelatorioVendasUltimoDia != FALSE) {
            $totalGeralValorVendido = "";
            if (isset($dadosRelatorioVendasUltimoDia)) {
                foreach ($dadosRelatorioVendasUltimoDia as $value) {
                    $totalGeralValorVendido += $value['vlr_lanc'];
                }
            }
        }

        $html = "";
        $html .= "<div id='pai'>
					<body id='voltarTopo'>
					<table id='cabecalhoTabela' width='100%' border='0' cellspacing='0' cellpadding='0' style='text-align: center; border-color:red'>
					    <tr>
					        <td style='font-size: 24px; font-family: verdana;'>Relat&oacute;rio Vendas por Loja do &Uacute;ltimo Dia<br></td>
					    </tr>
					    <tr style='background-color:#F9F9F9;'>
					    	<td id='cabecalhoEmissao' style='text-align: right;	padding-right: 150px; font-family: verdana; font-size: 12px; border:1px #DDDDDD;'><b>Emiss&atilde;o: " . date('d/m/Y H:i:s') . "</b><br>
					    	<b>Per&iacute;odo: " . date("d/m/Y", strtotime("-1 day")) . " a " . date("d/m/Y", strtotime("-1 day")) . "</b></td>
					    </tr>
					</table>
					<br>
					<div class='contentRelatorio'>
					    <table class='info' width='100%'cellspacing=0 style='border:#ccc 1px solid;  -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; -moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;'>
					        <tr style=' border: 0px; height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
					        	<td style=' -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;	background-color: #59a6d6;'></td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Pecas</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Devol.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Saldo</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Qtd. Vd.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Meta R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>V. Vendido R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>% Part.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>% Real</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Desc. R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>P/S</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Trocas</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Ticket M.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>V. Medio Prod.</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; background-color: #59a6d6;'>Filial</td>
					        </tr>
		";
        $totalGeralVlrLanc = "";
        $totalGeralItens = "";
        $totalGeralVlrItensTroca = "";
        $totalGeralVlrItensSaldo = "";
        $totalGeralVendas = "";
        $totalGeralMetas = "";
        $totalGeralPercentReal = "";
        $totalGeralVlrDesconto = "";
        $totalGeralVendasTrocas = "";
        $totalGeralTicketM = "";
        $totalGeralVMedioProd = "";

        if ($dadosRelatorioVendasUltimoDia != FALSE) {
            if (isset($dadosRelatorioVendasUltimoDia)) {
                foreach ($dadosRelatorioVendasUltimoDia as $chave) {
                    $totalGeralVlrLanc += $chave['vlr_lanc'];
                    $totalGeralItens += $chave['itens'];
                    $totalGeralVlrItensTroca += $chave['itens_troca'];
                    $totalGeralVlrItensSaldo += $chave['itens_saldo'];
                    $totalGeralVendas += $chave['vendas'];
                    $totalGeralMetas += $chave['metas'];
                    $totalGeralPercentReal += $chave['percent_real'];
                    $totalGeralVlrDesconto += $chave['vlr_desconto'];
                    $totalGeralVendasTrocas += $chave['vendas_trocas'];
                    $totalGeralTicketM += $chave['ticket_medio'];
                    $totalGeralVMedioProd += $chave['vlr_medio_prod'];

                    if ($chave['vendas'] != 0) {
                        $html .= "	<tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        			" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($chave['nm_fant'], 0, 20)) . "<br>
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['itens'], 0, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . $chave['itens_troca'] . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['itens_saldo'], 0, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vendas'], 0, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['metas'], 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vlr_lanc'], 2, ',', '.') . "
			        	</td>";
                        if ($totalGeralValorVendido != 0) {
                            $html .= " <td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format(($chave['vlr_lanc'] / $totalGeralValorVendido * 100), 2, ',', '.') . "
			        	</td>";
                        } else {
                            $html .= " <td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format(0, 2, ',', '.') . "
			        	</td>";
                        }
                        $html .= " 	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format(($chave['percent_real']), 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vlr_desconto'], 2, ',', '.') . "
			        	</td>";
                        if ($chave['vendas'] != 0) {
                            $html .= " <td style=' padding-left: 10px; padding-right:10px;'>
			        		" . (number_format($chave['itens'] / $chave['vendas'], 2, ',', '.')) . "
			        	</td>";
                        } else {
                            $html .= " <td style=' padding-left: 10px; padding-right:10px;'>
			        		" . (0) . "
			        	</td>";
                        }
                        $html .= "	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . $chave['vendas_trocas'] . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['ticket_medio'], 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . number_format($chave['vlr_medio_prod'], 2, ',', '.') . "
			        	</td>
			        	<td style=' padding-left: 10px; padding-right:10px;'>
			        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($chave['nm_fant'], 0, 20)) . "<br>
			        	</td>
			        </tr>";
                    }
                }

                $html .= "  <tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
			        	<td style='-moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px;	background-color: #BBE42F;'>Total Geral :</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralItens, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrItensTroca, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrItensSaldo, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVendas, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralMetas, 2, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrLanc, 2, ',', '.') . "</td>";
                if ($totalGeralValorVendido != 0) {
                    $html .= " <td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(($totalGeralVlrLanc / $totalGeralValorVendido * 100), 2, ',', '.') . "</td>";
                } else {
                    $html .= " <td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(0, 2, ',', '.') . "</td>";
                }
                if (!$totalGeralMetas == 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(($totalGeralVlrLanc / $totalGeralMetas * 100), 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>0,00</td>";
                }
                $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrDesconto, 2, ',', '.') . "</td>";
                if ($totalGeralVendas != 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralItens / $totalGeralVendas, 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(0, 2, ',', '.') . "</td>";
                }
                $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVendasTrocas) . "</td>";
                if (($totalGeralVendas - $totalGeralVendasTrocas) != 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrLanc / ($totalGeralVendas - $totalGeralVendasTrocas), 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(0, 2, ',', '.') . "</td>";
                }
                if ($totalGeralVlrItensSaldo != 0) {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrLanc / $totalGeralVlrItensSaldo, 2, ',', '.') . "</td>";
                } else {
                    $html .= "<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(0, 2, ',', '.') . "</td>";
                }
                $html .= "<td style=' -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; background-color: #BBE42F;'></td>
			        </tr>
			</table>
		</div>
	</body>
</div><br/><br/><br/><br/>";
            }
        }
        return $html;
    }

    public function geraHtmlRecebimentoAcumulado($dadosRelatorioRecebimentoAcumulado, $data_inicial, $data_final) {

        if ($dadosRelatorioRecebimentoAcumulado != FALSE) {
            $filialAnterior = -1;
            $totalGeralQuantidadeFilial = "";
            $totalGeralVlrRecebidoFilial = "";
            $totalGeralVlrMedioFilial = "";
            $totalPorFilialNmFant = "";

            foreach ($dadosRelatorioRecebimentoAcumulado as $key => $value) {
                //$totalGeralVlrRecebido += $value['valor_recebido'];
                if ($filialAnterior === $value['nm_fant']) {
                    $totalGeralQuantidadeFilial += $value['quantidade'];
                    $totalGeralVlrRecebidoFilial += $value['valor_recebido'];
                    $totalPorFilialNmFant = $value['nm_fant'];
                } else {
                    if ($totalGeralVlrRecebidoFilial != "") {
                        $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_valor_recebido' => $totalGeralVlrRecebidoFilial, 'total_nm_fant' => $totalPorFilialNmFant
                        );
                    }
                    $totalGeralVlrRecebidoFilial = $value['valor_recebido'];
                    $totalGeralQuantidadeFilial = $value['quantidade'];
                    $totalPorFilialNmFant = $value['nm_fant'];
                    $filialAnterior = $value['nm_fant'];
                }
            }

            $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_valor_recebido' => $totalGeralVlrRecebidoFilial, 'total_nm_fant' => $totalPorFilialNmFant
            );
        }
        $totalGeralQuantidade = "";
        $totalGeralVlrRecebido = "";
        $totalGeralVlrMedio = "";
        if ($dadosRelatorioRecebimentoAcumulado != FALSE) {
            $totalGeralValorVendido = "";
            if (isset($dadosRelatorioRecebimentoAcumulado)) {
                foreach ($dadosRelatorioRecebimentoAcumulado as $value) {
                    $totalGeralQuantidade += $value['quantidade'];
                    $totalGeralVlrRecebido += $value['valor_recebido'];
                    $totalGeralVlrMedio += $value['valor_medio'];
                }
            }
        }

        $html = "";
        $html .= "<br/><br/><div id='pai'>
					<body id='voltarTopo'>
					<table id='cabecalhoTabela' width='100%' border='0' cellspacing='0' cellpadding='0' style='text-align: center; border-color:red'>
					    <tr>
					        <td style='font-size: 24px; font-family: verdana;'>Relat&oacute;rio de Recebimento Acumulado Mensal<br></td>
					    </tr>
					    <tr style='background-color:#F9F9F9;'>
					    	<td id='cabecalhoEmissao' style='text-align: right;	padding-right: 150px; font-family: verdana; font-size: 12px; border:1px #DDDDDD;'><b>Emiss&atilde;o: " . date('d/m/Y H:i:s') . "</b><br>
					    	<b>Per&iacute;odo: " . $data_inicial . " a " . $data_final . "</b></td>
					    </tr>
					</table>
					<br>
					<div class='contentRelatorio'>
					    <table class='info' width='100%'cellspacing=0 style='border:#ccc 1px solid;  -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; -moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;'>
					        <tr style='border: 0px; height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
					        	<td style=' -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;	background-color: #59a6d6;'></td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Hor&aacute;rio de Recebimento</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Quantidade</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Valor Recebido R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Valor Medio R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; background-color: #59a6d6;'>Filial</td>
					        </tr>
		";

        $filialAnterior = -1;
        if ($dadosRelatorioRecebimentoAcumulado != FALSE) {
            if (isset($dadosRelatorioRecebimentoAcumulado)) {
                foreach ($dadosRelatorioRecebimentoAcumulado as $value) {
                    if ($filialAnterior === $value['nm_fant']) {
                        $html .= "<tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
	        			            <td>
	        			        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
	        			        	</td>
	        			        	<td>
	        			        		" . str_replace(array("�", "�", "�", "�s"), array("&Ccedil;", "&Atilde;", "", "&agrave;s"), $value['horario_recebimento']) . " horas" . "
	        			        	</td>
	        			        	<td>
	        			        		" . number_format($value['quantidade']) . "
	        			        	</td>
	        			        	<td>
	        			        		" . number_format($value['valor_recebido'], 2, ',', '.') . "
	        			        	</td>
	        			        	<td>
	        			        		" . number_format($value['valor_medio'], 2, ',', '.') . "
	        			        	</td>
	        			        	<td>
	        			        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
	        			        	</td>
	        			        </tr>";
                    } else {
                        if ($filialAnterior != $value['nm_fant']) {
                            foreach ($dadosRelatorioFilial as $chave) {
                                if ($value['nm_fant'] == $chave['total_nm_fant']) {

                                    $html .= "<tr style='background-color:#FFFFFF;'>
        						   <td> </td>
        						</tr>
        				        <tr style='background-color: #32C7FF;height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
        				        	<td>
        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
        				        	</td>
        				        	<td></td>
        				        	<td>
        				        		" . $chave['total_quantidade'] . "
        				        	</td>
        				        	<td>
        				        		" . number_format($chave['total_valor_recebido'], 2, ',', '.') . "
        				        	</td>
        				        	<td>
        				        		" . number_format($chave['total_valor_recebido'] / $chave['total_quantidade'], 2, ',', '.') . "
        				        	</td>
        				        	<td>
        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
        				        	</td>
        				        </tr>
        				        <tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
        				            <td>
        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
        				        	</td>
        				        	<td>
        				        		" . str_replace(array("�", "�", "�", "�s"), array("&Ccedil;", "&Atilde;", "", "&agrave;s"), $value['horario_recebimento']) . ' horas' . "
        				        	</td>
        				        	<td>
        				        		" . number_format($value['quantidade']) . "
        				        	</td>
        				        	<td>
        				        		" . number_format($value['valor_recebido'], 2, ',', '.') . "
        				        	</td>
        				        	<td>
        				        		" . number_format($value['valor_medio'], 2, ',', '.') . "
        				        	</td>
        				        	<td>
        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
        				        	</td>
        				        </tr>";
                                    $filialAnterior = $value['nm_fant'];
                                }
                            }
                        }
                    }
                }

                $html .= "<tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
			        	<td style='-moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px;	background-color: #BBE42F;'>Total Geral :</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'></td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralQuantidade, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrRecebido, 2, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(($totalGeralVlrRecebido / $totalGeralQuantidade), 2, ',', '.') . "</td>
			        	<td style=' -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; background-color: #BBE42F; '></td>
        			  </tr>
			</table>
		</div>
	</body>
</div><br/><br/><br/><br/>";
            }
        }
        return $html;
    }

    public function geraHtmlRecebimentoAcumuladoUltimoDia($dadosRelatorioRecebimentoUltimoDia) {

        $data_inicial = date("Y-m-d", strtotime("-1 day"));
        $data_final = date("Y-m-d", strtotime("-1 day"));

        if ($dadosRelatorioRecebimentoUltimoDia != FALSE) {
            $filialAnterior = -1;
            $totalGeralQuantidadeFilial = "";
            $totalGeralVlrRecebidoFilial = "";
            $totalGeralVlrMedioFilial = "";
            $totalPorFilialNmFant = "";

            foreach ($dadosRelatorioRecebimentoUltimoDia as $key => $value) {
                //$totalGeralVlrRecebido += $value['valor_recebido'];
                if ($filialAnterior === $value['nm_fant']) {
                    $totalGeralQuantidadeFilial += $value['quantidade'];
                    $totalGeralVlrRecebidoFilial += $value['valor_recebido'];
                    $totalPorFilialNmFant = $value['nm_fant'];
                } else {
                    if ($totalGeralVlrRecebidoFilial != "") {
                        $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_valor_recebido' => $totalGeralVlrRecebidoFilial, 'total_nm_fant' => $totalPorFilialNmFant
                        );
                    }
                    $totalGeralVlrRecebidoFilial = $value['valor_recebido'];
                    $totalGeralQuantidadeFilial = $value['quantidade'];
                    $totalPorFilialNmFant = $value['nm_fant'];
                    $filialAnterior = $value['nm_fant'];
                }
            }

            $dadosRelatorioFilial[] = array('total_quantidade' => $totalGeralQuantidadeFilial, 'total_valor_recebido' => $totalGeralVlrRecebidoFilial, 'total_nm_fant' => $totalPorFilialNmFant
            );
        }

        $totalGeralQuantidade = "";
        $totalGeralVlrRecebido = "";
        $totalGeralVlrMedio = "";
        if ($dadosRelatorioRecebimentoUltimoDia != FALSE) {
            $totalGeralValorVendido = "";
            if (isset($dadosRelatorioRecebimentoUltimoDia)) {
                foreach ($dadosRelatorioRecebimentoUltimoDia as $value) {
                    $totalGeralQuantidade += $value['quantidade'];
                    $totalGeralVlrRecebido += $value['valor_recebido'];
                    $totalGeralVlrMedio += $value['valor_medio'];
                }
            }
        }

        $html = "";
        $html .= "<div id='pai'>
					<body id='voltarTopo'>
					<table id='cabecalhoTabela' width='100%' border='0' cellspacing='0' cellpadding='0' style='text-align: center; border-color:red'>
					    <tr>
					        <td style='font-size: 24px; font-family: verdana;'>Relat&oacute;rio de Recebimento do &Uacute;ltimo Dia<br></td>
					    </tr>
					    <tr style='background-color:#F9F9F9;'>
					    	<td id='cabecalhoEmissao' style='text-align: right;	padding-right: 150px; font-family: verdana; font-size: 12px; border:1px #DDDDDD;'><b>Emiss&atilde;o: " . date('d/m/Y H:i:s') . "</b><br>
					    	<b>Per&iacute;odo: " . date("d/m/Y", strtotime("-1 day")) . " a " . date("d/m/Y", strtotime("-1 day")) . "</b></td>
					    </tr>
					</table>
					<br>
					<div class='contentRelatorio'>
					    <table class='info' width='100%'cellspacing=0 style='border:#ccc 1px solid;  -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; -moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;'>
					        <tr style='border: 0px; height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
					        	<td style=' -moz-border-radius-topleft:10px; -webkit-border-top-left-radius:10px; border-top-left-radius:10px;	background-color: #59a6d6;'></td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Hor&aacute;rio de Recebimento</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Quantidade</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Valor Recebido R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6;'>Valor Medio R$</td>
					            <td style=' padding-left: 5px; padding-right:5px;background-color: #59a6d6; -moz-border-radius-topright:10px; -webkit-border-top-right-radius:10px; border-top-right-radius:10px; background-color: #59a6d6;'>Filial</td>
					        </tr>
		";

        $filialAnterior = -1;
        if ($dadosRelatorioRecebimentoUltimoDia != FALSE) {
            if (isset($dadosRelatorioRecebimentoUltimoDia)) {
                foreach ($dadosRelatorioRecebimentoUltimoDia as $value) {
                    if ($filialAnterior === $value['nm_fant']) {
                        $html .= "<tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
	        			            <td style=' padding-left: 5px; padding-right:5px;'>
	        			        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
	        			        	</td>
	        			        	<td style=' padding-left: 5px; padding-right:5px;'>
	        			        		" . str_replace(array("�", "�", "�", "�s"), array("&Ccedil;", "&Atilde;", "", "&agrave;s"), $value['horario_recebimento']) . " horas" . "
	        			        	</td>
	        			        	<td style=' padding-left: 5px; padding-right:5px;'>
	        			        		" . number_format($value['quantidade']) . "
	        			        	</td>
	        			        	<td style=' padding-left: 5px; padding-right:5px;'>
	        			        		" . number_format($value['valor_recebido'], 2, ',', '.') . "
	        			        	</td>
	        			        	<td style=' padding-left: 5px; padding-right:5px;'>
	        			        		" . number_format($value['valor_medio'], 2, ',', '.') . "
	        			        	</td>
	        			        	<td style=' padding-left: 5px; padding-right:5px;'>
	        			        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
	        			        	</td>
	        			        </tr>";
                    } else {
                        if ($filialAnterior != $value['nm_fant']) {
                            foreach ($dadosRelatorioFilial as $chave) {
                                if ($value['nm_fant'] == $chave['total_nm_fant']) {
                                    $html .= "<tr style='background-color:#FFFFFF;'>
				        						   <td> </td>
				        						</tr>
				        				        <tr style='background-color: #32C7FF;height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
				        				        	</td>
				        				        	<td></td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . $chave['total_quantidade'] . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . number_format($chave['total_valor_recebido'], 2, ',', '.') . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . number_format($chave['total_valor_recebido'] / $chave['total_quantidade'], 2, ',', '.') . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
				        				        	</td>
				        				        </tr>
				        				        <tr style='height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
				        				            <td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . str_replace(array("�", "�", "�", "�s"), array("&Ccedil;", "&Atilde;", "", "&agrave;s"), $value['horario_recebimento']) . ' horas' . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . number_format($value['quantidade']) . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . number_format($value['valor_recebido'], 2, ',', '.') . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . number_format($value['valor_medio'], 2, ',', '.') . "
				        				        	</td>
				        				        	<td style=' padding-left: 5px; padding-right:5px;'>
				        				        		" . str_replace(array("�", "�", "�"), array("&Ccedil;", "&Atilde;", ""), substr($value['nm_fant'], 0, 20)) . "
				        				        	</td>
				        				        </tr>";
                                    $filialAnterior = $value['nm_fant'];
                                }
                            }
                        }
                    }
                }

                $html .= "<tr style=' height:30px; font-family: verdana; font-size: 12px; color:#3A3352;'>
			        	<td style='-moz-border-radius-bottomleft:10px;	-webkit-border-bottom-left-radius:10px;	border-bottom-left-radius:10px;	background-color: #BBE42F;'>Total Geral :</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'></td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralQuantidade, 0, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format($totalGeralVlrRecebido, 2, ',', '.') . "</td>
			        	<td style=' padding-left: 10px; padding-right:10px;background-color: #BBE42F;'>" . number_format(($totalGeralVlrRecebido / $totalGeralQuantidade), 2, ',', '.') . "</td>
			        	<td style=' -moz-border-radius-bottomright:10px; -webkit-border-bottom-right-radius:10px;	border-bottom-right-radius:10px; background-color: #BBE42F;'></td>
        			  </tr>
			</table>
		</div>
	</body>
</div><br/><br/><br/><br/>";
            }
        }
        return $html;
    }

    private function gerarPDF($html) {

        App::import('Vendor', 'mpdf', array('file' => 'mpdf' . DS . 'mpdf.php'));
        $diretorio = WWW_ROOT . 'file' . DS . 'upload/';
        $nomeArquivo = $diretorio . DS . time() . ".pdf";

        $mpdf = new mPDF('c', 'A4-L', '', '');
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->list_indent_first_level = 0;
        $stylesheet = file_get_contents(WWW_ROOT . 'css/mpdfstyletables.css');
        $mpdf->WriteHTML($stylesheet, 1);
        $mpdf->WriteHTML($html, 2);
        $mpdf->Output($nomeArquivo, 'F');

        return $nomeArquivo;
    }

    /**
     * Metodo responsavel por enviar o informativo di�rio por e-mail
     * @author Edson Rodrigues <ed.juniior01@gmail.com>
     * @return boolean
     */
    private function enviarEmail($anexo, $informativo, $email) {

        $resultado = '';

        App::import('Vendor', 'PHPMailer', array('file' => 'PHPMailer' . DS . 'class.phpmailer.php'));
        //Primeiro setamos o cabeÃ§alho:
        $header = " Content-type: text/html; charset=iso-8859-1\r\n";
        //instanciamos o objeto
        $mail = new PHPMailer();

        $mail->SMTPDebug = 0;

        $mail->IsSMTP(); // Define que a mensagem ser� SMTP
        $mail->Host = "smtp.systecinfo.com.br"; // Endere�o do servidor SMTP
        $mail->SMTPAuth = true; // Usa autentica��o SMTP? (opcional)
        $mail->Port = 587;
        $mail->Username = 'cadastro@systecinfo.com.br'; // Usu�rio do servidor SMTP
        $mail->Password = '56thjm,.'; // Senha do servidor SMTP
        // Define o remetente
        // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        $mail->From = "cadastro@systecinfo.com.br"; // Seu e-mail
        $mail->FromName = utf8_encode("SysApp - Informativo Di�rio"); // Seu nome

        if (isset($email)) {
            $mail->AddAddress($email);
        }
        $mail->AddBCC('cadastro@systecinfo.com.br'); // C�pia Oculta

        $mail->IsHTML(true); // Define que o e-mail ser� enviado como HTML
        $mail->CharSet = 'UTF-8'; // Charset da mensagem (opcional)
        //Podemos setar qual é o tamanho do texto por linha pra quebrar a linha de forma automÃ¡tica
        $mail->WordWrap = 50;
        /*     	
          //vamos anexar os arquivos
          foreach($anexo as $arquivo){
          $mail->AddAttachment($arquivo['anexo']);
          } */

        //Setamos a propriedade do HTML para true
        $mail->IsHTML(true);

        //Colocamos o texto do email
        $mail->Body = utf8_encode("<b>SysApp - Seu informativo di�rio gerencial</b><br/>
    						
    						<br/>Acesse: webapp.systec.ftp.sh para mais relat�rios.<br/>
    						
    			<br/><b>Equipe SysApp agradece !</b>
    			<br/>
    			");

        //Adicionaremos o Page Footer
        if ($anexo != "") {
            $anexo .= $this->page_footer_relatorios();
        }

        //vamos anexar os arquivos
        $mail->Body = utf8_encode($anexo);

        //Vamos colocar o assunto do email
        $mail->Subject = utf8_encode("$informativo");

        if ($anexo != "") {
            //e mandamos enviar:
            if (!$mail->Send()) {
                $resultado = $mail->ErrorInfo;
            } else {
                $resultado = "OK";
            }
        }

        return $resultado;
    }

    public function recebimento_faixa_atraso() {

//21957: Configurar acesso por loja WebApp
        $this->loadModel("ConfigUserSysApp");

        $cd_usuario = $this->Session->read('Questionarios.cd_usu');

        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);

        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);

       $filiais = $this->PrcFilial->find("all",
                        array(
                                'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'), 
                                'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'), 'order' => 'PrcFilial.nm_fant',
                                "joins" => array(
                                        array(
                                                "table" => "segu_usu_filial",
                                                "alias" => "usuario",
                                                "type" => "INNER",
                                                "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")))
                            ));

        $this->set(compact('filiais'));

        $this->loadModel("TipoPagamento");
        $this->TipoPagamento->setDataSource($_SESSION['Config']['database']);
        $tpPagamentos = $this->TipoPagamento->find('all', array('fields' => array('cd_doc', 'ds_doc')));
        $this->set(compact('tpPagamentos'));

        $this->loadModel("FaixaAtraso");
        $this->FaixaAtraso->setDataSource($_SESSION['Config']['database']);
        $faixaAtrasos = $this->FaixaAtraso->find('all', array('fields' => array('ds_periodo', 'cd_ctr', 'atraso_inicial', 'atraso_inicial'), 'order' => array('ds_periodo')));
        $this->set(compact('faixaAtrasos'));

        if ($this->request->is('post')) {
            //print_r($this->request->data);

            define('__ROOT__', dirname(dirname(__FILE__)));
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/PHPJasperXML.inc.php');
            require_once (__ROOT__ . '/Vendor/PHPJasperXML/tcpdf/tcpdf.php');

            if ($this->request->data['Relatorios']['cliente'] == '1') {
                $param_cd_pessoa = '';
            } else {
                $pessoas = $this->request->data['Relatorios']['cliente'];
                $param_cd_pessoa = " AND rc_lanc.cd_pessoa IN ($pessoas)";
            }

            if (isset($this->request->data['Relatorios']['filial'])) {
                $cod_filiais = '';
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            } else {
                $this->loadModel("PrcFilial");
                $cd_filial = $this->PrcFilial->find('all', array('fields' => 'cd_filial', 'group' => 'cd_filial', 'order' => 'cd_filial'));
                $cod_filial = '';
                foreach ($cd_filial as $key) {
                    foreach ($key as $chave) {
                        foreach ($chave as $value) {
                            $cod_filial .= "," . $value;
                        }
                    }
                }
                $param_cd_filial = substr($cod_filial, 1);
            }

            if (isset($this->request->data['Relatorios']['pgto'])) {
                $cd_doc = '';
                foreach ($this->request->data['Relatorios']['pgto'] as $value) {
                    $cd_doc .= "," . $value;
                }
                $param_cd_doc = substr($cd_doc, 1);
            } else {
                $this->loadModel("TipoPagamento");
                $tpPagamentos = $this->TipoPagamento->find('all', array('fields' => array('cd_doc', 'ds_doc')));
                $cod_pagamento = '';
                foreach ($tpPagamentos as $key) {
                    foreach ($key as $chave) {
                        foreach ($chave as $value) {
                            $cod_pagamento .= "," . $value;
                        }
                    }
                }
                $param_cd_doc = substr($cod_pagamento, 1);
            }

            if (isset($this->request->data['Relatorios']['faixaAtraso'])) {
                $cod_faixa_atraso = '';
                foreach ($this->request->data['Relatorios']['faixaAtraso'] as $value) {
                    $cod_faixa_atraso .= "," . $value;
                }
                $cod_faixa_atraso = substr($cod_faixa_atraso, 1);

                $menorFaixa = min($this->request->data['Relatorios']['faixaAtraso']);
                $maiorFaixa = max($this->request->data['Relatorios']['faixaAtraso']);

                $cd_ctr_menor = $this->FaixaAtraso->find('first', array('fields' => 'cd_ctr', 'conditions' => array('atraso_inicial' => $menorFaixa)));
                $cd_ctr_maior = $this->FaixaAtraso->find('first', array('fields' => 'cd_ctr', 'conditions' => array('atraso_inicial' => $maiorFaixa)));

                $menorFaixaSelecionada = $this->FaixaAtraso->find('all', array('fields' => array('atraso_inicial'), 'conditions' => array('cd_ctr' => $cd_ctr_menor['FaixaAtraso']['cd_ctr'])));
                $maiorFaixaSelecionada = $this->FaixaAtraso->find('all', array('fields' => array('atraso_final'), 'conditions' => array('cd_ctr' => $cd_ctr_maior['FaixaAtraso']['cd_ctr'])));

                $menorSelecionada = $menorFaixaSelecionada[0]['FaixaAtraso']['atraso_inicial'] . " dias ";
                if ($menorFaixaSelecionada[0]['FaixaAtraso']['atraso_inicial'] == "1") {
                    $menorSelecionada = $menorFaixaSelecionada[0]['FaixaAtraso']['atraso_inicial'] . " dia ";
                }
                $maiorSelecionada = $maiorFaixaSelecionada[0]['FaixaAtraso']['atraso_final'] . " dias ";

                $menorFaixa = "'" . $menorFaixaSelecionada[0]['FaixaAtraso']['atraso_inicial'] . " days" . "'";
                $maiorFaixa = "'" . $maiorFaixaSelecionada[0]['FaixaAtraso']['atraso_final'] . " days" . "'";
            }

            /* 			if(isset($this->request->data['Relatorios']['pgtoAntecipado'])){

              }else {

              } */

            $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

            if (empty($this->request->data['Relatorios']['per_ini_baixa'])) {
                $per_ini_baixa = date('Y-m-d');
            } else {
                $per_ini_baixa_mostra = $this->request->data['Relatorios']['per_ini_baixa'];
                $per_ini_baixa = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_baixa']);
                $per_ini_baixa = "'" . $per_ini_baixa . "'";
            }
            if (empty($this->request->data['Relatorios']['per_fim_baixa'])) {
                $per_fim_baixa = date('Y-m-d');
            } else {
                $per_fim_baixa_mostra = $this->request->data['Relatorios']['per_fim_baixa'];
                $per_fim_baixa = $funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_baixa']);
                $per_fim_baixa = "'" . $per_fim_baixa . "'";
            }
            $cifrao = "R$";

            $PHPJasperXML = new PHPJasperXML();
            $xml = simplexml_load_file('relatorios_recebimento_faixa_de_atraso.jrxml'); //file name
            $PHPJasperXML->arrayParameter = array("cifrao" => $cifrao, "per_ini_baixa_mostra" => $per_ini_baixa_mostra, "per_fim_baixa_mostra" => $per_fim_baixa_mostra, "param_cd_pessoa" => $param_cd_pessoa, "param_cd_filial" => $param_cd_filial, "param_cd_doc" => $param_cd_doc, "menorFaixa" => $menorFaixa,
                "maiorFaixa" => $maiorFaixa, "per_ini_baixa" => $per_ini_baixa, "per_fim_baixa" => $per_fim_baixa, "cod_faixa_atraso" => $cod_faixa_atraso, "menorSelecionada" => $menorSelecionada, "maiorSelecionada" => $maiorSelecionada);
            $PHPJasperXML->xml_dismantle($xml);
            $host = $_SESSION['Config']['host'];
            $db = $_SESSION['Config']['databasename'];
            $user = $_SESSION['Config']['user'];
            $password = $_SESSION['Config']['password'];
            $PHPJasperXML->transferDBtoArray("'" . $host . "'", $user, $password, "'" . $db . "'", "psql"); //$PHPJasperXML->transferDBtoArray(url,dbuser,dbpassword,db);
            //print_r($PHPJasperXML);
            $PHPJasperXML->outpage("I");
        }
    }

    public function smsCobranca() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $per_ini_envio = $this->request->data["Relatorios"]["per_ini_envio"];
            $per_fim_envio = $this->request->data["Relatorios"]["per_fim_envio"];
            $per_ini_retorno = $this->request->data["Relatorios"]["per_ini_retorno"];
            $per_fim_retorno = $this->request->data["Relatorios"]["per_fim_retorno"];

            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $tipo_sms = $this->request->data["Relatorios"]["tipo_sms"];
            $dados = $this->request->data;

            $clientes = $this->Relatorio->relatorio_retorno($dados);

            $this->set(compact('tipo_arquivo', 'clientes', 'tipo_sms', 'per_ini_envio', 'per_fim_envio', 'per_ini_retorno', 'per_fim_retorno'));
            $this->layout = 'relatorios';
            $this->render("relatorio_sms_cobranca");
        }
        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));

        $this->set(compact('filiais'));
    }

    public function envioSms() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $per_ini_envio = $this->request->data["Relatorios"]["per_ini_envio"];
            $per_fim_envio = $this->request->data["Relatorios"]["per_fim_envio"];
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $tipo_sms = $this->request->data["Relatorios"]["tipo_sms"];
            $dados = $this->request->data;
            $clientes = $this->Relatorio->relatorio_envio($dados);
            $this->loadModel("PrcFilial");
            if (isset($this->request->data['Relatorios']['filial'])) {
                $filiais = $this->PrcFilial->find('all', array('conditions' => array('cd_filial' => $this->request->data['Relatorios']['filial']), 'fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));
            } else {
                $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));
            }
            $nomeFiliais = '';
            foreach ($filiais as $value) {
                $nomeFiliais .= ", " . $value['PrcFilial']['nm_apresentacao_carne'];
            }
            $nomeFiliais = substr($nomeFiliais, 1);

            $this->set(compact('tipo_arquivo', 'clientes', 'tipo_sms', 'per_ini_envio', 'per_fim_envio', 'nomeFiliais'));
            $this->layout = 'relatorios';
            $this->render("relatorio_envio_sms");
        }
        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));

        $this->set(compact('filiais'));
    }

    public function smsValor() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $per_ini_envio = $this->request->data["Relatorios"]["per_ini_envio"];
            $per_fim_envio = $this->request->data["Relatorios"]["per_fim_envio"];
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $tipo_listagem = $this->request->data["Relatorios"]["tipo_listagem"];
            $dados = $this->request->data;


            $this->loadModel("PrcFilial");
            if (isset($this->request->data['Relatorios']['filial'])) {
                $filiais = $this->PrcFilial->find('all', array('conditions' => array('cd_filial' => $this->request->data['Relatorios']['filial']), 'fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));
            } else {
                $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));
            }
            $nomeFiliais = '';
            foreach ($filiais as $value) {
                $nomeFiliais .= ", " . $value['PrcFilial']['nm_apresentacao_carne'];
            }
            $nomeFiliais = substr($nomeFiliais, 1);
            if ($tipo_listagem == 'S') {
                $dadosRelatorio = $this->Relatorio->sms_valor_sintetico($dados);
                $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_envio', 'per_fim_envio'));
                $this->layout = 'relatorios';
                $this->render("relatorio_sms_sintetico");
            } else {
                $dadosRelatorio = $this->Relatorio->sms_valor_analitico($dados);

                $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_envio', 'per_fim_envio'));
                $this->layout = 'relatorios';
                $this->render("relatorio_sms_analitico");
            }
        }
        $campanhas = $this->Relatorio->campanhas();
        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));

        $this->set(compact('filiais', 'campanhas'));
    }

    public function fluxoVendasHora() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $per_ini_vendas = $this->request->data["Relatorios"]["per_ini_vendas"];
            $per_fim_vendas = $this->request->data["Relatorios"]["per_fim_vendas"];
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];

            $dados = $this->request->data;
            $dadosRelatorio = $this->Relatorio->fluxo_vendas_hora($dados);

            $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_vendas', 'per_fim_vendas'));
            if ($tipo_arquivo == 'GRAFICO') {
                $this->layout = 'graficos';
            } else {
                $this->layout = 'relatorios';
            }
            $this->render("relatorio_fluxo_vendas_hora");
        }
        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant')));
        $this->set(compact('filiais'));
    }

    public function acompanhamentoEcommerce() {
        if ($this->request->is('post') || isset($this->params['pass']['0'])) {

            if (!isset($this->params['pass']['0'])) {
                $per_inicio = $this->request->data["Relatorios"]["per_inicio"];
                $per_fim = $this->request->data["Relatorios"]["per_fim"];
                $numEcommerce = $this->request->data['Relatorios']['numero_ecommerce'];
            } else {
                $per_inicio = '';
                $per_fim = '';
                $numEcommerce = '';
            }
            $dados = $this->request->data;

            $qtdPedido = $this->Relatorio->qtd_pedido();

            foreach ($qtdPedido as $key => $value) {
                $statusPedido[$value[0]['status_pedido']] = ($value[0]['count']);
            }

            $dadosRelatorio = $this->Relatorio->acompanhamento_ecommerce($dados, 'normal');
            $dadosRelatorioCancelados = $this->Relatorio->acompanhamento_ecommerce_cancelados($dados);
            $dadosRelatorioBaixa = $this->Relatorio->acompanhamento_ecommerce_baixa($dados);

            $skuPendente = $this->Relatorio->sku_pendente();
            $skuPendenteEstoque = $this->Relatorio->sku_pendente_estoque();
            $skuPendentePreco = $this->Relatorio->sku_pendente_preco();

            $skuPendenteSincronizado = $this->Relatorio->sku_sincronizado();
            $skuPendenteSincronizadoEstoque = $this->Relatorio->sku_sincronizado_estoque();
            $skuPendenteSincronizadoPreco = $this->Relatorio->sku_sincronizado_preco();

            $this->set(compact('dadosRelatorio', 'dadosRelatorioCancelados', 'dadosRelatorioBaixa', 'per_inicio', 'per_fim', 'numEcommerce', 'skuPendente', 'skuPendenteEstoque', 'skuPendentePreco', 'skuPendenteSincronizado', 'skuPendenteSincronizadoEstoque', 'skuPendenteSincronizadoPreco', 'statusPedido'));
            $this->layout = 'graficos';
            $this->render("relatorio_acompanhamento_ecommerce");
        }
    }

    public function acompanhamentoEcommerceAjax() {
        $this->layout = false;
        if ($this->request->is('post')) {

            if (!isset($this->params['pass']['0'])) {
//                $per_inicio = $this->request->data["Relatorios"]["per_inicio"];
//                $per_fim = $this->request->data["Relatorios"]["per_fim"];
//                $numEcommerce = $this->request->data['Relatorios']['numero_ecommerce'];
            } else {
                $per_inicio = '';
                $per_fim = '';
                $numEcommerce = '';
            }
            if (!empty($this->request->data['valor_pedido'])) {
                $this->request->data['valor_pedido'] = $this->Funcionalidades->formatarMoedaBd($this->request->data['valor_pedido']);
            }
            $dados = $this->request->data;

            $dadosRelatorio = $this->Relatorio->acompanhamento_ecommerce($dados, 'ajax');
            $dadosRelatorioCancelados = $this->Relatorio->acompanhamento_ecommerce_cancelados($dados);
            $dadosRelatorioBaixa = $this->Relatorio->acompanhamento_ecommerce_baixa($dados);


            $skuPendente = $this->Relatorio->sku_pendente();
            $skuPendenteEstoque = $this->Relatorio->sku_pendente_estoque();
            $skuPendentePreco = $this->Relatorio->sku_pendente_preco();

            $skuPendenteSincronizado = $this->Relatorio->sku_sincronizado();
            $skuPendenteSincronizadoEstoque = $this->Relatorio->sku_sincronizado_estoque();
            $skuPendenteSincronizadoPreco = $this->Relatorio->sku_sincronizado_preco();

            $this->set(compact('dadosRelatorio', 'dadosRelatorioCancelados', 'dadosRelatorioBaixa', 'per_inicio', 'per_fim', 'numEcommerce', 'skuPendente', 'skuPendenteEstoque', 'skuPendentePreco', 'skuPendenteSincronizado', 'skuPendenteSincronizadoEstoque', 'skuPendenteSincronizadoPreco'));
//            $this->layout = 'graficos';
//            $this->render("relatorio_acompanhamento_ecommerce");
        }
    }

    public function simplificadoQuantidade() {
        set_time_limit(0);
        if ($this->request->is('post')) {

            if (isset($this->request->data['Relatorios']['periodoIni']) && $this->request->data['Relatorios']['periodoIni'] != '') {
                $inicio = $this->Funcionalidades->formatarDataBd($this->request->data['Relatorios']['periodoIni']);
            } else {
                $inicio = '1900-01-01';
            }
            if (isset($this->request->data['Relatorios']['periodoFim']) && $this->request->data['Relatorios']['periodoFim'] != '') {
                $fim = $this->Funcionalidades->formatarDataBd($this->request->data['Relatorios']['periodoFim']);
            } else {
                $fim = date('Y-m-d');
            }
            if (isset($this->request->data['Relatorios']['categoria']) && $this->request->data['Relatorios']['categoria'] != '') {
                $categoria = '"cd_categoria" = "' . $this->request->data['Relatorios']['categoria'] . '"';
            } else {
                $categoria = '';
            }
            if (isset($this->request->data['Relatorios']['familia']) && $this->request->data['Relatorios']['familia'] != '') {
                $idFamilia = explode('-', $this->request->data['Relatorios']['familia']);
                $familia = '"cd_familia" = "' . $idFamilia[1] . '"';
            } else {
                $familia = '';
            }
            if (isset($this->request->data['Relatorios']['departamento']) && $this->request->data['Relatorios']['departamento'] != '') {
                $departamento = '"cd_depto" = "' . $this->request->data['Relatorios']['departamento'] . '"';
            } else {
                $departamento = '';
            }
            if (isset($this->request->data['Relatorios']['grupo']) && $this->request->data['Relatorios']['grupo'] != '') {
                $idGrupo = explode('-', $this->request->data['Relatorios']['grupo']);
                $grupo = '"cd_grupo" = "' . $idGrupo[0] . '"';
            } else {
                $grupo = '';
            }
            if (isset($this->request->data['Relatorios']['fabricante']) && $this->request->data['Relatorios']['fabricante'] != '') {
                $fabricante = '"cd_fabricante" = "' . $this->request->data['Relatorios']['fabricante'] . '"';
            } else {
                $fabricante = '';
            }
            if (isset($this->request->data['Relatorios']['marca']) && $this->request->data['Relatorios']['marca'] != '') {
                $marca = '"cd_marca" = "' . $this->request->data['Relatorios']['marca'] . '"';
            } else {
                $marca = '';
            }
            if (isset($this->request->data['Relatorios']['descricao']) && $this->request->data['Relatorios']['descricao'] != '') {
                $descricao = '"ds_prod_y" LIKE ' . "'%" . mb_strtoupper($this->request->data['Relatorios']['descricao'], 'UTF-8') . "%'";
            } else {
                $descricao = '';
            }


            $this->loadModel("VwRelatorioSimplificado");

            if (isset($this->request->data['Relatorios']['filial'])) {
                $resultado = $this->VwRelatorioSimplificado->find('all', array('conditions' => array('cd_filial' => $this->request->data['Relatorios']['filial'],
                        'OR' => array(
                            array('dt_ult_venda BETWEEN ? and ?' => array($inicio, $fim)),
                            array('dt_ult_entrada BETWEEN ? and ?' => array($inicio, $fim)),
                            array('dt_ult_pedido BETWEEN ? and ?' => array($inicio, $fim)),
                        ),
                        $marca, $categoria, $familia, $departamento, $grupo, $fabricante, $descricao),
                    'order' => $this->request->data['Relatorios']['ordem'],
                    'fields' => array('cd_cpl', 'ds_prod_y', 'ds_tamanho', 'cd_categoria', 'ds_categoria',
                        'cd_familia', 'ds_familia', 'cd_depto', 'ds_depto', 'cd_grupo', 'ds_grupo',
                        'cd_fabricante', 'ds_fabricante', 'cd_marca', 'ds_marca', 'cd_cor', 'ds_cor', 'nome_fornecedor',
                        'cd_ref_fabrica', 'nr_ref_fabrica', 'max(dt_ult_pedido) as dt_ult_pedido', 'max(dt_ult_entrada) as dt_ult_entrada',
                        'max(dt_ult_venda) as dt_ult_venda', 'sum(qtde_pedido) as qtde_pedido', 'sum(qtde_nf) as qtde_nf',
                        'sum(qtde_venda) as qtde_venda', 'sum(qtde_estoque) as qtde_estoque'),
                    'group' => array('cd_cpl', 'ds_prod_y', 'ds_tamanho', 'cd_categoria', 'ds_categoria',
                        'cd_familia', 'ds_familia', 'cd_depto', 'ds_depto', 'cd_grupo', 'ds_grupo',
                        'cd_fabricante', 'ds_fabricante', 'cd_marca', 'ds_marca', 'cd_cor', 'ds_cor', 'nome_fornecedor',
                        'cd_ref_fabrica', 'nr_ref_fabrica', 'dt_ult_pedido', 'dt_ult_entrada',
                        'dt_ult_venda', 'qtde_pedido', 'qtde_nf',
                        'qtde_venda', 'qtde_estoque')
                ));
            } else {
                $resultado = $this->VwRelatorioSimplificado->find('all', array('conditions' => array(
                        'OR' => array(
                            array('dt_ult_venda BETWEEN ? and ?' => array($inicio, $fim)),
                            array('dt_ult_entrada BETWEEN ? and ?' => array($inicio, $fim)),
                            array('dt_ult_pedido BETWEEN ? and ?' => array($inicio, $fim)),
                        ), $marca, $categoria, $familia, $departamento, $grupo, $fabricante, $descricao),
                    'order' => $this->request->data['Relatorios']['ordem'],
                    'fields' => array('cd_cpl', 'ds_prod_y', 'ds_tamanho', 'cd_categoria', 'ds_categoria',
                        'cd_familia', 'ds_familia', 'cd_depto', 'ds_depto', 'cd_grupo', 'ds_grupo',
                        'cd_fabricante', 'ds_fabricante', 'cd_marca', 'ds_marca', 'cd_cor', 'ds_cor', 'nome_fornecedor',
                        'cd_ref_fabrica', 'nr_ref_fabrica', 'max(dt_ult_pedido) as dt_ult_pedido', 'max(dt_ult_entrada) as dt_ult_entrada',
                        'max(dt_ult_venda) as dt_ult_venda', 'sum(qtde_pedido) as qtde_pedido', 'sum(qtde_nf) as qtde_nf',
                        'sum(qtde_venda) as qtde_venda', 'sum(qtde_estoque) as qtde_estoque'),
                    'group' => array('cd_cpl', 'ds_prod_y', 'ds_tamanho', 'cd_categoria', 'ds_categoria',
                        'cd_familia', 'ds_familia', 'cd_depto', 'ds_depto', 'cd_grupo', 'ds_grupo',
                        'cd_fabricante', 'ds_fabricante', 'cd_marca', 'ds_marca', 'cd_cor', 'ds_cor', 'nome_fornecedor',
                        'cd_ref_fabrica', 'nr_ref_fabrica', 'dt_ult_pedido', 'dt_ult_entrada',
                        'dt_ult_venda', 'qtde_pedido', 'qtde_nf',
                        'qtde_venda', 'qtde_estoque')));
            }
            foreach ($resultado as $value) {
                $cpls[$value['VwRelatorioSimplificado']['cd_cpl']] = '';
            }
            $this->loadModel("VwEstProdutoCplTamanhoDsTamanhos");
            if (isset($cpls)) {
                foreach ($cpls as $key => $value) {
                    $resultado2 = $this->VwEstProdutoCplTamanhoDsTamanhos->find('all', array('conditions' => array('cd_cpl' => $key), 'fields' => 'ds_tamanho', 'order' => 'ds_tamanho'));
                    foreach ($resultado2 as $value) {
                        $grade[] = $value['VwEstProdutoCplTamanhoDsTamanhos']['ds_tamanho'];
                    }
                    $tam[$key] = $grade;
                    $grade = '';
                }
            }

            $this->loadModel("PrcFilial");
            if (isset($this->request->data['Relatorios']['filial'])) {
                $filiais = $this->PrcFilial->find('all', array('conditions' => array('cd_filial' => $this->request->data['Relatorios']['filial']), 'fields' => array('cd_emp', 'cd_filial', 'nm_fant')));
            } else {
                $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant')));
            }

            $periodo = $this->Funcionalidades->formatarDataAp($inicio) . " a " . $this->Funcionalidades->formatarDataAp($fim);

            $ordem = $this->request->data['Relatorios']['ordem'];

            $this->set(compact('resultado', 'tam', 'filiais', 'periodo', 'ordem'));
            $this->layout = 'relatorios';
            $this->render('relatorio_simplificado');
        }

        $this->loadModel("EstProdutoCategoria");
        $listaCategoria = $this->EstProdutoCategoria->find('list', array('fields' => array('cd_categoria', 'ds_categoria'), 'order' => 'ds_categoria'));
        foreach ($listaCategoria as $key => $value) {
            $categoria[$key] = strtoupper(utf8_encode($value));
        }
        $this->loadModel("EstProdutoFamilia");
        $listaFamilia = $this->EstProdutoFamilia->find('all', array('fields' => array('cd_categoria', 'cd_familia', 'ds_familia'), 'order' => 'ds_familia'));
        foreach ($listaFamilia as $value) {
            @$familia[$value['EstProdutoFamilia']['cd_categoria'] . '-' . $value['EstProdutoFamilia']['cd_familia']] = strtoupper(utf8_encode($value['EstProdutoFamilia']['ds_familia'])); //$value; 
        }
        @asort($familia);

        $this->loadModel("EstProdutoDepto");
        $listaDepartamento = $this->EstProdutoDepto->find('list', array('fields' => array('cd_depto', 'ds_depto'), 'order' => 'ds_depto'));
        foreach ($listaDepartamento as $key => $value) {
            $departamento[$key] = strtoupper(utf8_encode($value));
        }

        $this->loadModel("EstProdutoGrupo");
        $listaGrupo = $this->EstProdutoGrupo->find('list', array('fields' => array('cd_grupo', 'ds_grupo'), 'order' => 'ds_grupo'));
        foreach ($listaGrupo as $key => $value) {
            $grupo[$key] = strtoupper(utf8_encode($value));
        }
        @asort($grupo);

        $this->loadModel("EstProdutoFabricante");
        $listaFabricante = $this->EstProdutoFabricante->find('list', array('fields' => array('cd_fabricante', 'ds_fabricante'), 'order' => 'ds_fabricante'));
        foreach ($listaFabricante as $key => $value) {
            $fabricante[$key] = strtoupper(utf8_encode($value));
        }

        $this->loadModel("EstProdutoMarca");
        $listaMarca = $this->EstProdutoMarca->find('list', array('fields' => array('cd_marca', 'ds_marca'), 'order' => 'ds_marca'));
        foreach ($listaMarca as $key => $value) {
            $marca[$key] = strtoupper(utf8_encode($value));
        }
        $this->loadModel("PrcFilial");
        $filiais = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_apresentacao_carne')));

        $this->set(compact('filiais', 'categoria', 'familia', 'departamento', 'grupo', 'fabricante', 'marca'));
    }

    public function listar_combo() {
        $this->layout = false;
        if ($this->RequestHandler->isAjax()) {
            if (isset($this->request->data["categoria"])) {
                $this->loadModel("EstProdutoFamilia");
                $this->set('listaFamilia', $this->EstProdutoFamilia->find('all', array('fields' => array('cd_familia', 'cd_categoria', 'ds_familia'), 'conditions' => array('cd_categoria' => $this->request->data["categoria"]), 'recursive' => -1)));
            }
            if (isset($this->request->data["familia"])) {
                $condicoes = explode('-', $this->request->data["familia"]);

                $this->loadModel("EstProdutoGrupo");
                $listaGrupo = $this->EstProdutoGrupo->find('all', array('fields' => array('cd_grupo', 'cd_categoria', 'cd_familia', 'ds_grupo'), 'order' => 'ds_grupo', 'conditions' => array('cd_categoria' => $condicoes[0], 'cd_familia' => $condicoes[1])));
                foreach ($listaGrupo as $value) {
                    $grupo[$value['EstProdutoGrupo']['cd_grupo'] . '-' . $value['EstProdutoGrupo']['cd_categoria'] . '-' . $value['EstProdutoGrupo']['cd_familia']] = $value['EstProdutoGrupo']['ds_grupo']; //$value; 
                }

                $this->set('listaGrupo', $grupo);
            }
        }
    }

    public function detalharPedido() {
        if ($this->request->is('post')) {
            $numero_ecommerce = $this->request->data["numero_ecommerce"];
            $codigo_pedido = $this->request->data["codigo_ecommerce"];

            $dadosRelatorio = $this->Relatorio->detalhe_pedido_ecommerce($numero_ecommerce);

            $this->set(compact('dadosRelatorio', 'numero_ecommerce', 'codigo_pedido'));
            $this->layout = 'historico';
        }
    }

    public function gravarNumero() {
        if ($this->request->is('post')) {
            if ($this->request->data["tipo"] == 'rastreamento') {
                $codigo = strtoupper($this->request->data["codigo"]);
                $codigo_interno = $this->request->data["codigo_interno"];
                $retorno = $this->Relatorio->gravar_tracking_number($codigo, $codigo_interno);
                if ($retorno) {
                    echo 'Pedido com status que não permite gravação de rastreamento!';
                } else {
                    echo 'Código gravado com sucesso!';
                }
            } else {
                $codigo_interno = $this->request->data["codigo_interno"];
                $anti_fraude = $this->request->data["anti_fraude"];
                $retorno = $this->Relatorio->gravar_anti_fraude($anti_fraude, $codigo_interno);
                if ($retorno) {
                    echo 'Erro ao gravar código anti-fraude!';
                } else {
                    echo 'Código anti-fraude gravado com sucesso!';
                }
            }
            $this->autoRender = false;
        }
    }

    public function atendimentos() {
        set_time_limit(0);
        if ($this->request->is('post')) {

            $this->loadModel("GlbQuestionarioResposta");

            $tipo_arquivo = $this->request->data['Relatorios']['tipo_arquivo'];

            $data_in = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $data_fim = $this->request->data['Relatorios']['per_fim_pesquisas'];

            if ($this->request->data['Relatorios']['per_ini_pesquisas'] == '') {
                $per_ini_pesquisas = '1900-01-01';
            } else {
                $per_ini_pesquisas = $this->Funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_pesquisas']);
            }
            if ($this->request->data['Relatorios']['per_fim_pesquisas'] == '') {
                $per_fim_pesquisas = date("Y-m-d");
            } else {

                $per_fim_pesquisas = $this->Funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_pesquisas']);
            }
            if (isset($this->request->data['Relatorios']['pesquisas'])) {
                $atendimentos = $this->GlbQuestionarioResposta->find("all", array('conditions' => array('GlbQuestionarioResposta.cd_questionario' => $this->request->data['Relatorios']['pesquisas'], 'GlbQuestionarioResposta.dt_cad BETWEEN ? AND ?' => array($per_ini_pesquisas, $per_fim_pesquisas)),
                    'fields' => array("GlbQuestionarioResposta.*", 'Usuario.nm_usu', 'Pesquisa.ds_questionario', 'Cliente.nm_pessoa'),
                    "joins" => array(
                        array(
                            "table" => "vw_segu_usu",
                            "alias" => "Usuario",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_usu_cad = Usuario.cd_usu")
                        ),
                        array(
                            "table" => "glb_pessoa",
                            "alias" => "Cliente",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_pessoa = Cliente.cd_pessoa")
                        ),
                        array(
                            "table" => "glb_questionario",
                            "alias" => "Pesquisa",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_questionario = Pesquisa.cd_questionario")
                        )), 'order' => array('GlbQuestionarioResposta.cd_questionario', 'GlbQuestionarioResposta.dt_cad', 'status_atendimento')
                        )
                );
            } else {
                $atendimentos = $this->GlbQuestionarioResposta->find("all", array('conditions' => array('GlbQuestionarioResposta.dt_cad BETWEEN ? AND ?' => array($per_ini_pesquisas, $per_fim_pesquisas)), 'fields' => array("GlbQuestionarioResposta.*", 'Usuario.nm_usu', 'Pesquisa.ds_questionario', 'Cliente.nm_pessoa'),
                    "joins" => array(
                        array(
                            "table" => "vw_segu_usu",
                            "alias" => "Usuario",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_usu_cad = Usuario.cd_usu")
                        ),
                        array(
                            "table" => "glb_pessoa",
                            "alias" => "Cliente",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_pessoa = Cliente.cd_pessoa")
                        ),
                        array(
                            "table" => "glb_questionario",
                            "alias" => "Pesquisa",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_questionario = Pesquisa.cd_questionario")
                        )), 'order' => array('GlbQuestionarioResposta.cd_questionario', 'GlbQuestionarioResposta.dt_cad', 'status_atendimento'))
                );
            }
            if ($tipo_arquivo == 'GRAFICO') {

                $respostas = $this->Relatorio->atendimento_por_pergunta($per_ini_pesquisas, $per_fim_pesquisas, $this->request->data['Relatorios']['pesquisas'][0]);
                foreach ($respostas as $value) {
                    $respostasGrafico[$value[0]['cd_pergunta']][$value[0]['ds_pergunta']][$value[0]['ds_pergunta_cpl']] = $value[0]['qtde_resposta'];
                }

                $this->layout = 'graficos';
            } else {
                $this->layout = 'relatorios';
            }

            $this->set(compact('atendimentos', 'tipo_arquivo', 'data_in', 'data_fim', 'respostasGrafico'));
            $this->render("relatorio_atendimentos");
        }
        $this->loadModel("GlbQuestionario");
        $pesquisas = $this->GlbQuestionario->find('all');
        $this->set(compact('pesquisas'));
    }

    public function sugestoes() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $this->loadModel("GlbQuestionarioResposta");
            $tipo_arquivo = $this->request->data['Relatorios']['tipo_arquivo'];

            $data_in = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $data_fim = $this->request->data['Relatorios']['per_fim_pesquisas'];

            if ($this->request->data['Relatorios']['per_ini_pesquisas'] == '') {
                $per_ini_pesquisas = '1900-01-01';
            } else {
                $per_ini_pesquisas = $this->Funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_ini_pesquisas']);
            }
            if ($this->request->data['Relatorios']['per_fim_pesquisas'] == '') {
                $per_fim_pesquisas = date("Y-m-d");
            } else {

                $per_fim_pesquisas = $this->Funcionalidades->formatarDataBd($this->request->data['Relatorios']['per_fim_pesquisas']);
            }
            if (isset($this->request->data['Relatorios']['pesquisas'])) {
                $atendimentos = $this->GlbQuestionarioResposta->find("all", array('conditions' => array('GlbQuestionarioResposta.cd_questionario' => $this->request->data['Relatorios']['pesquisas'], 'GlbQuestionarioResposta.dt_cad BETWEEN ? AND ?' => array($per_ini_pesquisas, $per_fim_pesquisas), 'Pergunta.tp_pergunta' => 3), 'fields' => array("GlbQuestionarioResposta.*", "RespostaCpl.ds_resposta", "Pergunta.tp_pergunta", "Cliente.nm_pessoa", "Pesquisa.ds_questionario"),
                    "joins" => array(
                        array(
                            "table" => "glb_questionario_resposta_cpl",
                            "alias" => "RespostaCpl",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_resposta = RespostaCpl.cd_resposta")
                        ),
                        array(
                            "table" => "glb_questionario_pergunta",
                            "alias" => "Pergunta",
                            "type" => "INNER",
                            "conditions" => array("Pergunta.cd_pergunta = RespostaCpl.cd_pergunta")
                        ),
                        array(
                            "table" => "glb_pessoa",
                            "alias" => "Cliente",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_pessoa = Cliente.cd_pessoa")
                        ),
                        array(
                            "table" => "glb_questionario",
                            "alias" => "Pesquisa",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_questionario = Pesquisa.cd_questionario")
                        )), 'order' => array('GlbQuestionarioResposta.cd_questionario', 'GlbQuestionarioResposta.dt_cad', 'status_atendimento'))
                );
            } else {
                $atendimentos = $this->GlbQuestionarioResposta->find("all", array('conditions' => array('GlbQuestionarioResposta.dt_cad BETWEEN ? AND ?' => array($per_ini_pesquisas, $per_fim_pesquisas), 'Pergunta.tp_pergunta' => 3), 'fields' => array("GlbQuestionarioResposta.*", "RespostaCpl.ds_resposta", "Pergunta.tp_pergunta", "Cliente.nm_pessoa", "Pesquisa.ds_questionario"),
                    "joins" => array(
                        array(
                            "table" => "glb_questionario_resposta_cpl",
                            "alias" => "RespostaCpl",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_resposta = RespostaCpl.cd_resposta")
                        ),
                        array(
                            "table" => "glb_questionario_pergunta",
                            "alias" => "Pergunta",
                            "type" => "INNER",
                            "conditions" => array("Pergunta.cd_pergunta = RespostaCpl.cd_pergunta")
                        ),
                        array(
                            "table" => "glb_pessoa",
                            "alias" => "Cliente",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_pessoa = Cliente.cd_pessoa")
                        ),
                        array(
                            "table" => "glb_questionario",
                            "alias" => "Pesquisa",
                            "type" => "INNER",
                            "conditions" => array("GlbQuestionarioResposta.cd_questionario = Pesquisa.cd_questionario")
                        )), 'order' => array('GlbQuestionarioResposta.cd_questionario', 'GlbQuestionarioResposta.dt_cad', 'status_atendimento'))
                );
            }

            $this->layout = 'relatorios';

            $this->set(compact('atendimentos', 'tipo_arquivo', 'data_in', 'data_fim', 'respostasGrafico'));
            $this->render("relatorio_sugestoes");
        }
        $this->loadModel("GlbQuestionario");
        $pesquisas = $this->GlbQuestionario->find('all');
        $this->set(compact('pesquisas'));
    }

    public function index() {
        $this->render('relatorios');
    }

    public function pedidoOnline() {
        if ($this->request->is('post')) {
            var_dump($this->request);
            die();
        }
        $this->layout = 'graficos';
    }

    public function inadimplencia() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $per_ini_pesquisas = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $per_fim_pesquisas = $this->request->data['Relatorios']['per_fim_pesquisas'];
            $dados = $this->request->data;
            $dadosRelatorio = $this->Relatorio->inadimplencia($dados);
            $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
            $this->layout = 'relatorios';
            $this->render("relatorio_inadimplencia");
        }
        $this->loadModel("PrcRegiaoFilial");
        $listaRegiao = $this->PrcRegiaoFilial->find('all', array('fields' => array('cd_regiao', 'ds_regiao')));
        foreach ($listaRegiao as $key => $value) {
            $regiao[$value['PrcRegiaoFilial']['cd_regiao']] = strtoupper(utf8_encode($value['PrcRegiaoFilial']['ds_regiao']));
        }
        $this->loadModel("PrcFilial");
        $listaFilial = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'order' => 'nm_fant'));
        foreach ($listaFilial as $key => $value) {
            $filial[$value['PrcFilial']['cd_filial']] = $value['PrcFilial']['nm_fant'];
        }
        $this->loadModel("GlbCargo");
        $listaCargo = $this->GlbCargo->find('all', array('fields' => array('cd_cargo', 'ds_cargo'), 'order' => 'ds_cargo'));
        foreach ($listaCargo as $key => $value) {
            $cargo[$value['GlbCargo']['cd_cargo']] = $value['GlbCargo']['ds_cargo'];
        }
        $this->set(compact('regiao'));
        $this->set(compact('filial'));
        $this->set(compact('cargo'));
    }

    function listar_filiais() {
        $regioes = substr($this->request->data['regioes'], 1);
        $regiao = explode(',', $regioes);
        $this->loadModel("PrcRegiaoFilial");
        $filiais = $this->PrcRegiaoFilial->find("all", array('conditions' => array('PrcRegiaoFilial.cd_regiao' => $regiao), 'fields' => array('filial.cd_filial', 'filial.nm_fant'), 'order' => 'filial.nm_fant',
            "joins" => array(
                array(
                    "table" => "prc_regiao_filial_prc_filial",
                    "alias" => "regiaoFilial",
                    "type" => "INNER",
                    "conditions" => array("PrcRegiaoFilial.cd_regiao = regiaoFilial.cd_regiao")),
                array(
                    "table" => "prc_filial",
                    "alias" => "filial",
                    "type" => "INNER",
                    "conditions" => array("regiaoFilial.cd_filial = filial.cd_filial")
        ))));
        $this->set(compact('filiais'));
    }

    function descricaoAtendimento() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $per_ini_pesquisas = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $per_fim_pesquisas = $this->request->data['Relatorios']['per_fim_pesquisas'];
            $dados = $this->request->data;
            $dadosRelatorio = $this->Relatorio->descricao_atendimento($dados);
            $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
            $this->layout = 'relatorios';
            $this->render("relatorio_descricao_atendimento");
        }
        $this->loadModel("GlbQuestionario");
        $listaPesquisa = $this->GlbQuestionario->find('all', array('fields' => array('cd_questionario', 'ds_questionario'), 'order' => 'ds_questionario'));
        foreach ($listaPesquisa as $key => $value) {
            $pesquisa[$value['GlbQuestionario']['cd_questionario']] = $value['GlbQuestionario']['ds_questionario'];
        }
        $this->set(compact('pesquisa'));
    }

    function acompanhamentoTempoCrediario() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $per_ini_pesquisas = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $per_fim_pesquisas = $this->request->data['Relatorios']['per_fim_pesquisas'];
            $dados = $this->request->data;
            if ($tipo_arquivo == 'GRAFICO') {
                $dadosRelatorio = $this->Relatorio->acompanhamento_tempo_crediario_grafico($dados);
                $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
                $this->layout = 'relatorios';
                $this->render("relatorio_acompanhamento_tempo_crediario");
            } else if ($tipo_arquivo == 'PDF') {
                $dadosRelatorio = $this->Relatorio->acompanhamento_tempo_crediario($dados);
                $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
                $this->layout = 'relatorios';
                $this->render("relatorio_acompanhamento_tempo_crediario");
            } else if ($tipo_arquivo == 'EXCEL') {
                $dadosRelatorio = $this->Relatorio->acompanhamento_tempo_crediario($dados);
                $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
                $this->layout = 'relatorios';
                $this->render("relatorio_acompanhamento_tempo_crediario");
            }
        }
        $this->loadModel("PrcFilial");
        $listaFilial = $this->PrcFilial->find('all', array('fields' => array('cd_emp', 'cd_filial', 'nm_fant'), 'order' => 'nm_fant'));
        foreach ($listaFilial as $key => $value) {
            $filial[$value['PrcFilial']['cd_filial']] = $value['PrcFilial']['nm_fant'];
        }
        $this->set(compact('filial'));
    }

    function atendimentoAniversariante() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $per_ini_pesquisas = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $per_fim_pesquisas = $this->request->data['Relatorios']['per_fim_pesquisas'];
            $dados = $this->request->data;
            $dadosRelatorio = $this->Relatorio->atendimento_aniversariante($dados);
            $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
            $this->layout = 'relatorios';
            $this->render("relatorio_atendimento_aniversariante");
        }
        $this->loadModel("GlbQuestionario");
        $listaPesquisa = $this->GlbQuestionario->find('all', array('fields' => array('cd_questionario', 'ds_questionario'), 'conditions' => array('tipo_questionario' => $this->request->data["GlbQuestionarioParametro"]["tipo_questionario"] = 1), 'order' => 'ds_questionario'));
        foreach ($listaPesquisa as $key => $value) {
            $pesquisa[$value['GlbQuestionario']['cd_questionario']] = $value['GlbQuestionario']['ds_questionario'];
        }
        $this->set(compact('pesquisa'));
    }

    function respostaPesquisa() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $per_ini_pesquisas = $this->request->data['Relatorios']['per_ini_pesquisas'];
            $per_fim_pesquisas = $this->request->data['Relatorios']['per_fim_pesquisas'];
            $dados = $this->request->data;
            $dadosRelatorio = $this->Relatorio->resposta_pesquisa($dados);
            $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_pesquisas', 'per_fim_pesquisas'));
            $this->layout = 'relatorios';
            $this->render("relatorio_resposta_pesquisa");
        }
        $this->loadModel("GlbQuestionario");
        $listaPesquisa = $this->GlbQuestionario->find('all', array('fields' => array('cd_questionario', 'ds_questionario'), 'order' => 'ds_questionario'));
        foreach ($listaPesquisa as $key => $value) {
            $pesquisa[$value['GlbQuestionario']['cd_questionario']] = $value['GlbQuestionario']['ds_questionario'];
        }
        $this->set(compact('pesquisa'));
    }

    function retornoContatoPesquisa() {
        set_time_limit(0);
        if ($this->request->is('post')) {
            $tipo_arquivo = $this->request->data["Relatorios"]["tipo_arquivo"];
            $per_ini_envio = $this->request->data["Relatorios"]["per_ini_envio"];
            $per_fim_envio = $this->request->data["Relatorios"]["per_fim_envio"];
            $per_ini_retorno = $this->request->data["Relatorios"]["per_ini_retorno"];
            $per_fim_retorno = $this->request->data["Relatorios"]["per_fim_retorno"];
            $dados = $this->request->data;
            $dadosRelatorio = $this->Relatorio->retorno_contato_pesquisa($dados);
            $this->set(compact('tipo_arquivo', 'dadosRelatorio', 'per_ini_envio', 'per_fim_envio', 'per_ini_retorno', 'per_fim_retorno'));
            $this->layout = 'relatorios';
            $this->render("relatorio_retorno_contato_pesquisa");
        }
        $this->loadModel("GlbQuestionario");
        $listaPesquisa = $this->GlbQuestionario->find('all', array('fields' => array('cd_questionario', 'ds_questionario'), 'order' => 'ds_questionario'));
        foreach ($listaPesquisa as $key => $value) {
            $pesquisa[$value['GlbQuestionario']['cd_questionario']] = $value['GlbQuestionario']['ds_questionario'];
        }
        $this->set(compact('pesquisa'));
    }

    /**
     * Relatório de Estoque Detalhado por Família/Grupo
     * Exibe estoque com custo, quantidade, SKUs e percentuais
     */
    public function estoque_detalhado() {
        if (!$this->Session->check('Config.databasename')) {
            $this->Session->setFlash(__('Primeiro selecione a empresa desejada!'));
            $this->redirect(array('controller' => 'Relatorios', 'action' => 'empresa'));
        }

        // Verifica permissão (usando permissão de relatórios)
        if (!in_array('Relatórios', $this->Session->read('Questionarios.permissoes'))) {
            $this->Session->setFlash(__('Esta página não existe!'));
            echo ("<script language=\"javascript\">setTimeout(function(){window.location.assign('/SysApp/app/webroot/index.php/Relatorios/');},0000);</script>");
        }

        $funcionalidades = new FuncionalidadesComponent(new ComponentCollection());

        // Configurar acesso por loja
        $this->loadModel("ConfigUserSysApp");
        $cd_usuario = $this->Session->read('Questionarios.cd_usu');
        $retornoConsulta = $this->ConfigUserSysApp->retornaCodigoUsuErp($cd_usuario);
        $cd_usu_erp = $retornoConsulta[0][0]['cd_usu_erp'];

        // Buscar filiais disponíveis para o usuário
        $this->loadModel("PrcFilial");
        $this->PrcFilial->setDataSource($_SESSION['Config']['database']);
        
        $filiais = $this->PrcFilial->find("all", array(
            'fields' => array('PrcFilial.cd_emp', 'PrcFilial.cd_filial', 'PrcFilial.nm_fant'),
            'conditions' => array('usuario.cd_usu = ' . $cd_usu_erp, 'PrcFilial.sts_filial = 1'),
            'order' => 'PrcFilial.nm_fant',
            "joins" => array(
                array(
                    "table" => "segu_usu_filial",
                    "alias" => "usuario",
                    "type" => "INNER",
                    "conditions" => array("PrcFilial.cd_filial = usuario.cd_filial")
                )
            )
        ));

        $this->set(compact('filiais'));

        // Processar formulário
        if ($this->request->is("POST")) {
            set_time_limit(0); // Remove timeout para relatórios grandes

            // Data de referência do estoque
            if (empty($this->request->data['dt_referencia'])) {
                $param_dt_referencia = date('Y-m-d');
                $data_formatada = date('d/m/Y');
            } else {
                $param_dt_referencia = $funcionalidades->formatarDataBd($this->request->data['dt_referencia']);
                $data_formatada = $funcionalidades->formatarDataAp($this->request->data['dt_referencia']);
            }

            // Filiais selecionadas
            $cod_filiais = '';
            if (isset($this->request->data['Relatorios']['filial'])) {
                foreach ($this->request->data['Relatorios']['filial'] as $value) {
                    $cod_filiais .= "," . $value;
                }
                $param_cd_filial = substr($cod_filiais, 1);
            } else {
                // Se nenhuma filial foi selecionada, pegar todas
                $param_cd_filial = '';
                foreach ($filiais as $filial) {
                    $param_cd_filial .= "," . $filial['PrcFilial']['cd_filial'];
                }
                $param_cd_filial = substr($param_cd_filial, 1);
            }

            // Tipo de agrupamento (Família ou Grupo)
            $tipo_agrupamento = isset($this->request->data['tipo_agrupamento']) ? $this->request->data['tipo_agrupamento'] : 'FAMILIA';

            // Ordenação
            $ordenacao = isset($this->request->data['ordenacao']) ? $this->request->data['ordenacao'] : 'VALOR_DESC';

            // Filtro de estoque zerado
            $exibir_estoque_zerado = isset($this->request->data['exibir_estoque_zerado']) ? true : false;

            // Tipo de arquivo (HTML ou EXCEL)
            $tipo_arquivo = isset($this->request->data['Relatorios']['tipo_arquivo']) ? $this->request->data['Relatorios']['tipo_arquivo'] : 'HTML';

            // Parâmetros para o model
            $parametros = array(
                'dt_referencia' => $param_dt_referencia,
                'cd_filial' => $param_cd_filial,
                'tipo_agrupamento' => $tipo_agrupamento,
                'ordenacao' => $ordenacao,
                'exibir_estoque_zerado' => $exibir_estoque_zerado
            );

            // Buscar dados do relatório
            $dadosRelatorio = $this->Relatorio->estoque_detalhado($parametros);

            // Calcular totais gerais
            $total_custo = 0;
            $total_qtde = 0;
            $total_skus = 0;

            if ($dadosRelatorio) {
                foreach ($dadosRelatorio as $linha) {
                    $total_custo += floatval($linha[0]['custo_total']);
                    $total_qtde += floatval($linha[0]['qtde_total']);
                    $total_skus += intval($linha[0]['total_skus']);
                }
            }

            // Preparar dados para a view
            $this->set(compact(
                'dadosRelatorio',
                'data_formatada',
                'tipo_agrupamento',
                'ordenacao',
                'tipo_arquivo',
                'total_custo',
                'total_qtde',
                'total_skus',
                'exibir_estoque_zerado'
            ));

            $this->layout = 'relatorios';
            $this->render("relatorio_estoque_detalhado");
        }
    }

}
