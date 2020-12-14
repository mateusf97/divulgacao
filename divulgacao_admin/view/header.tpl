<!DOCTYPE html>
<html>
<head>
  <title>Sergio's Showcase Admin</title>
  <link rel="stylesheet" type="text/css" href="css/normalize.css">
  <link rel="stylesheet" type="text/css" href="css/grid.css">
  <link rel="stylesheet" type="text/css" href="css/body.css">
  <link rel="stylesheet" type="text/css" href="css/custom.css">
  <link rel="icon" href="images/back.svg" />
  <script type="text/javascript" src="scripts/jquery-3.4.1.min.js"></script>
  <script type="text/javascript" src="scripts/jquery-validation.js"></script>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <input type="hidden" value="<?php echo $hash; ?>" id="hash">
</head>
<body>

  <div class="header">
    <div class="row text-center">
      <div class="columns medium-12 small-12 header-image">
        <a href="?page=home&hash=<?=$hash;?>"><img src="images/logo.svg"></a>
      </div>
    </div>
  </div>



  <div class="body">
    <div class="menu-admin text-center">
      <button><a href="?page=home&hash=<?php echo $hash; ?>">Ver Todos</a></button>
      <button><a href="?page=novo&hash=<?php echo $hash; ?>">Adicionar novo</a></button>
    </div>

