<?php


  function validaDados($data) {
	  if(strlen($data['login']) < 3) {
      return false;
    }

    if(strlen($data['password']) < 3) {
      return false;
    }

    if(strlen($data['nome']) < 3) {
      return false;
    }

    return true;
  }

  function verificaDuplicata($data) {
    include '../model/conexao.php';

    $SQL = "SELECT count(*) AS total FROM users WHERE login = '" . $data['login'] ."'";
    $rs = mysqli_query($conexao, $SQL) or die("login_erro");
    $result = array();

    while ($row = mysqli_fetch_array($rs)) {
      $data[] = $row;
    }

    $result['num_rows'] = count($data);
    $result['row'] = isset($data[0]) ? $data[0] : array();
    $result['rows'] = $data;

    // Retorna quantidade de usuários com o login especificado

    return (boolean) $result['row']['total'];
  }

  function criaUsuario($data) {
    include '../model/conexao.php';

    $SQL = "INSERT INTO users SET type = '" . $data['type'] . "', name = '" . $data['nome'] . "', login = '" . $data['login'] . "', password = '" . $data['password'] . "';";

    $rs = mysqli_query($conexao, $SQL) or die("error criacao de usuario");

    $SQL = "SELECT id FROM users ORDER BY id DESC limit 1";
    $rs = mysqli_query($conexao, $SQL) or die("login_erro");
    $result = array();

    while ($row = mysqli_fetch_array($rs)) {
      $data[] = $row;
    }

    $result['num_rows'] = count($data);
    $result['row'] = isset($data[0]) ? $data[0] : array();

    return $result['row']['id'];
  }



?>