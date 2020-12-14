<?php

use \Output\Output;
use \Client\Client;
use \Authentication\Authentication;




/**
* Route get the private data for a specific client
*
* @return 200 | 40# and response
*
**/

$app->get('/client_public_data/{username}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  /**
  *
  * user needs to be a provider to continue
  */

  $Client = new Client($this->db);
  $userId = $Client->getIdByUsername($args['username']);

  if (!$userId) {
    return $Output->response($response, 400, null);
  }

  $data = $Client->getClientPublicData($userId);

  return $Output->response($response, 200, $data);
});
