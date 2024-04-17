<?php
require_once '../auth.php';
require_once '../Models/fabricante.class.php';

if (isset($_POST['upload']) == 'Cadastrar') {

	$NomeFabricante = $_POST['NomeFabricante'];

	//---Fabricante---//
	$CNPJFabricante = $_POST['CNPJFabricante'];
	$EmailFabricante = $_POST['EmailFabricante'];
	$EnderecoFabricante = $_POST['EnderecoFabricante'];
	$TelefoneFabricante = $_POST['TelefoneFabricante'];
	$ativo = $_POST['ativo'];
	$status = 1;

	//--Representante--//


	$iduser = $_POST['iduser'];

	if ($iduser == $idUsuario && $NomeFabricante != NULL) {

		if (!isset($_POST['idFabricante'])) {

			$NomeRepresentante = $_POST['NomeRepresentante'];
			$TelefoneRepresentante = $_POST['TelefoneRepresentante'];
			$EmailRepresentante = $_POST['EmailRepresentante'];
			$resp = (new Fabricante)->InsertFabricante($NomeFabricante, $CNPJFabricante, $EmailFabricante, $EnderecoFabricante, $TelefoneFabricante, $idUsuario,  $NomeRepresentante, $TelefoneRepresentante, $EmailRepresentante, $status, $perm);
		} else {
			$idFabricante = $_POST['idFabricante'];
			$resp = (new Fabricante)->UpdateFabricante($idFabricante, $NomeFabricante, $CNPJFabricante, $EmailFabricante, $EnderecoFabricante, $TelefoneFabricante, $ativo, $idUsuario, $perm);
		}
		$_SESSION['alert'] = $resp;
	} else {
		$_SESSION['alert'] = 3;
	}
}

header('Location: ../../views/fabricante/');
