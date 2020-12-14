<?php

use \Output\Output;
use \Provider\Provider;
use \Authentication\Authentication;






/**
* Route get provider dogs for specific username
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_dogs/{username}', function ($request, $response, array $args) {
  $User = new Provider($this->db);
  $Output = new Output();

  $userId = $User->getIdByUsername($args['username']);

  $dogs = $User->getDogs($userId);

  return $Output->response($response, 200, $dogs);
});






/**
* return a dog data from specific id
*
* @return 20# | 40# and response
*
**/

$app->get('/provider_dog/{id}', function ($request, $response, array $args) {
  $User = new Provider($this->db);
  $Output = new Output();

  $res = $User->getDog($args['id']);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Delete a dog data from specific id
*
* @return 20# | 40# and response
*
**/

$app->delete('/provider_dog/{id}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());

  $res = $Provider->deleteDog($args['id']);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Update a dog data from specific id
*
* @return 20# | 40# and response
*
**/

$app->patch('/provider_dog/{id}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $params = $request->getParsedBody();

  $res = $Provider->updateDog($args['id'], $params);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Create a new dog for a user
*
* @return 20# | 40# and response
*
**/

$app->post('/provider_dogs', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("PROVIDER")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Provider = new Provider($this->db, $Authentication->getToken());
  $params = $request->getParsedBody();

  $res = $Provider->createDog($params);

  return $Output->response($response, $res['status'], $res['response']);
});
