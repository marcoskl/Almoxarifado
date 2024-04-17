<?php


/**
 * 
 */
require_once 'connect.php';

class Relatorio extends Connect
{

	public function qtdeItensEstoqueTotal($perm)
	{
		if ($perm == 1) {

			$query = "SELECT SUM(`QuantItens`) AS QuantItens , SUM(`QuantItensVend`) AS QuantItensVend FROM `itens`";

			$result = mysqli_query($this->SQL, $query);

			if ($row = mysqli_fetch_assoc($result)) {

				$qi = $row['QuantItens'];
				$qiv = $row['QuantItensVend'];
				$r = $qi - $qiv;
				return $r;
			}
		}
	}

	public function qtdeItensEstoque($perm, $status = null, $idProduto = null)
	{
		if ($perm == 1) {

			if ($idProduto != null) {
				$AND = "AND `Produto_CodRefProduto` = '$idProduto' AND `Ativo` = '$status'";
			} elseif ($status != null) {
				$AND = "AND `Ativo` = '$status'";
			} else {
				$AND = "";
			}


			$query = "SELECT `Produto_CodRefProduto`, `NomeProduto`, SUM(`QuantItens`) AS QuantItens , SUM(`QuantItensVend`) AS QuantItensVend FROM `itens`, `produtos`
				WHERE `Produto_CodRefProduto` = `CodRefProduto`
				$AND
				GROUP BY `Produto_CodRefProduto`";

			$result = mysqli_query($this->SQL, $query);

			while ($row[] = mysqli_fetch_assoc($result));
			return json_encode($row);
		}
	}

	public function selectCliente($perm)
	{
		if ($perm == 1) {

			$query = "SELECT `idCliente`,`NomeCliente` FROM `cliente`";
			$result = mysqli_query($this->SQL, $query);
			while ($row[] = mysqli_fetch_assoc($result));
			return json_encode($row);
		}
	}

	public function selectProduto($perm, $status = null)
	{
		if ($perm == 1) {

			if ($status != null) {
				$where = "WHERE `Ativo` = '$status'";
			} else {
				$where = "";
			}

			$query = "SELECT `CodRefProduto`,`NomeProduto` FROM `produtos` $where";
			$result = mysqli_query($this->SQL, $query);
			while ($row[] = mysqli_fetch_assoc($result));

			return json_encode($row);
		}
	}

	public function vendascliente($perm, $idProduto = null, $idCliente = null)
	{
		if ($perm == 1) {
			if ($idProduto != null && $idCliente != null) {
				$AND = "AND `Produto_CodRefProduto` = '$idProduto' AND `idCliente` = '$idCliente'";
			} elseif ($idProduto != null) {
				$AND = "AND `Produto_CodRefProduto` = '$idProduto'";
			} elseif ($idCliente != null) {
				$AND = "AND `idCliente` = '$idCliente'";
			} else {
				$AND = "";
			}
			$query = "SELECT * FROM vendas, cliente, itens, produtos WHERE cliente_idCliente = idCliente AND idItem = iditens AND Produto_CodRefProduto = CodRefProduto $AND ORDER BY idVendas DESC";
			$result = mysqli_query($this->SQL, $query);
			while ($row[] = mysqli_fetch_assoc($result));

			return json_encode($row);
		}
	}

	public function vendasdiarias($idUsuario, $perm)
	{
		$dt = (new Relatorio)->registrocaixa($idUsuario, $perm);
		$datain = $dt['datain'];
		$dataout = explode(' ', $dt['dataout']);
		if ($dataout[0] == date('Y-m-d')) {
			$dataout = date('Y-m-d H:i:s');
		} else {
			$dataout = date('Y-m-d 23:59:59', strtotime($dataout[0]));
		}

		$query = "SELECT * FROM `vendas` WHERE `datareg` BETWEEN '$datain' AND '$dataout'";
		$result = mysqli_query($this->SQL, $query);
		while ($row[] = mysqli_fetch_assoc($result));
		return json_encode($row);
	}

