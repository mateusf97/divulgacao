<?php

use \Output\Output;
use \User\User;
use \Products\Products;
use \Authentication\Authentication;
use Slim\Http\UploadedFile;

/**
* user login
*
* @return json - User authenticated data
*
**/

$app->get('/product/{id}', function ($request, $response, array $args) {

  $Products = new Products($this->db);
  $Output = new Output();

  $res = $Products->get($args['id']);

  return $Output->response($response, $res['status'], $res['response']);
});


/**
* user login
*
* @return json - User authenticated data
*
**/

$app->get('/products', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $Products = new Products($this->db);
  $Output = new Output();

  $res = $Products->list();

  return $Output->response($response, $res['status'], $res['response']);
});


/**
* user login
*
* @return json - User authenticated data
*
**/

$app->post('/upload', function ($request, $response, array $args) {

  $Output = new Output();
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $file = array();

  $file['type'] = explode("/", $_FILES['file']['type'])[1];
  $file['tmp_name'] = $_FILES['file']['tmp_name'];
  $file['name'] = md5(uniqid(rand(100, 100000) . '' . time(), true)) . md5(uniqid(rand(100, 100000) . '' . time(), true)) . '.' .  $file['type'];
  $file['location'] = $this->get('upload_directory') . $file['name'];

  move_uploaded_file($file['tmp_name'], $file['location']);

  if (file_exists($file['location'])) {
    $res['status'] = 200;
    $res['response'] = $file['location'];
  } else {
    $res['status'] = 400;
    $res['response'] = "Error";
  }

  return $Output->response($response, $res['status'], $res['response']);

});


/**
* user login
*
* @return json - User authenticated data
*
**/

$app->post('/products', function ($request, $response, array $args) {

  $Products = new Products($this->db);
  $Output = new Output();

  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $params = $request->getParsedBody();
  $res = $Products->create($params);

  if ($res["status"] != 201) {
    unlink($params['image_url']);
  }

  return $Output->response($response, $res['status'], $res['response']);
});


/**
* user login
*
* @return json - User authenticated data
*
**/

$app->post('/update_product/{id}', function ($request, $response, array $args) {

  $Products = new Products($this->db);
  $Output = new Output();

  $params = $request->getParsedBody();

  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $res = $Products->update($params, $args['id']);

  if ($res["status"] != 201) {
    unlink($params['image_url']);
  }

  return $Output->response($response, $res['status'], $res['response']);
});



/**
* user login
*
* @return json - User authenticated data
*
**/

$app->delete('/product', function ($request, $response, array $args) {

  $Products = new Products($this->db);
  $Output = new Output();

  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $params = $request->getParsedBody();
  $res = $Products->delete($params);


  return $Output->response($response, $res['status'], $res['response']);
});

