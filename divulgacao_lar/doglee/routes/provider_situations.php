<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;


/**
* Get all Provider status
*
* @return array
*
**/

$app->get('/provider_situations', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());

  $res = $Provider->getSituations();

  return $Output->response($response, $res['status'], $res['response']);
});