	public function dataAberturaCaixa($token, $datain, $perm)
	{
		if ($perm == 1) {

			$query = "SELECT * FROM `registrocaixa` WHERE `dataRegistro` >= '$datain' AND `statusCaixa` = 1 AND `token` = '$token' ORDER BY `dataRegistro` ASC";
			$result = mysqli_query($this->SQL, $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$datain = $row['dataRegistro'];
			}
			return $datain;
		}
	}
	public function dataFechamentoCaixa($token, $dataout, $perm)
	{

		if ($perm == 1) {
			if ($dataout == date('Y-m-d')) {
				$dataout = date('Y-m-d H:i:s');
			}

			$query = "SELECT * FROM `registrocaixa` WHERE `dataRegistro` < '$dataout' AND `statusCaixa` = 0 AND `token` = '$token' ORDER BY `dataRegistro` DESC";
			$result = mysqli_query($this->SQL, $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$dataout = $row['dataRegistro'];
			} else {
				$dataout = null;
			}

			return $dataout;
		}
	}

	public function registrocaixaglobal($idUsuario, $datain, $dataout, $perm)
	{
		if ($perm == 1) {
			$gerartoken = $idUsuario . '#' . date('Y-m-d');
			$token = md5($gerartoken);
			$in = (new Relatorio)->dataAberturaCaixaGlobal($datain, $perm);
			$out = (new Relatorio)->dataFechamentoCaixaGlobal($dataout, $perm);
			return array('datain' => $in, 'dataout' => $out);
		}
	}

	public function dataAberturaCaixaGlobal($datain, $perm)
	{
		if ($perm == 1) {

			$query = "SELECT * FROM `registrocaixa` WHERE `dataRegistro` >= '$datain' AND `statusCaixa` = 1 ORDER BY `dataRegistro` ASC";
			$result = mysqli_query($this->SQL, $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$datain = $row['dataRegistro'];
			}
			return $datain;
		}
	}

	public function dataFechamentoCaixaGlobal($dataout, $perm)
	{

		if ($perm == 1) {
			$dataout = explode(' ', $dataout);
			if ($dataout[0] == date('Y-m-d')) {
				$dataout = date('Y-m-d H:i:s');
			} else {
				$dataout = date('Y-m-d 23:59:59', strtotime($dataout[0]));
			}

			$query = "SELECT * FROM `registrocaixa` WHERE `dataRegistro` <= '$dataout' AND `statusCaixa` = 0 ORDER BY `dataRegistro` DESC";
			$result = mysqli_query($this->SQL, $query);
			if ($row = mysqli_fetch_assoc($result)) {
				$dataout = $row['dataRegistro'];
			} else {
				$dataout = null;
			}

			return $dataout;
		}
	}

	public function vendasdataindataout($datain, $dataout, $perm)
	{
		$dataout = explode(' ', $dataout);
		if ($dataout[0] == date('Y-m-d')) {
			$dataout = date('Y-m-d H:i:s');
		} else {
			$dataout = date('Y-m-d 23:59:59', strtotime($dataout[0]));
		}

		$query = "SELECT * FROM `vendas` WHERE `datareg` BETWEEN '$datain' AND '$dataout'";
		$result = mysqli_query($this->SQL, $query);
		while ($row[] = mysqli_fetch_assoc($result));

		return json_encode($row);
	}

	public function registrocaixa($idUsuario, $perm)
	{
		$dataHoje = date('Y-m-d');
		$query = "SELECT * FROM `registrocaixa` WHERE `dataRegistro` >= '$dataHoje' AND `usuario_idUsuario` = '$idUsuario' ORDER BY `idRegistroCaixa` DESC";
		$result = mysqli_query($this->SQL, $query);
		$token = 0;
		while ($row = mysqli_fetch_assoc($result)) {

			$datain = $row['dataRegistro'];
			if ($row['statusCaixa'] == 0) {
				$dataout = $row['dataRegistro'];
			}

			$statusCaixa = $row['statusCaixa'];
			$valor = $row['valoremcaixa'];
			$token = $row['token'];
		}
		return array('datain' => $datain, 'dataout' => $dataout, 'statusCaixa' => $statusCaixa, 'token' => $token, 'valoremcaixa' => $valor);
	}


