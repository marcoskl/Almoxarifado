<?php

/**
 * Vendas
 */

require_once 'connect.php';

class Vendas extends Connect
{

  public function itensVerify($iditem, $quant, $perm)
  {

    if ($perm > 2) {
      $_SESSION['msg'] =  'Erro - Você não tem permissão!';
      header('Location: ../../views/vendas/');
      exit();
    }

    $query = "SELECT * FROM `itens`, `produtos` WHERE `idItens` = '$iditem' AND `Produto_CodRefProduto` = `CodRefProduto`";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    $total = mysqli_num_rows($result);

    if ($total > 0) {

      if ($row = mysqli_fetch_array($result)) {

        $q = $row['QuantItens'];
        $v = $row['QuantItensVend'];
        $quantotal = $v + $quant;

        if ($q >= $quantotal) {

          return array('status' => '1', 'NomeProduto' => $row['NomeProduto'],);
        } else {
          $estoque = $q - $v;
          return array('status' => '0', 'NomeProduto' => $row['NomeProduto'], 'estoque' => $estoque);
        }
      }
    } else {

      $_SESSION['msg'] =  '<div class="alert alert-warning">
      <strong>Ops!</strong> Produto (' . $iditem . ') não encontrado!</div>';

      header('Location: ../../views/vendas/index.php');
      exit;
    }
  }

  public function itensVendidos($iditem, $quant, $cliente, $email, $cpfcliente, $cart, $idUsuario, $perm, $pagamento, $block = null)
  {

    $cpfcliente = Connect::limpaCPF_CNPJ($cpfcliente);
    $idCliente = Vendas::idCliente($cpfcliente); // Verifica se o cliente existe no DB.

    $jaComprou = (new Vendas)->jaComprou($idCliente, $iditem, $block);

    if ($perm > 2) {
      $_SESSION['msg'] =  '<div class="alert alert-danger alert-dismissible">
                         <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                         <strong>Erro!</strong> Você não tem permissão! </div>';
      header('Location: ../../views/vendas/index.php');
      exit();
    } elseif ($cpfcliente == null && $cliente == null && $email == null && $cart == null) {
      $_SESSION['msg'] =  '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <strong>Erro!</strong> Cadastre um Cliente! </div>';
      header('Location: ../../views/vendas/index.php');
      exit();
    } elseif ($jaComprou >= 3) {
      $_SESSION['msg'] =  '<div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <strong>Erro!</strong> O Cliente já efetuou "' . $jaComprou . '" compras este ano do produto Cód.:' . $iditem . '! </div>';
      header('Location: ../../views/vendas/index.php');
      exit();
    }

    $query = "SELECT * FROM `itens` WHERE `idItens`= '$iditem'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {

      //------Verificação da Venda-----------

      if ($row = mysqli_fetch_array($result)) {

        $q = $row['QuantItens'];
        $v = $row['QuantItensVend'];

        $quantotal = $v + $quant;

        if ($q >= $quantotal) {

          $valor = ($row['ValVendItens'] * $quant);

          if ($idCliente > 0) { // Se o cliente existir, Retorne o ID do cliente
            $idCliente = $idCliente; // ID do cliente
          } else {

            $novoclient = "INSERT INTO `cliente`(`idCliente`, `NomeCliente`, `EmailCliente`, `cpfCliente`, `statusCliente`, `Usuario_idUsuario`) VALUES (NULL,'$cliente','$email','$cpfcliente',1,'$idUsuario')";

            if (mysqli_query($this->SQL, $novoclient) or die(mysqli_error($this->SQL))) {
              $idCliente = mysqli_insert_id($this->SQL);
            }
          }


          $query = "INSERT INTO `vendas`(`idvendas`, `quantitens`, `valor`, `iditem`, `cart`, `cliente_idCliente`, `usuario_idUsuario`) VALUES (NULL, '$quant', '$valor', '$iditem', '$cart', '$idCliente', '$idUsuario')";
          if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

            $query = "UPDATE `itens` SET `QuantItensVend` = '$quantotal' WHERE `idItens`= '$iditem'";
            if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

              //limpa itens da lista
              unset(
                $_SESSION['itens'],
                $_SESSION['CPF'],
                $_SESSION['Cliente'],
                $_SESSION['Email'],
                $_SESSION['cart']
              );

              $_SESSION['notavd'] = $cart;
              $_SESSION['pagamento'] = $pagamento;

              $_SESSION['msg'] = '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <strong>Sucesso!</strong> Venda efetuada!</div>';
            }
          } else {
            $_SESSION['msg'] =  '<div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <strong>Erro!</strong> Venda não efetuada! </div>';

            header('Location: ../../views/vendas/');
            exit();
          }
        } else {

          $estoque = $row['QuantItens'] - $row['QuantItensVend'];

          $_SESSION['msg'] =  '<div class="alert alert-warning alert-dismissible">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>Ops!</strong> Quantidade maior do que em estoque! </br> Quantidade em estoque: <b>' . $estoque . '</b></div>';
          header('Location: ../../views/vendas/');
          exit();
        }

