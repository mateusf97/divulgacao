<?php
require '../vendor/autoload.php';

$config['displayErrorDetails'] = true;
$config['debug']               = true;

  // DATABASE
$config['db']['host']   = '127.0.0.1';
$config['db']['user']   = 'root';
$config['db']['pass']   = '123123';
$config['db']['dbname'] = 'showcase';

$app = new \Slim\App(['settings' => $config]);

$app->options('/{routes:.+}', function ($request, $response, $args) {
  return $response;
});

$app->add(function ($req, $res, $next) {
  $response = $next($req, $res);

  return $response
  ->withHeader('Access-Control-Allow-Origin', '*')
  ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
  ->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, OPTIONS');
});

$container = $app->getContainer();

$container['db'] = function ($c) {
  $db = $c['settings']['db'];
  $pdo = new PDO('mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'],
   $db['user'], $db['pass']);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

  return $pdo;
};

/**
* Constants Autoload file
*
*/

if (is_file('./app/constants.php')) {
  require './app/constants.php';
}

/**
* Autoload for routes.
* @var $path contains a file path to be required
*
*/

foreach (scandir(dirname('./routes/routes')) as $folder) {
  $file = dirname('./routes/routes') . '/' . $folder;

  if (is_file($file)) {
    require $file;
  }
}

/**
* Autoload for models.
* @var $file contains a file file to be required
*
*/

foreach (scandir(dirname('./model/model')) as $folder) {
  $file = dirname('./model/model') . '/' . $folder;

  if (is_file($file)) {
    require $file;
  }
}

$app->run();
