<?php
require_once '../auth.php';
require_once('../Models/itens.class.php');

if (isset($_POST["codigo"]) != null) {

  $resp = (new Itens)->search($_POST["codigo"]);
  if ($resp != 0) {
    //$array = [];
    // Verificar se o array já está na sessão, senão, criá-lo
    if (!isset($_SESSION['carrinho'])) {
      $_SESSION['carrinho'] = array();
    }

    foreach ($resp as $user) {
      if (isset($user['idItens']) != null) {
        // Adicionar um novo item ao carrinho (adicionando um item fictício para demonstração)
        $idItens = $user['idItens'];
        $codigo = $user['CodRefProduto'];
        $nome = $user['NomeProduto'];
        $valor = $user['ValVendItens'];
        $item = array('codigo' => $codigo, 'iditem' => $idItens, 'nome' => $nome, 'valor' => $valor);

        echo json_encode($item);
      }
    }
    $_SESSION['carrinho'][] = $item;
  }
}