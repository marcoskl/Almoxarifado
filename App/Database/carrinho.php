<?php
require_once '../auth.php';
require_once('../Models/itens.class.php');

if (isset($_POST['prodSubmit']) != null && $_POST['prodSubmit'] == "carrinho") {

	if (isset($_POST["idItem"]) != null) {

		$idProd = explode(' - ', $_POST["idItem"]);

		$resp = (new Itens)->itemSelect($idProd[0]);

		if (isset($resp[0]['idItens']) != null) {
			$idProduto = $resp[0]['idItens'];
			$CodRefProduto = isset($resp[0]['CodRefProduto']) ? $resp[0]['CodRefProduto'] : '';
			$nameprod = isset($resp[0]['NomeProduto']) ? $resp[0]['NomeProduto'] : 'Nome não encontrado';
			$ValVendItens = isset($resp[0]['ValVendItens']) ? $resp[0]['ValVendItens'] : null;
			$qtde = $_POST['qtde'];

			if (!empty($idProduto) && !empty($qtde)) {

				$var = array('idItem' => $idProduto, 'CodRefProduto' => $CodRefProduto, 'qtde' => $qtde, 'ValVendItens' => $ValVendItens, 'nameproduto' => $nameprod);

				if (!isset($_SESSION['itens'][$idProduto])) {
					$_SESSION['itens'][$idProduto] = $var;
				} else {
					$_SESSION['itens'][$idProduto] = $var;
				}
			}
		}
		$pkCount = (is_array($_SESSION['itens']) ? count($_SESSION['itens']) : 0);

		if ($pkCount == 0) {
			echo ' Carrinho Vazios</br> ';
		} else {

			$cont = 1;
			$valorTotal = 0;

			foreach ($_SESSION['itens'] as $produtos) {

				$idItem = $produtos['idItem'];
				$qtde = $produtos['qtde'];
				$CodRefProduto = $produtos['CodRefProduto'];
				$nameproduto = $produtos['nameproduto'];
				$valor = $produtos['ValVendItens'] * $qtde;
				$valorTotal = $valorTotal + $valor;

				echo '<tr>
					<td>' . $cont . '</td>
					<td>' . $CodRefProduto . '</td>
					<td>' . $nameproduto . '</td>					
					<td><input type="hidden" id="iditens" name="idItem[' . $idItem . ']" value="' . $idItem . '" />
					<input type="hidden" id="qtd" name="qtd[' . $idItem . ']" value="' . $qtde . '" />' . $qtde . '
					</td>
					<td>R$:' . number_format($valor, '2', ',', '.') . '</td>
					<td>
					<a href="../../App/Database/remover.php?remover=carrinho&id=' . $idItem . '"><i class="fa fa-trash text-danger"></i></a></td>
					</tr>';
				$cont = $cont + 1;
			}
			if (isset($valorTotal) != 0) {
				echo '<tr bgcolor="a0a0a0"><td  colspan="4">Total compra</td>
				<td colspan="2"><b>R$:' . number_format($valorTotal, '2', ',', '.');
				echo '</b></td></tr>';
			}
		}
	}
}
