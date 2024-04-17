<?php
require_once '../../App/auth.php';

if (isset($_POST['tabela']) != null) {

	$tabela = $_POST['tabela'];

	require_once '../../App/Models/' . $tabela . '.class.php';

	$id = $_POST['id'];

	if($_POST['status'] == 'on'){
		$value = 1;
	}else{
		$value = 0;
	}
	
	$ob = new $tabela;
	$ob->Ativo($value, $id);
} else {
	$_SESSION['msg'] = 'Error - tabela não encontrada';
}

header('location: ../../views/' . $tabela);