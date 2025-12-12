<?php
/**
 * Controller para Correção de XMLs de NFe
 */

// Carrega configurações otimizadas para XML
require_once BASE_PATH . '/config/xml_config.php';

class XmlController extends Controller {
    
    public function __construct() {
        parent::__construct();
        $this->layout = 'default';
    }
    
    /**
     * Página principal de correção de XMLs
     */
    public function index() {
        $this->pageTitle = 'Correção de XMLs NFe';
        $this->render();
    }
    
    /**
     * Upload e processamento de XMLs
     */
    public function processar() {
        // Aumenta limites para processar grandes volumes
        ini_set('max_execution_time', 600); // 10 minutos
        ini_set('memory_limit', '512M'); // 512MB de memória
        
        if (!$this->isPost()) {
            $this->redirect('xml/index');
            return;
        }
        
        $response = [
            'success' => false,
            'message' => '',
            'logs' => [],
            'stats' => [
                'total' => 0,
                'corrigidos' => 0,
                'sem_divergencia' => 0,
                'erros' => 0
            ]
        ];
        
        try {
            if (!isset($_FILES['xmls']) || empty($_FILES['xmls']['name'][0])) {
                throw new Exception('Nenhum arquivo foi enviado.');
            }
            
            $files = $_FILES['xmls'];
            $total = count($files['name']);
            $response['stats']['total'] = $total;
            
            // Diretório para salvamento dos XMLs corrigidos
            $uploadDir = 'C:/systec/xmls corrigidos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Limpa arquivos antigos (mais de 1 hora)
            $this->limparArquivosAntigos($uploadDir, 3600);
            
            // Processa em lotes para economizar memória
            $loteTamanho = 100; // Processa 100 arquivos por vez
            $logsDetalhados = []; // Apenas primeiros e últimos logs para economizar memória
            
            for ($lote = 0; $lote < ceil($total / $loteTamanho); $lote++) {
                $inicio = $lote * $loteTamanho;
                $fim = min($inicio + $loteTamanho, $total);
                
                // Processa cada arquivo do lote
                for ($i = $inicio; $i < $fim; $i++) {
                // Processa cada arquivo do lote
                for ($i = $inicio; $i < $fim; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    $log = [
                        'tipo' => 'erro',
                        'arquivo' => $files['name'][$i],
                        'mensagem' => 'Erro no upload do arquivo'
                    ];
                    // Só guarda logs se for dos primeiros 50 ou últimos 50
                    if ($i < 50 || $i >= $total - 50) {
                        $logsDetalhados[] = $log;
                    }
                    $response['stats']['erros']++;
                    continue;
                }
                
                $nomeArquivo = $files['name'][$i];
                $tmpName = $files['tmp_name'][$i];
                
                // Valida extensão
                if (strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION)) !== 'xml') {
                    $log = [
                        'tipo' => 'erro',
                        'arquivo' => $nomeArquivo,
                        'mensagem' => 'Arquivo não é XML'
                    ];
                    if ($i < 50 || $i >= $total - 50) {
                        $logsDetalhados[] = $log;
                    }
                    $response['stats']['erros']++;
                    continue;
                }
                
                // Processa o XML
                $resultado = $this->corrigirXml($tmpName, $nomeArquivo);
                
                if ($resultado['success']) {
                    // Salva o arquivo corrigido
                    $caminhoDestino = $uploadDir . $nomeArquivo;
                    file_put_contents($caminhoDestino, $resultado['xml']);
                    
                    if ($resultado['corrigido']) {
                        $response['stats']['corrigidos']++;
                        $log = [
                            'tipo' => 'sucesso',
                            'arquivo' => $nomeArquivo,
                            'mensagem' => sprintf('Corrigido: diferença ajustada R$ %.2f', $resultado['diferenca'])
                        ];
                        if ($i < 50 || $i >= $total - 50) {
                            $logsDetalhados[] = $log;
                        }
                    } else {
                        $response['stats']['sem_divergencia']++;
                        $log = [
                            'tipo' => 'info',
                            'arquivo' => $nomeArquivo,
                            'mensagem' => 'Sem divergência'
                        ];
                        if ($i < 50 || $i >= $total - 50) {
                            $logsDetalhados[] = $log;
                        }
                    }
                } else {
                    $response['stats']['erros']++;
                    $log = [
                        'tipo' => 'erro',
                        'arquivo' => $nomeArquivo,
                        'mensagem' => $resultado['erro']
                    ];
                    if ($i < 50 || $i >= $total - 50) {
                        $logsDetalhados[] = $log;
                    }
                }
                
                // Libera memória a cada 50 arquivos
                if ($i % 50 == 0) {
                    gc_collect_cycles();
                }
            }
            
            // Após processar o lote, libera memória
            gc_collect_cycles();
            }
            
