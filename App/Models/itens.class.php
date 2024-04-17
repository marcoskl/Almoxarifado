<?php

/*
   Class produtos
  */

require_once 'connect.php';

class Itens extends Connect
{

  public function listItens($idprodutos, $idFabricante)
  {
    $query = "SELECT * FROM `itens`,`fabricante`,`produtos` WHERE (`Fabricante_idFabricante` = `idFabricante` AND `Produto_CodRefProduto` = `CodRefProduto`) AND (`Fabricante_idFabricante` = '$idFabricante' AND `Produto_CodRefProduto` = '$idprodutos') ";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    $q = 0;
    $v = 0;
    $e = 0;
    while ($rowlist = mysqli_fetch_array($result)) {

      $q = $q + $rowlist['QuantItens'];
      $v = $v + $rowlist['QuantItensVend'];
      $e = $q - $v;
      $NomeProduto = $rowlist['NomeProduto'];
      $fabricante  = $rowlist['NomeFabricante'];
    }

    return array('NomeProduto' => $NomeProduto, 'Fabricante' => $fabricante, 'QuantItens' => $q, 'QuantItensVend' => $v, 'Estoque' => $e,);
  }

  public function totalitens($value)
  {
    $query = "SELECT `Produto_CodRefProduto`, `Fabricante_idFabricante` FROM `itens` WHERE `itensPublic` = '$value' GROUP BY `Produto_CodRefProduto`, `Fabricante_idFabricante`";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
    while ($row = mysqli_fetch_array($result)) {

      $idprodutos = $row['Produto_CodRefProduto'];
      $idFabricante = $row['Fabricante_idFabricante'];

      echo '<li>';
      $resp = Itens::listItens($idprodutos, $idFabricante);
      echo '<b> Produto: ' . $resp['NomeProduto'];
      echo ' / Fabricante: ' . $resp['Fabricante'];
      echo '</b> Comprados: ' . $resp['QuantItens'];
      echo ' | Vendidos: ' . $resp['QuantItensVend'];
      echo ' | Em Estoque: ' . $resp['Estoque'];
      echo '</li>';
    }
  }

