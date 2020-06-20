<?php

function getNow() {
	include_once 'model/conexao.php';

	$SQL = "SELECT NOW() AS hora";
	$rs = mysqli_query($conexao, $SQL) or die("login_erro");
	$result = array();
	while ($row = mysqli_fetch_array($rs)){
		$result[] = $row;
	}

	return $result;
}

?>