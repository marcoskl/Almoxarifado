<?php
require_once '../auth.php';
require_once '../Models/vendas.class.php';

if (isset($_POST['retiradacaixa']) == 'saque') {
  $valor = $_POST['valorretirada'] ?? '0';
  $descricao = $_POST['descricaoretirada'];
  $resp = (new Vendas)->caixaRetirada($idUsuario, $valor, $descricao, $perm);

  if ($resp != null) {
    $_SESSION['alert'] = 1;
  } else {
    $_SESSION['alert'] = 0;
  }
}
header('Location: ../../views/vendas/');
