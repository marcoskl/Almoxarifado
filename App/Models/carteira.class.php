<?php

require_once 'connect.php';

class Carteira extends Connect
{
  public function SetUser($value)
  {
    $query = "SELECT `idCliente`, `NomeCliente`, `EmailCliente` FROM `cliente` WHERE `idCliente` = '$value' OR cpfCliente = '$value'";
    $result = mysqli_query($this->SQL, $query);
    if ($row = mysqli_fetch_assoc($result)) {
      return $row['idCliente'];
    } else {
      return null;
    }
  }
  public function SetUserConf($value)
  {
    $query = "SELECT `idCliente`, `cpfCliente`, `NomeCliente`, `EmailCliente` FROM `cliente` WHERE `idCliente` = '$value' OR cpfCliente = '$value'";
    $result = mysqli_query($this->SQL, $query);
    if ($row[] = mysqli_fetch_assoc($result)) {
      return json_encode($row);
    } else {
      return null;
    }
  }
  public function Extrato($value)
  {
    $value = (new Carteira)->SetUser($value);
    $query = "SELECT * FROM `carteira` WHERE `usuario_idUsuario` = '$value' ORDER BY `idCarteira` DESC";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }
  function UpdateSaldo($idCarteira, $saldo, $status, $descricao, $perm)
  {
    if ($perm == 1) {
      $query = "UPDATE `carteira` SET `Saldo` = '$saldo', `descricao` = '$descricao', `status` = '$status' WHERE `idCarteira` = '$idCarteira'";
      $result = mysqli_query($this->SQL, $query);
      if ($result) {
        if ($status == 1) {
          return 1;
        }
        return 2;
      }
      return 0;
    }
  }
  function DeleteSaldo($idCarteira, $perm)
  {
    if ($perm == 1) {
      $query = "DELETE FROM `carteira` WHERE `idCarteira` = $idCarteira";
      $result = mysqli_query($this->SQL, $query);
      if ($result) {
        return 1;
      } else {
        return 0;
      }
    }
  }
  public function SaldoEmConta($idCliente)
  {
    $Entradas = (new Carteira)->SomaEntradas($idCliente);
    $Entradas = json_decode($Entradas, true);
    $Entrada = $Entradas[0]['Saldo'];
    $Saidas = (new Carteira)->SomaSaidas($idCliente);
    $Saidas = json_decode($Saidas, true);
    $Saida = $Saidas[0]['debito'];
    $Saldo = $Entrada - $Saida;
    return  $Saldo;
  }

