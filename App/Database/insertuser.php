<?php
require_once '../auth.php';
require_once '../Models/usuario.class.php';

$username  = $_POST['username'];
$email = $_POST['email'];

$permissao = $_POST['permissao'];


if ($username != NULL && $perm == 1 || isset($_POST['idUser']) == $idUsuario) {

	if (!file_exists($_FILES['arquivo']['name'])) {

		$pt_file =  '../../views/dist/img/' . $_FILES['arquivo']['name'];

		if (!empty($_FILES['arquivo']['name'])) {
			$extensao = pathinfo($_FILES['arquivo']['name']);
			$extensao = "." . strtolower($extensao['extension']);
		}
		if ($extensao == '.jpeg' || $extensao == '.jpg' || $extensao == '.png') {

			if ($pt_file != '../../views/dist/img/') {
				$imagem = time() . uniqid(md5($_POST['username'])) . $extensao;
				$destino =  '../../views/dist/img/' . $imagem;

				$destino =  '../../views/dist/img/' . $_FILES['arquivo']['name'];
				$arquivo_tmp = $_FILES['arquivo']['tmp_name'];
				move_uploaded_file($arquivo_tmp, $destino);
				chmod($destino, 0644);

				$nomeimagem =  'dist/img/' . $_FILES['arquivo']['name'];
			} elseif (isset($_POST['valor']) != NULL) {
				$nomeimagem = isset($_POST['valor']) ?? 'dist/img/avatar.png';
			} else {
				$nomeimagem = 'dist/img/avatar.png';
			}
		} else {
			$nomeimagem = 'dist/img/avatar.png';
		}
	}

	if (isset($_POST['idUser']) != NULL) {
		$idUser = $_POST['idUser'];
		if ($perm == 1) {
			$resp = (new Usuario)->updateUser($idUser, $username, $email, $nomeimagem, $permissao);
		} else {
			$resp = (new Usuario)->updateUser($idUser, $username, $email, $nomeimagem);
		}
	} else {
		$password = $_POST['password'];
		$resp = (new Usuario)->insertUser($username, $email, $password, $nomeimagem, $permissao, $perm);
	}
	$_SESSION['alert'] = $resp;
} else {
	$_SESSION['alert'] = 0;
}
header('location: ../../views/usuarios/');
