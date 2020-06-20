<?php

function newClient($data) {
	require 'model/conexao.php';
  require 'model/utils.php';

  extractNumbers($data['CPF']);

	$SQL = "INSERT INTO user SET name = '" . $data['nome'] . "',  occupation = '" . $data['cargo'] . "', CPF = '" . $data['CPF'] . "', profile = '" . $data['perfil'] . "', state = '" . $data['estado'] . "', city = '" . $data['cidade'] . "', created_by = '" . $data['responsavel'] . "'";

	if (!mysqli_query($conexao, $SQL)) {
    echo "CPF já existe, volte a tela inicial e tente gerar os dados com esse CPF.";
    exit();
  }
}

?>