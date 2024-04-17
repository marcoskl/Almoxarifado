<?php
require_once '../auth.php';
require_once '../Models/fabricante.class.php';

if (isset($_POST['update']) == 'Cadastrar') {

	$idFabricante = $_POST['idFabricante'];

	$resp = (new Fabricante)->DelFabricante($idFabricante, $perm);
	$_SESSION['alert'] = $resps;
} else {
	$_SESSION['alert'] = 0;
}

header('Location: ../../views/fabricante/');