            // Após processar o lote, libera memória
            gc_collect_cycles();
            }
            
            // Se houver muitos logs, adiciona mensagem resumida
            if ($total > 100) {
                $response['logs'] = array_merge(
                    [['tipo' => 'info', 'arquivo' => '', 'mensagem' => "Mostrando primeiros e últimos 50 logs de $total arquivos"]],
                    $logsDetalhados
                );
            } else {
                $response['logs'] = $logsDetalhados;
            }
            
            $response['success'] = true;
            $response['message'] = sprintf(
                'Processamento concluído: %d arquivos processados, %d corrigidos, %d sem divergência, %d erros',
                $total,
                $response['stats']['corrigidos'],
                $response['stats']['sem_divergencia'],
                $response['stats']['erros']
            );
            
        } catch (Exception $e) {
            $response['message'] = 'Erro: ' . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * Corrige um arquivo XML de NFe
     */
    private function corrigirXml($caminhoArquivo, $nomeArquivo) {
        $resultado = [
            'success' => false,
            'corrigido' => false,
            'diferenca' => 0,
            'xml' => null,
            'erro' => null
        ];
        
        try {
            // Carrega o XML com opções otimizadas
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($caminhoArquivo, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_NOBLANKS);
            
            if ($xml === false) {
                $erros = libxml_get_errors();
                throw new Exception('XML inválido: ' . $erros[0]->message);
            }
            
            // Registra namespace
            $xml->registerXPathNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');
            
            // Busca os itens (det)
            $itens = $xml->xpath('//nfe:det');
            
            if (empty($itens)) {
                throw new Exception('Nenhum item encontrado no XML');
            }
            
            // Calcula totais
            $totalLiquido = 0;
            $somaDesc = 0;
            $temDesconto = false;
            
            foreach ($itens as $item) {
                $item->registerXPathNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');
                
                $vProd = (float) $item->xpath('.//nfe:vProd')[0];
                $vDescNodes = $item->xpath('.//nfe:vDesc');
                $vDesc = !empty($vDescNodes) ? (float) $vDescNodes[0] : 0;
                
                $somaDesc += $vDesc;
                $totalLiquido += ($vProd - $vDesc);
                
                if ($vDesc > 0) {
                    $temDesconto = true;
                }
            }
            
            // Busca valores totais
            $vNF = (float) $xml->xpath('//nfe:ICMSTot/nfe:vNF')[0];
            $vDescTotal = (float) $xml->xpath('//nfe:ICMSTot/nfe:vDesc')[0];
            $vProdTotal = (float) $xml->xpath('//nfe:ICMSTot/nfe:vProd')[0];
            
            // Calcula diferença
            $diferenca = round($vNF - $totalLiquido, 2);
            
            if ($diferenca != 0) {
                // Pega o último item
                $ultimoItem = $itens[count($itens) - 1];
                $ultimoItem->registerXPathNamespace('nfe', 'http://www.portalfiscal.inf.br/nfe');
                
                if ($temDesconto) {
                    // Ajusta no desconto
                    $vDescAtual = (float) $ultimoItem->xpath('.//nfe:vDesc')[0];
                    $ultimoItem->xpath('.//nfe:vDesc')[0][0] = number_format($vDescAtual - $diferenca, 2, '.', '');
                    
                    $xml->xpath('//nfe:ICMSTot/nfe:vProd')[0][0] = number_format($vNF + ($vDescTotal - $diferenca), 2, '.', '');
                    $xml->xpath('//nfe:ICMSTot/nfe:vDesc')[0][0] = number_format($somaDesc - $diferenca, 2, '.', '');
                } else {
                    // Ajusta nos valores unitários
                    if ($vNF < $totalLiquido) {
                        $diferenca *= -1;
                    }
                    
                    $vUnCom = (float) $ultimoItem->xpath('.//nfe:vUnCom')[0];
                    $vProdItem = (float) $ultimoItem->xpath('.//nfe:vProd')[0];
                    $vUnTrib = (float) $ultimoItem->xpath('.//nfe:vUnTrib')[0];
                    
                    $ultimoItem->xpath('.//nfe:vUnCom')[0][0] = number_format($vUnCom - $diferenca, 2, '.', '');
                    $ultimoItem->xpath('.//nfe:vProd')[0][0] = number_format($vProdItem - $diferenca, 2, '.', '');
                    $ultimoItem->xpath('.//nfe:vUnTrib')[0][0] = number_format($vUnTrib - $diferenca, 2, '.', '');
                    
                    if ($vNF > $totalLiquido) {
                        $xml->xpath('//nfe:ICMSTot/nfe:vProd')[0][0] = number_format($vProdTotal - $diferenca, 2, '.', '');
                        $xml->xpath('//nfe:ICMSTot/nfe:vNF')[0][0] = number_format($vNF - $diferenca, 2, '.', '');
                    }
                }
                
                $resultado['corrigido'] = true;
                $resultado['diferenca'] = abs($diferenca);
            }
            
            // Gera XML corrigido
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = false; // Desabilita formatação para economia de memória
            $resultado['xml'] = $dom->saveXML();
            $resultado['success'] = true;
            
            // Libera memória
            unset($xml, $dom);
            
        } catch (Exception $e) {
            $resultado['erro'] = $e->getMessage();
        }
        
        return $resultado;
    }
    
    /**
     * Limpa arquivos antigos do diretório temporário
     */
    private function limparArquivosAntigos($dir, $tempoSegundos) {
        if (!is_dir($dir)) {
            return;
        }
        
        $arquivos = glob($dir . '*');
        $agora = time();
        
        foreach ($arquivos as $arquivo) {
            if (is_file($arquivo)) {
                if ($agora - filemtime($arquivo) >= $tempoSegundos) {
                    @unlink($arquivo);
                }
            }
        }
    }
    
    /**
     * Download dos arquivos processados
     */
    public function download() {
        $uploadDir = 'C:/systec/xmls corrigidos/';
        
        if (!is_dir($uploadDir)) {
            Session::setFlash('Nenhum arquivo para download.', 'error');
            $this->redirect('xml/index');
            return;
        }
        
        $files = glob($uploadDir . '*.xml');
        
        if (empty($files)) {
            Session::setFlash('Nenhum arquivo para download.', 'error');
            $this->redirect('xml/index');
            return;
        }
        
        // Cria um ZIP com os arquivos
        $zipName = 'xmls_corrigidos_' . date('YmdHis') . '.zip';
        $zipPath = BASE_PATH . '/public/uploads/' . $zipName;
        
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            Session::setFlash('Erro ao criar arquivo ZIP.', 'error');
            $this->redirect('xml/index');
            return;
        }
        
        foreach ($files as $file) {
            $zip->addFile($file, basename($file));
        }
        
        $zip->close();
        
        // Download
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipName . '"');
        header('Content-Length: ' . filesize($zipPath));
        readfile($zipPath);
        
        // Limpa arquivos temporários
        foreach ($files as $file) {
            unlink($file);
        }
        unlink($zipPath);
        
        exit;
    }
}
