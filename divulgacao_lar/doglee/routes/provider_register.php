<?php

use \Output\Output;
use \Provider\Provider;


/**
* Route to register a new host provider
*
* @return json - New user data
*
**/

$app->post('/provider_host_register', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $User = new Provider($this->db);
  $Output = new Output();

  $res = $User->create($params, 'HOST');

  return $Output->response($response, $res['status'], $res['response']);
});


/**
* Route to register a new dogwalker provider
*
* @return json - New user data
*
**/

$app->post('/provider_dogwalker_register', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $User = new Provider($this->db);
  $Output = new Output();

  $res = $User->create($params, 'DOGWALKER');

  return $Output->response($response, $res['status'], $res['response']);
});
