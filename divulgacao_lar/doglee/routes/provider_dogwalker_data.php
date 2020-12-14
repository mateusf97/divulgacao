<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;






/**
* Get the private data for a dogwalker
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_dogwalker_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $res = $Provider->getDogwalkerData();

  return $Output->response($response, 200, $res);
});






/**
* Route update the private data for an dogwalker
*
* @return 20# | 40# and response
*
**/

$app->patch('/provider_dogwalker_data', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $params = $request->getParsedBody();

  $res = $Provider->saveDogwalkerData($params);

  return $Output->response($response, $res['status'], $res['response']);
});
