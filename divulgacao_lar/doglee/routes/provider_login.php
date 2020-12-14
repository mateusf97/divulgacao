<?php

use \Output\Output;
use \Provider\Provider;


/**
* Autheticate user
*
* @return 20# | 40# and response
*
**/

$app->post('/provider_login', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $User = new Provider($this->db);
  $Output = new Output();

  $res = $User->login($params);

  return $Output->response($response, $res['status'], $res['response']);
});