  public function SomaEntradas($idCliente)
  {
    $query = "SELECT SUM(Saldo) AS Saldo FROM `carteira` 
    WHERE `usuario_idUsuario` = '$idCliente' 
    AND Tipo = 1
    AND  status = 1";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }

  public function SomaSaidas($idCliente)
  {
    $query = "SELECT SUM(Saldo) AS debito FROM `carteira` 
    WHERE `usuario_idUsuario` = '$idCliente' 
    AND Tipo = 0";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }
  public function creditoDebito($idCarteira, $valorPay, $valorPaySaldo, $desc, $anexo, $idUserProcesso)
  {
    try {
      $query = "UPDATE Carteira SET `status` = 1, `Saldo` = '$valorPaySaldo' WHERE `idCarteira` = $idCarteira";
      mysqli_query($this->SQL, $query);

      $query2 = "INSERT INTO Carteira (`Saldo`, `Tipo`, `Descricao`, `anexo`, `status`, `DataReg`, `usuario_idUsuario`) 
      VALUES ('$valorPay', 0, '$desc', '$anexo', 1, CURRENT_TIMESTAMP, '$idUserProcesso')";
      mysqli_query($this->SQL, $query2);
      return 1;
    } catch (Exception $e) {
      return 'Error: credito e debito - ' . $e;
    }
  }
  public function carteiraDebito($Saldo, $descricao, $anexo, $idCliente, $pagamento = null)
  {
    if ($pagamento != null) {
      $tipo = $pagamento;
    } else {
      $tipo = 0;
    }
    $queryInsert = "INSERT INTO `carteira`(`Saldo`, `Tipo`, `Descricao`, `anexo`, `status`, `DataReg`, `usuario_idUsuario`) 
    VALUES ('$Saldo', '$tipo', '$descricao', '$anexo', 1, CURRENT_TIMESTAMP, '$idCliente')";
    mysqli_query($this->SQL, $queryInsert);
    return 1;
  }
  public function carteiraCredito($Saldo, $descricao, $anexo, $idUser, $tipo = NULL, $status = NULL)
  {
    if ($tipo != NULL) {
      $tipos = $tipo;
    } else {
      $tipos = 1;
    }
    if ($status != NULL) {
      $status = $status;
    } else {
      $status = 0;
    }
    $queryInsert = "INSERT INTO `carteira`(`Saldo`, `Tipo`, `Descricao`, `anexo`, `status`, `DataReg`, `usuario_idUsuario`) 
    VALUES ('$Saldo', $tipos, '$descricao', '$anexo', '$status', CURRENT_TIMESTAMP, '$idUser')";
    if (mysqli_query($this->SQL, $queryInsert)) {
      $id = mysqli_insert_id($this->SQL);
      return $id;
    }
    return 0;
  }
  public function removeCredito($idCarteira)
  {
    $query = "DELETE FROM `carteira` WHERE `idCarteira` = $idCarteira";
    $result = mysqli_query($this->SQL, $query);
    if ($result) {
      return 1;
    } else {
      return 0;
    }
  }

  public function listCarteiraAnalise()
  {

    $query = "SELECT * FROM Clientes WHERE idCliente IN 
    (SELECT `usuario_idUsuario` FROM `carteira` WHERE `status` = 0 AND `Tipo` = 1) ";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }
  public function verificStatusCarteiras($perm, $status = null)
  {
    if ($perm == 1) {
      if ($status != null) {
        $status = " AND C.status = $status";
      } else {
        $status = " AND C.status = 0 ";
      }
      $query = "SELECT C.*, U.idCliente, U.cpfCliente, U.NomeCliente, U.EmailCliente FROM carteira AS C, cliente AS U WHERE C.usuario_idUsuario = U.idCliente $status ORDER BY C.idCarteira DESC LIMIT 100";
      $result = mysqli_query($this->SQL, $query);
      while ($row[] = mysqli_fetch_assoc($result));
      return json_encode($row);
    }
  }
  public function verifyPay($codigoCota, $idUser, $idPart = null)
  {
    if ($idPart != null) {
      $table = ", ParticipantesCota AS P";
      $AND = "AND P.idparticipantesCota = '$idPart' AND C.usuario_idUsuario = P.usuario_idUsuario";
    } else {
      $table = '';
      $AND = '';
    }
    $regra = "Crédito - Referente ao Bolão n.º $codigoCota-$idPart";
    $query = "SELECT * FROM Carteira AS C $table
    WHERE C.Tipo = 1 
    AND C.Descricao = '$regra'
    AND C.usuario_idUsuario = '$idUser' $AND";
    $result = mysqli_query($this->SQL, $query);
    $total = mysqli_num_rows($result);

    if ($total > 0) {
      if ($row = mysqli_fetch_assoc($result)) {
        if ($row['idCarteira'] != null) {
          return array(1, $row['Saldo']);
        }
      }
    } else {
      return 0;
    }
  }

  public function alertSaldo($idCliente)
  {
    $idCliente = mysqli_real_escape_string($this->SQL, $idCliente);

    $saldo = (new Carteira)->SaldoEmConta($idCliente);

    $query = "SELECT * from `AlertSaldo` WHERE `usuario_idUsuario` = '$idCliente'";
    $result = mysqli_query($this->SQL, $query);
    if ($row = mysqli_fetch_assoc($result)) {
      if ($saldo >= $row['saldoMinimo']) {
        return null;
      } else {
        $saldoMinimo = $row['saldoMinimo'];
        return array('saldo' => $saldo, 'saldoMinimo' => $saldoMinimo);
      }
    }
  }

  public function verifyLogsSaldoMinimo($idCliente)
  {
    $idCliente = mysqli_real_escape_string($this->SQL, $idCliente);
    $query = "SELECT * FROM `logs` WHERE `usuario_idUsuario` = '$idCliente' AND `descricao` LIKE 'AlertSaldo%' ORDER BY `idlogs` DESC";
    $result = mysqli_query($this->SQL, $query);
    $total = mysqli_num_rows($result);
    if ($total > 0) {
      if ($row = mysqli_fetch_assoc($result)) {
        $dataout = date('Y-m-d H:i:s', strtotime("+2 days 23 hours", strtotime($row['dataregout'])));
        $dataout = strtotime($dataout);
        $dataAtual = strtotime(date('Y-m-d H:i:s'));
        if ($dataout < $dataAtual) {
          return 1;
        } else {
          return 0;
        }
      }
    } else {
      return 1;
    }
  }

  public function registerAlertSsaldo($idCliente, $saldoMinimo)
  {
    $idCliente = mysqli_real_escape_string($this->SQL, $idCliente);
    $saldoMinimo = mysqli_real_escape_string($this->SQL, $saldoMinimo);

    $select = "SELECT * FROM `AlertSaldo` WHERE `Clientes_idCliente` = '$idCliente'";
    $result = mysqli_query($this->SQL, $select);
    $total = mysqli_num_rows($result);
    if ($total > 0) {
      $query = "UPDATE `AlertSaldo` SET `saldoMinimo` = '$saldoMinimo', `dataReg` = CURRENT_TIMESTAMP WHERE `Clientes_idCliente` = '$idCliente'";
    } else {
      $query = "INSERT INTO `AlertSaldo` VALUES(NULL, '$saldoMinimo', CURRENT_TIMESTAMP,'$idCliente')";
    }
    mysqli_query($this->SQL, $query);
    return 1;
  }

  public function verifyAlertSsaldo($idCliente)
  {
    $query = "SELECT * FROM `AlertSaldo` WHERE `Clientes_idCliente` = '$idCliente'";
    $result = mysqli_query($this->SQL, $query);
    if ($row = mysqli_fetch_assoc($result)) {
      return $row['saldoMinimo'];
    }
  }

  public function UsersAlertSaldo()
  {
    $query = "SELECT * FROM `AlertSaldo`";
    $result = mysqli_query($this->SQL, $query);
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  }
  public function verifyDebito($anexo, $idCliente)
  {
    $query = "SELECT * FROM `carteira` WHERE `anexo` = '$anexo' AND `usuario_idUsuario` = '$idCliente'";
    $result = mysqli_query($this->SQL, $query);
    if ($row = mysqli_fetch_assoc($result)) {
      return $row;
    } else {
      return 0;
    }
  }
}
