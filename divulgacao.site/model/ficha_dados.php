<?php

function verificaCPF($cpf) {
	require 'model/conexao.php';
  require_once 'model/utils.php';

  extractNumbers($cpf);

	$SQL = "SELECT count(*) as total FROM user WHERE CPF = '" . $cpf . "'";
  $rs = mysqli_query($conexao, $SQL);

  while ($row = mysqli_fetch_array($rs)){
    $result = $row;
  }

  return (boolean) $result['total'];
}

function buscaClient($cpf) {
  require 'model/conexao.php';
  require_once 'model/utils.php';

  extractNumbers($cpf);

  $SQL = "SELECT * FROM user WHERE CPF = '" . $cpf . "'";
  $rs = mysqli_query($conexao, $SQL);

  while ($row = mysqli_fetch_array($rs)){
    $result[] = $row;
  }

  return $result[0];
}

?>