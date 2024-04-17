<?php

/*
   Class produtos
  */

require_once 'connect.php';

class Usuario extends Connect
{

  public function index($perm, $idUsuario)
  {
    if ($perm == 1) {
      $query = "SELECT * FROM `usuario`";
      $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

      while ($row[] = mysqli_fetch_array($result));
      return json_encode($row);
    } else {
      echo "Você não tem Permissao de acesso a este conteúdo!";
    }
  }

  public function insertUser($username, $email, $password, $pt_file, $permissao, $perm)
  {
    if ($perm == 1) {
      $username = (new Connect)->convertMysql($username);
      $email = (new Connect)->convertMysql($email);
      $pt_file = (new Connect)->convertMysql($pt_file);
      $dataRegistro = date('Y-m-d');
      $password = password_hash($password, PASSWORD_DEFAULT);

      $query = "INSERT INTO `usuario`(`username`,`email`,`password`,`imagem`,`dataregistro`, `permissao`)VALUES ('$username', '$email', '$password', '$pt_file', '$dataRegistro', '$permissao')";
      $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
      $id = mysqli_insert_id($this->SQL);
      return 1;
    }
  }

  public function editUsuario($id)
  {

    $query = "SELECT `idUsuario`, `username`, `email`, `imagem`, `permissao` FROM `usuario` WHERE `idUsuario` = '$id'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($row[] = mysqli_fetch_assoc($result)) {
      return json_encode($row);
    }
  }
  public function updateUser($idUser, $username, $email, $nomeimagem, $permissao = NULL)
  {

    if ($permissao != NULL) {
      $Permissao = ", permissao = '$permissao'";
    } else {
      $Permissao = '';
    }
    $username = (new Connect)->convertMysql($username);
    $email = (new Connect)->convertMysql($email);
    $nomeimagem = (new Connect)->convertMysql($nomeimagem);

    $query = "UPDATE `usuario` SET `username`='$username',`email`='$email',`imagem`='$nomeimagem' $Permissao WHERE `idUsuario`= '$idUser'";

    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {
      return 1;
    } else {
      return 0;
    }
  }

  public function trocaSenha($passAtual, $password, $idUsuario)
  {

    $query = "SELECT * FROM `usuario` WHERE `idUsuario` = '$idUsuario'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($row = mysqli_fetch_array($result)) {

      if (password_verify($passAtual, $row['password'])) {

        $id = $row['idUsuario'];

        $password = password_hash($password, PASSWORD_DEFAULT);

        $up = "UPDATE `usuario` SET `password` = '$password' WHERE `idUsuario` = '$id'";
        mysqli_query($this->SQL, $up) or die(mysqli_error($this->SQL));

        return 1;
      }
      return 0;
    }
    return 0;
  }
}
