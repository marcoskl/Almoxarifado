<?php
require_once '../auth.php';
require_once '../Models/carteira.class.php';
if (isset($_POST['idCarteira']) != NULL) {
  $idCarteira = $_POST['idCarteira'];
  $result = (new Carteira)->DeleteSaldo($idCarteira, $perm);
} else {
  $result = 0;
}
$_SESSION['msg'] = $result;
header('Location: ../../views/carteira/');