<?php
require_once '../auth.php';
require_once '../Models/carteira.class.php';
if (isset($_POST['idCarteira']) != NULL && isset($_POST['saldo']) != NULL) {
  $idCarteira = $_POST['idCarteira'];
  $saldo = $_POST['saldo'];
  $status = $_POST['status'];
  $descricao = $_POST['descricao'];
  $ButtonUpdate = $_POST['update'];
  $idUser = $_POST['idUserCarteira'];
  $result = (new Carteira)->UpdateSaldo($idCarteira, $saldo, $status, $descricao, $perm);
  if ($result == 1) {
    $resp = (new Carteira)->SetUserConf($idUser);
    $resp = json_decode($resp, true);
    $valuePay = $saldo;
    foreach ($resp as $row) {
      $email = $row['emailUsuario'];
      $nomeUser = $row['nomeUsuario'];
      $cpfuser = $row['cpfUsuario'];
    }
    include './cartDepConfirmado.php';
  } elseif ($result == 2) {
    $result = 1;
  }
} else {
  $result = 0;
}
$_SESSION['msg'] = $result;
if ($ButtonUpdate == 'listCarteira') {
  header('Location: ../../views/carteira/listCarteiras.php');
  exit();
}
$_SESSION['search'] = $idUser;
header('Location: ../../views/carteira/');