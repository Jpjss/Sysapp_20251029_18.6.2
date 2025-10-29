<?php
header('Content-type: application/json; charset=UTF-8');

$users_selecteds = array();

if(!empty($_GET['term'])){
  foreach($clientes as $cliente){
    if(strripos($cliente, $_GET['term']) !== false){
      array_push($clientes_selecteds, $cliente);
    }
  }
}

echo json_encode($clientes_selecteds);

?>
