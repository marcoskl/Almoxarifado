<?php
session_start(); //Iniciando a sessão

if (!isset($_SESSION["idUsuario"]) || !isset($_SESSION["usuario"])) {
	header('Location: ../');
} else {
	date_default_timezone_set('America/Sao_Paulo');
	$timezone = new DateTime(date('Y-m-d H:i:s'));
	$timezone->setTimeZone(new DateTimeZone("America/Sao_Paulo"));

	$idUsuario = $_SESSION["idUsuario"];
	$username  = $_SESSION["usuario"];
	$perm	     = $_SESSION["perm"];
	$foto      = $_SESSION["foto"];
	$site_url = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
}
