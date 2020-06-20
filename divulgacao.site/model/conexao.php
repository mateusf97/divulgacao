<?php

	$ip = "localhost:3308";
	$user = "root";
	$senha = "123123";

	$db = "vitrini";

	$conexao = mysqli_connect($ip, $user, $senha, $db);

	if (mysqli_connect_errno())
		echo "Erro na conexão: " . mysqli_connect_error();

?>