  public function index($value)
  {
    $query = "SELECT * FROM `itens`,`fabricante`,`produtos` WHERE (`Fabricante_idFabricante` = `idFabricante` AND `Produto_CodRefProduto` = `CodRefProduto`) AND `itensPublic` = '$value'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($result) {

      echo '<table id="example1" class="table">
    <thead class="thead-inverse">
      <tr>
        <th>Ativo</th>
        <th>Image</th>
        <th>Nome Produto</th>
        <th>Fabricante</th>
        <th>Quant. Estoque</th>
        <th>Quant. Vendido</th>
        <th>V. Compra.</th>
        <th>V. Vendido</th>
        <th>Data Compra</th>
        <th>Data Vencimento</th>
        <th>Edit</th>
        <th>Public</th>
      </tr>
    </thead>
    <tbody>';

      while ($row = mysqli_fetch_array($result)) {

        if ($row['ItensAtivo'] == 0) {
          $c = 'class="label-warning"';
        } else {
          $c = " ";
        }
        echo '<tr ' . $c . '><th>
          <!-- drag handle -->
          <span class="handle">
            <i class="fa fa-ellipsis-v"></i>
            <i class="fa fa-ellipsis-v"></i>
          </span>

          <!-- checkbox -->';
        $id = $row['idItens'];
        $Ativo = $row['ItensAtivo'];

        echo '<form class="label" name="ativ' . $id . '" enctype="multipart/form-data"  action="../../App/Database/action.php" method="post">
          <input type="hidden" name="id" id="id_action' . $id . '" value="' . $id . '">          
          <input type="hidden" id="status' . $id . '" name="status" 
          value="' . $Ativo . '">
          <input type="hidden" name="tabela" id="tabela' . $id . '" value="itens">  

          <input type="checkbox" id="checked' . $id . '" name="checked[' . $id . ']" ';
        if ($Ativo == 1) {
          echo "checked";
        }
        echo ' onclick="this.form.submit();"></form>
          </th><td>
          ';

        if (!empty($row['Image'])) {
          echo '<img src="../' . $row['Image'] . '" width="50" />';
        }
        echo '</td><td>' . $row['NomeProduto'] . '</td>
          <td>' . $row['NomeFabricante'] . '</td>
          <td>' . $row['QuantItens'] . '</td>
          <td>' . $row['QuantItensVend'] . '</td>
          <td>' . number_format($row['ValCompItens'],'2',',','.') . '</td>
          <td>' . number_format($row['ValVendItens'],'2',',','.') . '</td>
          <td>' . date('d/m/Y', strtotime($row['DataCompraItens'])) . '</td>
          <td>';
           if($row['DataVenci_Itens']){ 
            echo date('d/m/Y', strtotime($row['DataVenci_Itens'])); 
           }
           echo '</td>
          
          <td>
                <a href="edititens.php?q=' . $row['idItens'] . '"><i class="fa fa-edit"></i></a>
          </td>
          <td>
              <!-- Button trigger modal -->
                    <a href="" data-toggle="modal" data-target="#myModal' . $row['idItens'] . '">';

        if ($row['Public'] == 0) {
          echo '<i class="glyphicon glyphicon-remove" aria-hidden="true"></i>';
        } else {
          echo '<i class="glyphicon glyphicon-ok" aria-hidden="true"></i>';
        }

        echo '</a>


    <!-- Modal -->
  <div>
    <form id="delItens' . $row['idItens'] . '" name="delItens' . $row['idItens'] . '" action="../../App/Database/delItens.php" method="post" style="color:#000;">
    <div class="modal fade" id="myModal' . $row['idItens'] . '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Você tem serteza que deseja alterar o status deste item na sua lista.</h4>
          </div>
          <div class="modal-body">
            Código: ' . $row['idItens'] . ' - ' . $row['NomeProduto'] . ' - ' . $row['NomeFabricante'] . '
          </div>
          <input type="hidden" id="id' . $row['idItens'] . '" name="id" value="' . $row['idItens'] . '">
          <div class="modal-footer">
            <button type="submit" value="Cancelar" class="btn btn-default">Não</button>
            <button type="submit" name="update" value="Cadastrar" class="btn btn-primary">Sim</button>
          </div>
        </div>
      </div>
    </div>
    </form></div>

          </td>
            </tr>';
      }
      echo '</tbody>
  </table>';
    }
  }

  public function InsertItens($nomeimagem, $QuantItens, $ValCompItens, $ValVendItens, $DataCompraItens, $DataVenci_Itens, $Produto_CodRefProduto, $Fabricante_idFabricante, $idusuario)
  {

    if($DataVenci_Itens != null){
      $query = "INSERT INTO `itens`(`idItens`,`Image` ,`QuantItens`, `QuantItensVend`, `ValCompItens`, `ValVendItens`, `DataCompraItens`, `DataVenci_Itens`, `ItensAtivo`,`ItensPublic`, `Produto_CodRefProduto`, `Fabricante_idFabricante`, `Usuario_idUsuario`) VALUES (NULL, '$nomeimagem', '$QuantItens', 0, '$ValCompItens', '$ValVendItens', '$DataCompraItens', '$DataVenci_Itens', 1, 1, '$Produto_CodRefProduto', '$Fabricante_idFabricante', '$idusuario')";
 
    }else{
      $query = "INSERT INTO `itens`(`idItens`,`Image` ,`QuantItens`, `QuantItensVend`, `ValCompItens`, `ValVendItens`, `DataCompraItens`, `ItensAtivo`,`ItensPublic`, `Produto_CodRefProduto`, `Fabricante_idFabricante`, `Usuario_idUsuario`) VALUES (NULL, '$nomeimagem', '$QuantItens', 0, '$ValCompItens', '$ValVendItens', '$DataCompraItens',  1, 1, '$Produto_CodRefProduto', '$Fabricante_idFabricante', '$idusuario')";

    }
    
    
    if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

      header('Location: ../../views/itens/index.php?alert=1');
    } else {
      header('Location: ../../views/itens/index.php?alert=0');
    }
  } //InsertItens

  public function editItens($value)
  {
    $query = "SELECT *FROM `itens` WHERE `idItens` = '$value'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    if ($row = mysqli_fetch_array($result)) {

      $idItens = $row['idItens'];
      $nomeimagem = $row['Image'];
      $QuantItens = $row['QuantItens'];
      $ValCompItens = $row['ValCompItens'];
      $ValVendItens = $row['ValVendItens'];
      $DataCompraItens = $row['DataCompraItens'];
      $DataVenci_Itens = $row['DataVenci_Itens'];
      $Produto_CodRefProduto = $row['Produto_CodRefProduto'];
      $Fabricante_idFabricante = $row['Fabricante_idFabricante'];

      return $resp = array('Itens' => [
        'idItens' => $idItens,
        'Image' => $nomeimagem,
        'QuantItens'   => $QuantItens,
        'ValCompItens' => $ValCompItens,
        'ValVendItens' => $ValVendItens,
        'DataCompraItens' => $DataCompraItens,
        'DataVenci_Itens' => $DataVenci_Itens,
        'CodRefProduto' => $Produto_CodRefProduto,
        'idFabricante' => $Fabricante_idFabricante
      ],);
    }
  }

  public function updateItens($idItens, $nomeimagem, $QuantItens, $ValCompItens, $ValVendItens, $DataCompraItens, $DataVenci_Itens, $Produto_CodRefProduto, $Fabricante_idFabricante, $idusuario)
  {
    if($DataVenci_Itens != null){
    $ADD = "`DataVenci_Itens`='$DataVenci_Itens',";
    }else{
    $ADD = '';
    }
    $query = "UPDATE `itens` SET
      `Image` = '$nomeimagem', 
      `QuantItens`= '$QuantItens',
      `ValCompItens`='$ValCompItens',
      `ValVendItens`='$ValVendItens',
      `DataCompraItens`='$DataCompraItens',
      $ADD
      `Produto_CodRefProduto`='$Produto_CodRefProduto',
      `Fabricante_idFabricante`='$Fabricante_idFabricante',
      `Usuario_idUsuario`='$idusuario' 
      WHERE `idItens`= '$idItens'";
    

    if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

      header('Location: ../../views/itens/index.php?alert=1');
    } else {
      header('Location: ../../views/itens/index.php?alert=0');
    }
  }

  public function QuantItensVend($value, $idItens)
  {
    $query = "UPDATE `itens` SET `QuantItensVend` = '$value' WHERE `idItens`= '$idItens'";

    if ($result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL))) {

      header('Location: ../../views/itens/index.php?alert=1');
    } else {
      header('Location: ../../views/itens/index.php?alert=0');
    }
  }

  public function DelItens($value)
  {

    $query = "SELECT * FROM `itens` WHERE `idItens` = '$value'";
    $result = mysqli_query($this->SQL, $query);
    if ($row = mysqli_fetch_array($result)) {

      $id = $row['idItens'];
      $public = $row['ItensPublic'];

      if ($public == 1) {
        $p = 0;
      } else {
        $p = 1;
      }

      mysqli_query($this->SQL, "UPDATE `itens` SET `ItensPublic` = '$p' WHERE `idItens` = '$id'") or die(mysqli_error($this->SQL));
      header('Location: ../../views/itens/index.php?alert=1');
    } else {
      header('Location: ../../views/itens/index.php?alert=0');
    }
  }

  public function Ativo($value, $id)
  {

    $query = "UPDATE `itens` SET `ItensAtivo` = '$value' WHERE `idItens` = '$id'";
    $result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));

    header('Location: ../../views/itens/');
  } //ItensAtivo

  public function search($value)
  {
    if (isset($value)) {

      //$output = '';  
      $query = "SELECT P.CodRefProduto, P.NomeProduto, I.idItens, I.Produto_CodRefProduto, I.* FROM itens AS I, produtos AS P WHERE (I.Produto_CodRefProduto = P.CodRefProduto) AND (P.CodRefProduto LIKE '$value' OR P.NomeProduto LIKE '" . $value . "%') GROUP BY I.idItens, P.CodRefProduto LIMIT 5";
      $result = mysqli_query($this->SQL, $query);

      if (mysqli_num_rows($result) > 0) {

        while ($row[] = mysqli_fetch_assoc($result));

        return $row;
      } else {

        return 0;
      }
    }
  }

  public function itemSelect($value)
  {

    $query = "SELECT P.CodRefProduto, P.NomeProduto, I.idItens, I.Produto_CodRefProduto, I.* FROM itens AS I, produtos AS P WHERE (I.Produto_CodRefProduto = P.CodRefProduto) AND (P.CodRefProduto = '$value')";
    $result = mysqli_query($this->SQL, $query);
    if ($row[] = mysqli_fetch_assoc($result)) {
      return $row;
    }
  }
}

$itens = new Itens;