<?php
require_once '../auth.php';
require_once '../Models/carteira.class.php';
require_once '../Models/cliente.class.php';

$_SESSION['search'] = $_POST['idUser'];

if (!empty($_FILES['arquivo']['name']) && $_POST['valuePay'] != NULL && $_POST['idUser'] != NULL) {

  $idUser = isset($_POST['idUser']) ? $_POST['idUser'] : $idUsuario;
  $valuePay = $_POST['valuePay'];
  $descricao = 'CarteiraCredito';
  $pasta = 'carteira';


  if (!empty($_FILES['arquivo']['name'])) {
    $extensao = pathinfo($_FILES['arquivo']['name']);
    $extensao = "." . strtolower($extensao['extension']);
  }
  if ($extensao == '.jpeg' || $extensao == '.jpg' || $extensao == '.png' || $extensao == '.pdf') {

    $anexoPay = true;
    $arquivoanexo = $_FILES['arquivo']['name']; //Para envio de E-mail
    $imagem = time() . uniqid(md5($idUser)) . $extensao;
    $linkdb =  'views/dist/img/anexos/';
    $arquivo_tmp = $_FILES['arquivo']['tmp_name'];
    $destino =  '../../views/dist/img/anexos/';
    move_uploaded_file($arquivo_tmp, $destino . $imagem);
    chmod($destino . $imagem, 0644);
    $nomeimagem =  $linkdb . $imagem;

    //$linkAnexo = $site_url . '/views/dist/img/anexos/' . $imagem; 
    $linkAnexo = __DIR__ . '../../views/dist/img/anexos/' . $imagem;

    $file_headers = @get_headers($linkAnexo);

    if (stripos($file_headers[0], "404 Not Found") > 0  || (stripos($file_headers[0], "302 Found") > 0 && stripos($file_headers[7], "404 Not Found") > 0)) {
      $_SESSION['msg'] = 12;
      header('Location: ../../views/' . $pasta . '/');
    } else {

      if ($_POST['update'] === "cred_Debi") {
        $idUser = (new carteira)->SetUser($idUser); //idUser neste caso é o CPF do usuário
        $tipo = $_POST['tipo'];
        if ($tipo == 1) {
          $tipoEmail = "Depósito";
        } else {
          $tipoEmail = "Débito";
        }
        $status = 1;
        $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : $tipoEmail;
        $return = (new Carteira)->carteiraCredito($valuePay, $descricao, $nomeimagem, $idUser, $tipo, $status);
      } else {
        $descricao = "Depósito";
        $tipoEmail = "Depósito";
        $return = (new Carteira)->carteiraCredito($valuePay, $descricao, $nomeimagem, $idUser);
      }
      if ($return != 0) {
        /* $resp = (new Cliente)->editarId($idUser);

        $cpfuser = $resp['User']['cpfUser'];
        $email = $resp['User']['email'];
        $idCarteira = $return;
        //-------------------------------------------------//
        include_once './enviarEmailCarteira.php';
        //------------------------------------------------//
        */
        $_SESSION['msg'] = 1;
        header('Location: ../../views/' . $pasta . '/');
      } else {
        $_SESSION['msg'] = 0;
        header('Location: ../../views/' . $pasta . '/');
      }
    }
  } else {
    $_SESSION['msg'] = 12;
    header('Location: ../../views/' . $pasta . '/');
  }
} else {
  $_SESSION['msg'] = 10;
  header('Location: ../../views/carteira/');
}
