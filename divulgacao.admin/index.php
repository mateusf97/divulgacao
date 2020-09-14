<?php

	require_once 'controller/header.php';

	if (!isset($_GET['hash']) || !isset($_GET['page'])) {
		header('Location: ?page=login&hash=');
	}


	if (isset($_GET['page'])) {
		$page = 'controller/'. $_GET['page'] . '.php';
	} else {
		$page = 'controller/home.php';
	}

	if (file_exists($page)) {
		require_once $page;
	} else {
		require_once "controller/page404.php";
	}

	require_once 'controller/footer.php';
?>