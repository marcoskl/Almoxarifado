<?php

/*
Class produtos
*/

require_once 'connect.php';

class Fabricante extends Connect
{

  public function index($perm, $value = NULL)
  {

    if ($perm != 1) {
      echo "Você não tem permissão!";
    } else {

      if ($value == NULL) {
        $value = 1;
      }

      $query = "SELECT * FROM `fabricante` WHERE `Public` = '$value'";
      $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

      if ($result) {

        while ($row[] = mysqli_fetch_array($result));
        return json_encode($row);
      }
    }
  }

  public function listfabricante()
  {
    $query = "SELECT * FROM `fabricante`";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }

  public function InsertFabricante($NomeFabricante, $CNPJFabricante, $EmailFabricante, $EnderecoFabricante, $TelefoneFabricante, $idUsuario, $NomeRepresentante, $TelefoneRepresentante, $EmailRepresentante, $status, $perm)
  {

    if ($perm != 1) {
      echo "Você não tem permissão!";
    } else {

      $query = "SELECT * FROM `fabricante` WHERE `NomeFabricante` = '$NomeFabricante'";
      $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

      /*--Alteração de codigo para corriguir erro de verificação 
        se fabricante existe ou não no DB. */

      $total = mysqli_num_rows($result);

      if ($total > 0) {
        $row = mysqli_fetch_array($result);

        $idFabricante = $row['idFabricante'];
      } else {

        $query = "INSERT INTO `fabricante`(`NomeFabricante`, `CNPJFabricante`, `EmailFabricante`, `EnderecoFabricante`, `TelefoneFabricante`, `Public`, `Ativo`, `Usuario_idUsuario`) VALUES ('$NomeFabricante', '$CNPJFabricante', '$EmailFabricante', '$EnderecoFabricante', '$TelefoneFabricante', 1 , 1 , '$idUsuario')";

        $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
        $idFabricante = mysqli_insert_id($this->SQL);
      }

      if ($idFabricante > 0) {

        $query = "INSERT INTO `representante`(`idRepresentante`, `NomeRepresentante`, `TelefoneRepresentante`, `EmailRepresentante`,`repAtivo`,`repPublic`, `Fabricante_idFabricante`, `Usuario_idUsuario`) VALUES (NULL, '$NomeRepresentante', '$TelefoneRepresentante', '$EmailRepresentante',1 , 1,'$idFabricante', '$idUsuario')";

        if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {
          return 1;
        } else {
          return 0;
        }
      } else {
        return 0;
      }
    } //Insert
  }



  public function EditFabricante($idFabricante)
  {

    $query = "SELECT * FROM `fabricante` WHERE `idFabricante` = '$idFabricante'";
    if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

      if ($row = mysqli_fetch_array($result)) {

        $NomeFabricante = $row['NomeFabricante'];
        $CNPJFabricante = $row['CNPJFabricante'];
        $EmailFabricante = $row['EmailFabricante'];
        $EnderecoFabricante = $row['EnderecoFabricante'];
        $TelefoneFabricante = $row['TelefoneFabricante'];
        $Ativo = $row['Ativo'];
        $Usuario_idUsuario  = $row['Usuario_idUsuario'];

        $array = array('Fabricante' => array('Nome' => $NomeFabricante, 'CNPJ' => $CNPJFabricante, 'Email' => $EmailFabricante, 'Endereco' => $EnderecoFabricante, 'Telefone' => $TelefoneFabricante, 'Ativo' => $Ativo, 'Usuario' => $Usuario_idUsuario),);
        return $array;
      }
    } else {
      return 0;
    }
  }

  public function UpdateFabricante($idFabricante, $NomeFabricante, $CNPJFabricante, $EmailFabricante, $EnderecoFabricante, $TelefoneFabricante, $Ativo, $idUsuario, $perm)
  {

    if ($perm != 1) {
      echo "Você não tem permissão!";
      exit();
    } else {

      $query = "UPDATE `fabricante` SET `NomeFabricante`= '$NomeFabricante', `CNPJFabricante`='$CNPJFabricante',`EmailFabricante`='$EmailFabricante',`EnderecoFabricante`='$EnderecoFabricante',`TelefoneFabricante`='$TelefoneFabricante', `Ativo` = '$Ativo' ,`Usuario_idUsuario`='$idUsuario' WHERE `idFabricante` = '$idFabricante'";

      if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

        return 5;
      } else {
        return 0;
      }
    }
  } //update


  public function DelFabricante($idFabricante, $perm)
  {

    if ($perm != 1) {
      echo "Você não tem permissão!";
      exit();
    }

    $query = "SELECT * FROM `fabricante` WHERE `idFabricante` = '$idFabricante'";
    $result = mysqli_query($this->SQL, $query);
    if ($row = mysqli_fetch_assoc($result)) {

      $id = $row['idFabricante'];
      $public = $row['Public'];

      if ($public == 1) {
        $p = 0;
      } else {
        $p = 1;
      }

      $query = "UPDATE `fabricante` SET `Public` = '$p' WHERE `idFabricante` = '$id'";
      mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
      return 1;
    } else {
      return 0;
    }
  }

  public function Ativo($value, $id)
  {
    
   $query = "UPDATE `fabricante` SET `Ativo` = '$value' WHERE `idFabricante` = '$id'";
    mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    return 1;
  }
}

   // $fabricante = new Fabricante;