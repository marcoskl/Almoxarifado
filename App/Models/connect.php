<?php

/**
 * Conexão com o banco de dados
 */
class Connect
{

	var $localhost = "localhost";
	var $root = "root"; // Aqui vai o nome do usuário do seu Banco de dados MySQL.
	var $passwd = "";   // Aqui vai a senha do seu Banco de dados MySQL.
	var $database = "controlvendas";
	var $SQL;

	public function __construct()
	{
		$this->SQL = mysqli_connect($this->localhost, $this->root, $this->passwd);
		mysqli_select_db($this->SQL, $this->database);
		if (!$this->SQL) {
			die("Conexão com o banco de dados falhou!:" . mysqli_connect_error($this->SQL));
		}
	}

	function login($username, $password)
	{

		$query  = "SELECT * FROM `usuario` WHERE `username` = '$username'";
		$result = mysqli_query($this->SQL, $query) or die(mysqli_error($this->SQL));
		$total  = mysqli_num_rows($result);

		if ($total) {

			$dados = mysqli_fetch_array($result);

			if (password_verify($password, $dados['password'])) {

				$_SESSION['idUsuario'] = $dados['idUsuario'];
				$_SESSION['usuario']   = $dados['username'];
				$_SESSION['perm']      = $dados['permissao'];
				$_SESSION['foto']      = $dados['imagem'];

				header("Location: ../views/");
			} else {
				header("Location: ../login.php?alert=3");
			}
		} else {
			header("Location: ../login.php?alert=3");
		}
	}

	function limpaCPF_CNPJ($valor)
	{

		$valor = trim($valor);
		$valor = str_replace(".", "", $valor);
		$valor = str_replace(",", "", $valor);
		$valor = str_replace("-", "", $valor);
		$valor = str_replace("/", "", $valor);
		return $valor;
	}
	function format_CPF($nbr_cpf)
	{

		$parte_um     = substr($nbr_cpf, 0, 3);
		$parte_dois   = substr($nbr_cpf, 3, 3);
		$parte_tres   = substr($nbr_cpf, 6, 3);
		$parte_quatro = substr($nbr_cpf, 9, 2);

		$monta_cpf = "$parte_um.$parte_dois.$parte_tres-$parte_quatro";

		return $monta_cpf;
	}

	function format_moeda($valor)
	{
		return 'R$' . number_format($valor, 2, ',', '.');
	}

	function mask($val, $mask)
	{
		$maskared = '';
		$k = 0;
		for ($i = 0; $i <= strlen($mask) - 1; $i++) {
			if ($mask[$i] == '0') {
				if (isset($val[$k]))
					$maskared .= $val[$k++];
			} else {
				if (isset($mask[$i]))
					$maskared .= $mask[$i];
			}
		}
		return $maskared;
	}
	function logs($idUsuario, array $desc)
	{

		$description = $desc['description'];
		$tokin = $desc['tokin'];

		$query = "INSERT INTO `logs`(`description`, `tokin`, `usuario_idUsuario`) VALUES ('$description','$tokin', '$idUsuario')";
		mysqli_query($this->SQL, $query);
		return 'log Register';
	}
	function convertMysql($value)
	{
		$value = mysqli_real_escape_string($this->SQL, $value);
		return $value;
	}
	public static function slugify($text, string $divider = '-')
	{
		// replace non letter or digits by divider
		$text = preg_replace('~[^\pL\d]+~u', $divider, $text);

		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		// trim
		$text = trim($text, $divider);

		// remove duplicate divider
		$text = preg_replace('~-+~', $divider, $text);

		// lowercase
		$text = strtolower($text);

		if (empty($text)) {
			return 'n-a';
		}

		return $text;
	}
}
