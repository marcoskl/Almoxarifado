<?php
require_once '../auth.php';
require_once '../Models/produtos.class.php';

if (isset($_POST['codrefproduto']) != null && isset($_POST['nomeproduto']) != null && isset($_POST['update']) == 'Cadastrar') {

	$codrefproduto = $_POST['codrefproduto'];
	$nomeProduto = $_POST['nomeproduto'];

	if (isset($_POST['id']) != NULL && $idUsuario != NULL) {
		$id = $_POST['id'];
		$resp = (new Produtos)->UpdateProd($id, $codrefproduto, $nomeProduto, $idUsuario);
	} else {
		$resp = (new Produtos)->InsertProd($codrefproduto, $nomeProduto, $idUsuario);
	}
	$_SESSION['alert'] = $resp;
} else {
	$_SESSION['alert'] = 0;
}
header('Location: ../../views/produtos/');