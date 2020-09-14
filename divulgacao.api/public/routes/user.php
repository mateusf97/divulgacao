<?php

use \Output\Output;
use \User\User;
use \Authentication\Authentication;


/**
* user login
*
* @return json - User authenticated data
*
**/

$app->post('/login', function ($request, $response, array $args) {
  $params = $request->getParsedBody();

  $User = new User($this->db);
  $Output = new Output();

  $res = $User->login($params);

  return $Output->response($response, $res['status'], $res['response']);
});



/**
* User register
*
* @return 20# | 40# and response
*
**/




$app->post('/users', function ($request, $response, array $args) {

  $params = $request->getParsedBody();

  $User = new User($this->db);
  $Output = new Output();

  $res = $User->create($params);

  return $Output->response($response, $res['status'], $res['response']);
});



/**
* Updated password for authenticated user
*
* @return 20# | 40# and response
*
**/

$app->patch('/password', function ($request, $response, array $args) {
  $Output = new Output();

  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $User = new User($this->db, $request->getHeader('Authorization'));
  $params = $request->getParsedBody();

  if (!isset($params['old_password'])) {
    return $Output->response($response, 403, 'MISSING field "old_password"');
  }

  $oldAuthentication = array(
    'login' => $User->getEmail(),
    'password' => $params['old_password']
  );

  /**
  * @var $res['status'] = 200: Authenticad, so old password is valid
  */

  $res = $User->login($oldAuthentication);
  $authenticated = ($res['status'] === 200);

  if (!$authenticated) {
    return $Output->response($response, $res['status'], $res['response']);
  }

  if (!isset($params['new_password'])) {
    return $Output->response($response, 403, 'MISSING field "new_password"');
  }

  $res = $User->changePassword($params['new_password']);

  return $Output->response($response, $res['status'], $res['response']);
});