        header('Location: ../../views/vendas/notavd.php');
      }


      //------------------

    } else {
      $_SESSION['alert'] = 0;
      header('Location: ../../views/vendas/');
    }
  } // itensVendidos

  public function idcliente($cpfCliente)
  {

    $client = "SELECT * FROM `cliente` WHERE `cpfCliente` = '$cpfCliente'";

    if ($resultcliente = mysqli_query($this->SQL, $client)  or die(mysqli_error($this->SQL))) {

      $row = mysqli_fetch_array($resultcliente);
      return $idCliente = $row['idCliente'];
    }
  }

  //----------itemNome

  public function itemNome($idItens)
  {

    $query = "SELECT * FROM `produtos` WHERE `CodRefProduto` IN (SELECT `Produto_CodRefProduto` FROM `itens` WHERE `idItens` = '$idItens' AND `ItensAtivo` = 1 AND `ItensPublic` = 1)";

    $result = mysqli_query($this->SQL, $query)  or die(mysqli_error($this->SQL));

    $row = mysqli_fetch_array($result);

    if ($row['NomeProduto'] != NULL) {
      $resp = $row['NomeProduto'];
    } else {
      $resp = NULL;
    }

    return $resp;
  } //--itemNome

  public function notavd($cart)
  {
    $query = "SELECT * FROM `vendas` WHERE `cart` = '$cart'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    while ($row[] = mysqli_fetch_assoc($result));
    return json_encode($row);
  } //--notavd

  public function dadosItem($idItem)
  {

    $query = "SELECT * FROM `fabricante`, `produtos`, `itens` WHERE `idItens` = '$idItem' AND `Produto_CodRefProduto` = `CodRefProduto` AND `Fabricante_idFabricante` = `idFabricante`";

    if ($result = mysqli_query($this->SQL, $query)  or die(mysqli_error($this->SQL))) {

      $row = mysqli_fetch_assoc($result);

      return $row;
    }
  } //---dadosItem

  public function jaComprou($idCliente, $idItem = null, $block = null)
  {
    if (!empty($block)) {


      $Ano = date('Y');
      $dataAno = $Ano . '-01-01 00:00:00';
      $dataIn = $dataAno;
      $dataFim = date('Y-m-d H:i:s');

      $query = "SELECT COUNT(*) AS TOTAL FROM `vendas` WHERE `cliente_idCliente` = '$idCliente' AND (`datareg` BETWEEN '$dataIn' AND '$dataFim' AND `iditem` = '$idItem')";
      $result = mysqli_query($this->SQL, $query);

      $row = mysqli_fetch_assoc($result);
      return $row['TOTAL'];
    } else {
      return 0;
    }
  }
  public function carteiraDebito($Saldo, $debito, $anexo, $idCliente)
  {

    $queryInsert = "INSERT INTO `carteira`(`Saldo`, `Tipo`, `Descricao`, `anexo`, `status`, `DataReg`, `usuario_idUsuario`) 
    VALUES ('$Saldo', 0, '$debito', '$anexo', 1, CURRENT_TIMESTAMP, '$idCliente')";
    mysqli_query($this->SQL, $queryInsert);
    return 1;
  }
  public function caixaAberto($idUsuario)
  {
    $dataHoje = date('Y-m-d');
    $gerartoken = $idUsuario . '#' . date('Y-m-d');
    $token = md5($gerartoken);
    // verifica se o caixa já foi aberto pelo mesmo usuário.
    $verifySC = (new Vendas)->verifyStatusCaixa($idUsuario, $token);
    if ($verifySC != 0) {
      $query = "SELECT * FROM `registrocaixa` WHERE `dataRegistro` >= '$dataHoje' AND `token` = '$token' AND `Usuario_idUsuario` = '$idUsuario'";
      $result = mysqli_query($this->SQL, $query);
      if ($row[] = mysqli_fetch_assoc($result)) {
        return json_encode($row);
      }
    }
  }

  public function caixaRetirada($idUsuario, $valor, $descricao, $perm)
  {
    if ($perm == 1) {
      $gerartoken = $idUsuario . '#' . date('Y-m-d');
      $token = md5($gerartoken);
      $descricao = 'Retirada - ' . $descricao;

      $query = "INSERT INTO `registrocaixa`(`dataRegistro`, `descricao`, `statusCaixa`, `valoremcaixa`, `token`, `Usuario_idUsuario`) 
    VALUES (CURRENT_TIMESTAMP,'$descricao',2,'$valor','$token','$idUsuario')";
      mysqli_query($this->SQL, $query);
      $idRegistroCaixa = mysqli_insert_id($this->SQL);

      $resp = (new Vendas)->verifyRegistroCaixa($idUsuario, $idRegistroCaixa);

      return $resp;
    }
  }

  public function aberturaCaixa($idUsuario, $valor)
  {
    $dataHoje = date('Y-m-d H:i:s');
    $gerartoken = $idUsuario . '#' . date('Y-m-d');
    $token = md5($gerartoken);

    // verifica se o caixa já foi aberto pelo mesmo usuário.
    $verifySC = (new Vendas)->verifyStatusCaixa($idUsuario, $token);
    if ($verifySC == 0) {
      $query = "INSERT INTO `registrocaixa`(`dataRegistro`, `descricao`, `statusCaixa`, `valoremcaixa`, `token`, `Usuario_idUsuario`) VALUES (CURRENT_TIMESTAMP, 'Caixa aberto' , 1, '$valor', '$token', '$idUsuario')";
      mysqli_query($this->SQL, $query);
      return 1;
    } else {
      return 0;
    }
  }

  public function fecharCaixa($idUsuario)
  {
    $dataHoje = date('Y-m-d');
    $gerartoken = $idUsuario . '#' . date('Y-m-d');
    $token = md5($gerartoken);
    // verifica se o caixa já foi aberto pelo mesmo usuário.
    $verifySC = (new Vendas)->verifyStatusCaixa($idUsuario, $token);
    if ($verifySC != 0) {
      $query = "INSERT INTO `registrocaixa`(`dataRegistro`, `descricao`, `statusCaixa`, `token`, `Usuario_idUsuario`) VALUES (CURRENT_TIMESTAMP, 'Caixa fechado' , 0, '$token', '$idUsuario')";
      mysqli_query($this->SQL, $query);

      //return 1;
    }
  }
  public function verifyStatusCaixa($idUsuario, $token)
  {
    // verifica se o caixa já foi aberto pelo mesmo usuário.
    $verify_query = "SELECT * FROM `registrocaixa` WHERE `token` = '$token' AND `Usuario_idUsuario` = '$idUsuario' ORDER BY `idRegistroCaixa` DESC";
    $verify_result = mysqli_query($this->SQL, $verify_query);
    $total = mysqli_num_rows($verify_result);

    if ($row = mysqli_fetch_assoc($verify_result)) {
      if ($row['statusCaixa'] == 0) {
        return 0;
        exit();
      } elseif ($row['statusCaixa'] == 1 || $row['statusCaixa'] == 2) {

        return 1;
        exit();
      }
    }
    return 0;
  }

  public function verifyRegistroCaixa($idRegistroCaixa, $perm)
  {
    if ($perm == 1) {
      $query = "SELECT * FROM `registrocaixa` WHERE `idRegistroCaixa` = $idRegistroCaixa";
      $result = mysqli_query($this->SQL, $query);
      if ($row[] = mysqli_fetch_assoc($result));
      return json_encode($row);
    }
  }
}//Fim Class Vendas