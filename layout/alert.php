<?php
if (isset($_GET['alert']) != NULL || isset($_SESSION['msg']) != NULL) {
	if (isset($_GET['alert']) != NULL) {
		$value = $_GET['alert'];
	} else {
		$data = $_SESSION['msg'];
		$value = 'status';
	}
	switch ($value) {

		case 'status':
			if (isset($data['status']) == 1) {
				echo '<div class="alert alert-info alert-dismissible">
<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
 <strong>Ops!</strong> O Produto <b>' . $data['NomeProduto'] . '</b> não pode ser vendido nessa quantidade! <br/> Quantidade em estoque <b>' . $data['estoque'] . '. </b><br/></div>';
			}
			break;


		case '0':
			echo '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-warning"></i> Error! Operação não efetuada tente novamente.</h4>
                </div>';
			break;

		case '1':
			echo '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Operação realizada com sucesso!</h4>
                
              </div>';
			break;

		case '2':
			echo '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-warning"></i> Error! Cliente já cadastrado.</h4>
                </div>';
			break;
		case '3':
			echo '<div id="success-alert" class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Usúario ou Senha invalidos!</h4>
			</div>';
			break;
		case '4':
			echo '<div id="success-alert" class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Senha Atual invalida!</h4>
	
			</div>';
			break;
		case '5':
			echo '<div id="success-alert" class="alert alert-danger alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Novas senhas incompatíveis!</h4>
	
			</div>';
			break;

		case '6':
			echo '<div id="success-alert" class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Sucesso! Verifique seu E-mail para alterar a senha!';
			echo '</h4></div>';
			break;

		case '7':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Ops! Ocorreu um erro tente novamente mais tarde!</h4>
			</div>';
			break;


		case '8':
			echo '<div id="success-alert" class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Sucesso! Verifique seu E-mail para efetuar o pagamento!';
			echo '</h4></div>';
			break;

		case '9':
			echo '<div id="success-alert" class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Senha alterar com sucesso! Efetue o login!';
			echo '</h4></div>';
			break;

		case '10':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Ops! Ocorreu um erro, tente novamente!.</h4>
			</div>';
			break;
		case '11':
			echo '<div id="success-alert" class="alert alert-success alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i> Arquivo anexado com sucesso!';
			echo '</h4></div>';
			break;

		case '12':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Ops! Extensão do arquivo não permitida!.</h4>
			</div>';
			break;
		case '13':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Ops! O valor informado é inválido!</h4>
			</div>';
			break;
		case '14':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Ops! Usuário inválido!</h4>
			</div>';
			break;
		case '15':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-warning"></i> Ops! Usuário Bloqueado!</h4>
			</div>';
			break;
		case '16':
			echo '<div id="success-alert" class="alert alert-warning alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
				<h4><i class="icon fa fa-warning"></i> Ops! É necessário incluir um comprovante, tente novamente!.</h4>
				</div>';
			break;
	} //switch


	unset($_GET['alert'], $_SESSION['alert'], $_SESSION['msg']);
}
