<?php
require_once '../auth.php';
require_once '../Models/vendas.class.php';

if (isset($_POST['abrircaixa']) == 'abrir') {
  $valor = $_POST['valoremcaixa'] ?? '0';
  $resp = (new Vendas)->aberturaCaixa($idUsuario, $valor);

  if ($resp == 1) {
    $_SESSION['alert'] = 1;
  } else {
    $_SESSION['alert'] = 0;
  }
}
header('Location: ../../views/vendas/');
