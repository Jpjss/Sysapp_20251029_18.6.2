<?php
App::uses('AppController', 'Controller');

class AdminController extends AppController {

    public $uses = array(); // Não precisamos de um model específico por enquanto
    public $components = array('RequestHandler');

    public function admin_create_database() {
        $this->autoRender = false; // Desabilitamos a renderização de view, pois é uma API

        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }

        $dbName = $this->request->data['dbName'];

        // --- VALIDAÇÃO BÁSICA ---
        // Remove caracteres que não sejam alfanuméricos ou underscore
        $dbName = preg_replace('/[^a-zA-Z0-9_]/', '', $dbName);

        if (empty($dbName)) {
            $this->response->statusCode(400);
            return json_encode(array('error' => 'O nome do banco de dados não pode ser vazio ou conter caracteres inválidos.'));
        }

        try {
            // --- AVISO DE SEGURANÇA ---
            // A conexão de banco de dados padrão ('default') PRECISA ter privilégios de CREATE DATABASE.
            // Isso NÃO é recomendado para uma aplicação web em produção.
            // O ideal seria usar credenciais separadas e mais restritas para as operações do dia-a-dia
            // e usar credenciais com mais privilégios apenas para esta operação específica.
            
            $db = ConnectionManager::getDataSource('default');
            
            // Usamos query() para executar um comando SQL diretamente.
            // É CRUCIAL garantir que $dbName está sanitizado para evitar SQL Injection,
            // embora o risco seja menor em um comando DDL como este.
            $db->query("CREATE DATABASE `" . $dbName . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            // Opcional: Futuramente, podemos adicionar aqui a lógica para popular o novo banco de dados
            // com as tabelas do arquivo 'database_schema.sql'.

            return json_encode(array('success' => 'Banco de dados "' . $dbName . '" criado com sucesso.'));

        } catch (Exception $e) {
            // Captura exceções do banco de dados (ex: banco já existe, falta de permissão)
            $this->response->statusCode(500);
            return json_encode(array('error' => 'Falha ao criar o banco de dados: ' . $e->getMessage()));
        }
    }
}
