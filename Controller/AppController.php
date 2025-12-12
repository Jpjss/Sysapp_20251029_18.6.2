<?php

/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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
App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $components = array('Session');

    public function beforeFilter() {

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
                            //ConnectionManager::create($value['nome_banco'], $settings);
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
                    //ConnectionManager::create($this->Session->read("Config.database"), $settings);
                } catch (Exception $e) {
                    $retorno = $e;
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
                //ConnectionManager::create($this->Session->read("Config.database"), $settings);
            } catch (Exception $e) {
                $retorno = $e;
            }
        }
    }

    public function afterFilter() {
        if ($this->Session->check('Questionarios.nm_usu')) {
            if ($this->Session->check('Questionarios.nm_usu')) {

                $usu_escolher_db = $this->Session->read('Questionarios');
                $usuario = $usu_escolher_db ['cd_usu'];
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

                $infoDb = $this->ConfigUserEmpresaInterface->buscaInfoDb($usuario, $cd_empresa);


                if (isset($infoDb['1'])) {
                    //$this->redirect(array('controller' => 'Relatorios'));


                    foreach ($infoDb as $key) {
                        foreach ($key as $value) {
                            $hostname = $value['hostname_banco'];
                            $nome_empresa = $value['nome_empresa'];
                            $nome_banco = $value['nome_banco'];
                            $usuario_banco = $value['usuario_banco'];
                            $senha_banco = $value['senha_banco'];
                            $porta_banco = $value['porta_banco'];
                        }
                    }

                    //$this->Session->write('Config.database',$nome_banco);
                    //$this->Session->write('Config.databasename',$nome_banco);
                    //$this->Session->write('Config.host',$hostname);
                    //$this->Session->write('Config.user',$usuario_banco);
                    //$this->Session->write('Config.password',$senha_banco);
                    //$this->Session->write('Config.porta', $porta_banco); 
                }
            }
        }
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

}
