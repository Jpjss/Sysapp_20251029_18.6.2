<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Funcionalidades
 *
 * @author Systec
 */
class FuncionalidadesHelper extends AppHelper {

    //put your code here

    public function formatarDataAp($data) {
        $data = explode(" ", $data);
        $data[0] = implode("/", array_reverse(explode("-", $data[0])));
        return $data[0] . " " . substr(@$data[1], 0, 8);
    }

    public function formatarDataBd($data) {
        $data = explode(" ", $data);
        $data[0] = implode("-", array_reverse(explode("/", $data[0])));
        return $data[0] . " " . $data[1];
    }

    public function formatarTelefone($telefone) {
        return '(' . substr($telefone, 0, 2) . ') ' . substr($telefone, 2, 4) . '-' . substr($telefone, 6);
    }

    public function imagemPesquisa($id) {
        switch ($id) {
            case(1):
                return 'pesAniversariantes.png';
                break;
            case(2):
                return 'pesInativo.png';
                break;
            case(3):
                return 'pesProspeccao.png';
                break;
            case(4):
                return 'pesPosVendas.png';
                break;
            case(5):
                return 'pesVips.png';
                break;
        }
    }

}

?>
