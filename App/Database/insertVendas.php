<?php
require_once '../../App/auth.php';
require_once '../../App/Models/connect.php';
require_once '../../App/Models/vendas.class.php';
if (
    isset($_POST['idItem']) > 0 &&
    isset($_POST['qtd']) != NULL && (isset($_POST['nomeCliente']) != NULL &&
        isset($_POST['emailCliente']) != NULL &&
        isset($_POST['cpfcliente']) != NULL)
) {



    $cliente = $_POST['nomeCliente'];
    $email = $_POST['emailCliente'];
    $pagamento = $_POST['pagamento'];
    $cpfCliente = (new Connect)->limpaCPF_CNPJ($_POST['cpfcliente']);
    $cart = $_SESSION['cart'];

    foreach ($_POST['idItem'] as $key => $error) {

        $id = $_POST['idItem'][$key];
        $quant = $_POST['qtd'][$key];

        $vendas = new Vendas;
        $result = $vendas->itensVerify($id, $quant, $perm);

        if ($result['status'] == 0) {

            $_SESSION['msg'] = $result;
            header('Location: ../../views/vendas/');
            exit();
        }
    }



    foreach ($_POST['idItem'] as $key => $error) {

        $id = $_POST['idItem'][$key];
        $quant = $_POST['qtd'][$key];
        $block = !empty($_POST['block']) ?? null;
        (new Vendas)->itensVendidos($id, $quant, $cliente, $email, $cpfCliente, $cart, $idUsuario, $perm, $pagamento, $block);
    }
} else {
    $_SESSION['alert'] = 0;
    header('Location: ../../views/vendas/');
}
