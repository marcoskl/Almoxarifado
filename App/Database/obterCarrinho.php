<?php
require_once '../auth.php';
require_once('../Models/itens.class.php');
/*
	Existe uma atualização no PHP 7.2 que modifica o uso do "count" if(count($_SESSION['itens']) == 0), 
	no caso de você esta utilizando está versão ou superior,
	basta subistituir por "isset" ficando 
	====================================================
		if(isset($_SESSION['itens']) == 0)... 
	====================================================
	ou modificar o código ficando assim
	====================================================
		$pkCount = (isset($_SESSION['itens']) ? count($_SESSION['itens']) : 0);
		if ($pkCount == 0) {...
	====================================================
	*/

$pkCount = (isset($_SESSION['itens']) ? count($_SESSION['itens']) : 0);
if ($pkCount == 0) { // Alterado conforme descrito
  echo '<tr>
              		<td colspan="6">
              		<b>Carrinho Vazio</b>
              		</td>
              	</tr>';
} else {

  $cont = 1;
  $valorTotal = 0;

  foreach ($_SESSION['itens'] as $produtos) {
    //$var = explode(' - ', $produtos);
    $idItem = $produtos['idItem'];
    $CodRefProduto = $produtos['CodRefProduto'];
    $qtde = $produtos['qtde'];
    $nameproduto = $produtos['nameproduto'];
    $valor = $produtos['ValVendItens'] * $qtde;
    $valorTotal = $valorTotal + $valor;

    echo '<tr>
                <td>' . $cont . '</td>
			<td>' . $CodRefProduto . '</td>
			<td>' . $nameproduto . '</td>
      
			<td><input type="hidden" id="iditens" name="idItem[' . $idItem . ']" value="' . $idItem . '" />
			<input type="hidden" id="qtd" name="qtd[' . $idItem . ']" value="' . $qtde . '" /> ' . $qtde . '
      </td>
      <td><input type="hidden" id="valor" name="valor[' . $idItem . ']" value="' . $valor . '">R$:' . number_format($valor, '2', ',', '.') . '</td>
      <td>
			<a href="../../App/Database/remover.php?remover=carrinho&id=' . $idItem . '"><i class="fa fa-trash text-danger"></i></a></td>
			</td>
      
                </tr>';
    $cont = $cont + 1;
  }
  if (isset($valorTotal) != 0) {
    echo '<tr bgcolor="a0a0a0"><td  colspan="4">Total compra</td>
    <td colspan="2"><b>R$:' . number_format($valorTotal, '2', ',', '.');
    echo '</b></td></tr>';
  }
}
