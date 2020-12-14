<?php

use \Output\Output;
use \Client\Client;
use \Provider\Provider;
use \Authentication\Authentication;



/**
* Updated password for authenticated user
*
* @return 20# | 40# and response
*
**/

$app->patch('/password', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $authenticationType = $Authentication->getAuthenticationType();
  $params = $request->getParsedBody();

  if ($authenticationType === "PROVIDER") {
    $Provider = new Provider($this->db, $Authentication->getToken());

    $oldAuthentication = array(
      'login' => $Provider->getCpf(),
      'password' => $params['old_password']
    );

    /**
    * @var $res['status'] = 200: Authenticad, so old password is valid
    */

    $res = $Provider->login($oldAuthentication);
    $authenticated = ($res['status'] === 200);

    if (!$authenticated) {
      return $Output->response($response, $res['status'], $res['response']);
    }

    $res = $Provider->changePassword($params);
  }

  if ($authenticationType === "CLIENT") {
    $Client = new Client($this->db, $Authentication->getToken());

    $oldAuthentication = array(
      'login' => $Client->getCpf(),
      'password' => $params['old_password']
    );

    /**
    * @var $res['status'] = 200: Authenticad, so old password is valid
    */

    $res = $Client->login($oldAuthentication);
    $authenticated = ($res['status'] === 200);

    if (!$authenticated) {
      return $Output->response($response, $res['status'], $res['response']);
    }

    $res = $Client->changePassword($params);
  }

  return $Output->response($response, $res['status'], $res['response']);
});