	public function registroCompraClient($idUsuario, $perm)
	{
		$dt = (new Relatorio)->registrocaixa($idUsuario, $perm);
		$datain = $dt['datain'];

		$dataout = explode(' ', $dt['dataout']);
		if ($dataout[0] == date('Y-m-d')) {
			$dataout = date('Y-m-d H:i:s');
		} else {
			$dataout = date('Y-m-d 23:59:59', strtotime($dataout[0]));
		}
		$query = "SELECT V.cart, SUM(V.valor) AS totalcompra, V.cliente_idCliente, V.datareg, COUNT(V.cart) AS quantitens, L.NomeCliente, C.Tipo FROM vendas AS V, cliente AS L, carteira AS C WHERE L.idCliente = V.cliente_idCliente AND V.cart = C.anexo AND V.cart IN (SELECT `cart` FROM `vendas` WHERE `datareg` BETWEEN '$datain' AND '$dataout') GROUP BY V.cart, V.cliente_idCliente, V.datareg, C.Tipo";
		$result = mysqli_query($this->SQL, $query);
		while ($row[] = mysqli_fetch_assoc($result));
		return json_encode($row);
	}
	public function registroCompraClientglobal($idUsuario, $datain, $dataout, $perm)
	{
		$dt = (new Relatorio)->registrocaixaglobal($idUsuario, $datain, $dataout, $perm);
		$datain = $dt['datain'];

		$dataout = explode(' ', $dt['dataout']);
		if ($dataout[0] == date('Y-m-d')) {
			$dataout = date('Y-m-d H:i:s');
		} else {
			$dataout = date('Y-m-d 23:59:59', strtotime($dataout[0]));
		}

		$query = "SELECT V.cart, SUM(V.valor) AS totalcompra, V.cliente_idCliente, V.datareg, COUNT(V.cart) AS quantitens, L.NomeCliente, C.Tipo FROM vendas AS V, cliente AS L, carteira AS C WHERE L.idCliente = V.cliente_idCliente AND V.cart = C.anexo AND V.cart IN (SELECT `cart` FROM `vendas` WHERE `datareg` BETWEEN '$datain' AND '$dataout') GROUP BY V.cart, V.cliente_idCliente, V.datareg, C.Tipo";
		$result = mysqli_query($this->SQL, $query);
		while ($row[] = mysqli_fetch_assoc($result));
		return json_encode($row);
	}

	public function retiradaCaixa($datain, $dataout, $token)
	{
		$query = "SELECT `idRegistroCaixa`, registrocaixa.dataRegistro, `descricao`, `statusCaixa`, `valoremcaixa`, `token`, `Usuario_idUsuario`, `idUsuario`, `username` FROM `registrocaixa`, `usuario` WHERE registrocaixa.dataRegistro BETWEEN '$datain' AND '$dataout' AND `statuscaixa` = 2 AND `token` = '$token' AND `Usuario_idUsuario` = `idUsuario`";
		$result = mysqli_query($this->SQL, $query);
		while ($row[] = mysqli_fetch_assoc($result));
		return json_encode($row);
	}

	public function retiradaCaixaGlobal($datain, $dataout)
	{
		$query = "SELECT `idRegistroCaixa`, registrocaixa.dataRegistro, `descricao`, `statusCaixa`, `valoremcaixa`, `token`, `Usuario_idUsuario`, `idUsuario`, `username` FROM `registrocaixa`, `usuario` WHERE registrocaixa.dataRegistro BETWEEN '$datain' AND '$dataout' AND `statuscaixa` = 2 AND `Usuario_idUsuario` = `idUsuario`";
		$result = mysqli_query($this->SQL, $query);
		while ($row[] = mysqli_fetch_assoc($result));
		return json_encode($row);
	}
}
