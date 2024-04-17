<?php

/**
 * Class Cliente
 */

require_once 'connect.php';

class Cliente extends Connect
{

  function index($value, $perm)
  {
    if ($perm != 1) {
      echo "Você não tem permissão!";
      exit();
    }

    if ($value == NULL) {
      $value = 1;
    }

    $query = "SELECT * FROM `cliente` WHERE `statusCliente` = '$value'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {

      while ($row = mysqli_fetch_array($result)) {


        echo '<br />Cliente: ' . $row['NomeCliente'];
      }
    }
  } //fim -- index

  function insertCliente($NomeCliente, $EmailCliente, $cpfCliente, $idUsuario, $perm)
  {
    if ($perm == 1) {

      $cpfCliente = connect::limpaCPF_CNPJ($cpfCliente);

      $idCliente = Cliente::idCliente($cpfCliente);

      if ($idCliente > 0) {
        return 2;
        exit();
      } else {

        $NomeCliente = mysqli_real_escape_string($this->SQL, $NomeCliente);
        $EmailCliente = mysqli_real_escape_string($this->SQL, $EmailCliente);
        $cpfCliente = mysqli_real_escape_string($this->SQL, $cpfCliente);

        $query = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `EmailCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`) VALUES (NULL,'$NomeCliente','$EmailCliente','$cpfCliente',1,'$idUsuario')";
        $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

        if ($result) {

          return 1;
        } else {
          return 0;
        }
      }

      mysqli_close($this->SQL);
    }
  } //Insert Cliente

  function updateCliente($idCliente, $NomeCliente, $EmailCliente, $cpfCliente, $idUsuario, $perm)
  {

    if ($perm == 1) {

      $cpfCliente = connect::limpaCPF_CNPJ($cpfCliente);

      $NomeCliente = mysqli_real_escape_string($this->SQL, $NomeCliente);
      $EmailCliente = mysqli_real_escape_string($this->SQL, $EmailCliente);
      $cpfCliente = mysqli_real_escape_string($this->SQL, $cpfCliente);

      $query = "UPDATE `cliente` SET `NomeCliente`='$NomeCliente',`EmailCliente`='$EmailCliente',`cpfCliente`='$cpfCliente', `Usuario_idUsuario`= '$idUsuario' WHERE `idCliente`= '$idCliente'";
      $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

      if ($result) {
        return 1;
      } else {
        return 0;
      }

      mysqli_close($this->SQL);
    }
  }

  function statusCliente($status, $idCliente)
  {

    $query = "UPDATE `cliente` SET `statusCliente`= '$status' WHERE `idCliente`= '$idCliente'";

    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {
      return 1;
    } else {
      return 0;
    }

    mysqli_close($this->SQL);
  }

  function deleteCliente($idCliente)
  {

    $query = "DELETE FROM `cliente` WHERE `idCliente`= '$idCliente'";

    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {
      return 1;
    } else {
      return 0;
    }

    mysqli_close($this->SQL);
  }

  public function idcliente($cpfCliente)
  {

    $client = "SELECT * FROM `cliente` WHERE `cpfCliente` = '$cpfCliente'";

    if ($resultcliente = mysqli_query($this->SQL, $client)  or die(mysqli_error($this->SQL))) {

      $row = mysqli_fetch_array($resultcliente);
      return $idCliente = $row['idCliente'];
    }
  }

  function search($value)
  {

    if (isset($value)) {
      //$output = '';  
      $query = "SELECT * FROM `cliente` WHERE `cpfCliente` LIKE '" . $value . "%' OR `NomeCliente` LIKE '" . $value . "%' LIMIT 5";
      $result = mysqli_query($this->SQL, $query);

      if (mysqli_num_rows($result) > 0) {

        while ($row = mysqli_fetch_array($result)) {

          $output[] = $row;
        }

        return array('data' => $output);
      } else {

        return 0;
      }
    }
  } //------

  function searchdata($value)
  {

    $value = explode(' ', $value);
    $valor = str_replace(".", "", $value[0]); // Primeiro tira os pontos
    $valor = str_replace("-", "", $valor); // Depois tira o taço
    $value = $valor;

    if (isset($value)) {
      //$output = '';  
      $query = "SELECT * FROM `cliente` WHERE `cpfCliente` = '$value'";
      $result = mysqli_query($this->SQL, $query);
      if (mysqli_num_rows($result) > 0) {

        if ($row = mysqli_fetch_array($result)) {
          $output[] = $row;
        }
        return array('data' => $output);
      } else {
        return $value;
      }
    }
  } //----searchdata------

  public function dadoscliente($idCliente)
  {

    $client = "SELECT * FROM `cliente` WHERE `idCliente` = '$idCliente'";

    if ($resultcliente = mysqli_query($this->SQL, $client)  or die(mysqli_error($this->SQL))) {

      $row = mysqli_fetch_assoc($resultcliente);
      return $row;
    }
  }
  function SelectUsuario($value)
  {
    $query = "SELECT * FROM `cliente` WHERE `idCliente` = '$value' OR `cpfCliente` = '$value'";
    $result = mysqli_query($this->SQL, $query);
    $row[] = mysqli_fetch_assoc($result);
    return json_encode($row);
  }
  //Class Carteira - 
  function SaldoEmConta($idUsuario)
  {
    $Entradas = (new Cliente)->SomaEntradas($idUsuario);
    $Entradas = json_decode($Entradas, true);
    $Entrada = $Entradas[0]['Saldo'];
    $Saidas = (new Cliente)->SomaSaidas($idUsuario);
    $Saidas = json_decode($Saidas, true);
    $Saida = $Saidas[0]['debito'];
    $Saldo = $Entrada - $Saida;
    return  $Saldo;
  }

  function SomaEntradas($idUsuario)
  {
    $query = "SELECT SUM(Saldo) AS Saldo FROM `carteira` WHERE `usuario_idUsuario` = '$idUsuario' AND Tipo = 1";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }

  function SomaSaidas($idUsuario)
  {
    $query = "SELECT SUM(Saldo) AS debito FROM `carteira` WHERE `usuario_idUsuario` = '$idUsuario' AND Tipo = 0";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }
  // class Carteira
}
