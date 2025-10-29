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
class FuncionalidadesComponent extends Component {

    //put your code here
    public function formatarDataAp($date) {
        $date = explode(" ", $date);
        $date[0] = implode("/", array_reverse(explode("-", $date[0])));
        return $date[0] . " " . @$date[1];
    }

    public function formatarDataBd($date) {
        $date = explode(" ", $date);
        $date[0] = implode("-", array_reverse(explode("/", $date[0])));
        return $date[0] . " " . @$date[1];
    }

    public function formatarMoedaBd($moeda) {
        $moeda = str_replace(".", "", $moeda);
        $moeda = str_replace(",", ".", $moeda);
        return $moeda;
    }
 
}

?>
