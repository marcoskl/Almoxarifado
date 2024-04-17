<?php
require_once '../auth.php';
require_once '../Models/vendas.class.php';
echo $_POST['abrircaixa'];
if (isset($_POST['abrircaixa']) == 'abrir') {

  $resp = (new Vendas)->fecharCaixa($idUsuario);

  if ($resp == 1) {
    $_SESSION['alert'] = 1;
  } else {
    $_SESSION['alert'] = 0;
  }
}

header('Location: ../../views/vendas/relatoriovendasdia.php');
