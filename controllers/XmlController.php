<?php
/**
 * Controller para Correção de XMLs de NFe
 */

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
            
            // Diretório temporário para processamento
            $uploadDir = BASE_PATH . '/public/uploads/xml_temp/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // Processa cada arquivo
            for ($i = 0; $i < $total; $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    $response['logs'][] = [
                        'tipo' => 'erro',
                        'arquivo' => $files['name'][$i],
                        'mensagem' => 'Erro no upload do arquivo'
                    ];
                    $response['stats']['erros']++;
                    continue;
                }
                
                $nomeArquivo = $files['name'][$i];
                $tmpName = $files['tmp_name'][$i];
                
                // Valida extensão
                if (strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION)) !== 'xml') {
                    $response['logs'][] = [
                        'tipo' => 'erro',
                        'arquivo' => $nomeArquivo,
                        'mensagem' => 'Arquivo não é XML'
                    ];
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
                        $response['logs'][] = [
                            'tipo' => 'sucesso',
                            'arquivo' => $nomeArquivo,
                            'mensagem' => sprintf('Corrigido: diferença ajustada R$ %.2f', $resultado['diferenca'])
                        ];
                    } else {
                        $response['stats']['sem_divergencia']++;
                        $response['logs'][] = [
                            'tipo' => 'info',
                            'arquivo' => $nomeArquivo,
                            'mensagem' => 'Sem divergência'
                        ];
                    }
                } else {
                    $response['stats']['erros']++;
                    $response['logs'][] = [
                        'tipo' => 'erro',
                        'arquivo' => $nomeArquivo,
                        'mensagem' => $resultado['erro']
                    ];
                }
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
            // Carrega o XML
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($caminhoArquivo);
            
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
            $dom->formatOutput = true;
            $resultado['xml'] = $dom->saveXML();
            $resultado['success'] = true;
            
        } catch (Exception $e) {
            $resultado['erro'] = $e->getMessage();
        }
        
        return $resultado;
    }
    
    /**
     * Download dos arquivos processados
     */
    public function download() {
        $uploadDir = BASE_PATH . '/public/uploads/xml_temp/';
        
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
