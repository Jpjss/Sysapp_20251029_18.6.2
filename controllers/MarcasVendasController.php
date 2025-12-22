<?php
/**
 * Controller para Dashboard de Marcas mais Vendidas
 */

require_once BASE_PATH . '/core/Controller.php';

class MarcasVendasController extends Controller {
    
    /**
     * Exibe o dashboard de marcas mais vendidas
     */
    public function dashboard() {
        // Verificar autenticação usando o método padrão
        $this->requireAuth();
        
        // Verificar se tem empresa configurada
        if (!Session::check('Config.database')) {
            Session::setFlash('Por favor, selecione uma empresa antes de acessar os relatórios.', 'info');
            $this->redirect('relatorios/empresa');
            return;
        }
        
        // Renderizar view
        $this->render();
    }
}
