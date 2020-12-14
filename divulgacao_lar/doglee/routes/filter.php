<?php

use \Output\Output;
use \Provider\Provider;






/**
* Route get list of user based on filter
*
* @return 20# | 40# and response
*
**/

$app->get('/filter', function ($request, $response, array $args) {

  $Provider = new Provider($this->db);
  $Output = new Output();

  $filter = $request->getQueryParams();
  $res = $Provider->getProviderList($filter);

  return $Output->response($response, $res['status'], $res['response']);
});
