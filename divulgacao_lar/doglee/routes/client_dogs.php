<?php

use \Output\Output;
use \Client\Client;
use \Authentication\Authentication;






/**
* Route get list of specific client dogs
*
* @return 200 and dog list
*
**/

$app->get('/client_dogs/{username}', function ($request, $response, array $args) {

  $Client = new Client($this->db);
  $Output = new Output();

  $userId = $Client->getIdByUsername($args['username']);
  $dogs = $Client->getDogs($userId);

  return $Output->response($response, 200, $dogs);
});






/**
* Route get list of specific dog id
*
* @return 20# | 40# and response
*
**/

$app->get('/client_dog/{id}', function ($request, $response, array $args) {

  $Client = new Client($this->db);
  $Output = new Output();

  $res = $Client->getDog($args['id']);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Delete a dog data from specific id
*
* @return 20# | 40# and response
*
**/

$app->delete('/client_dog/{id}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());
  $res = $Client->deleteDog($args['id']);

  return $Output->response($response, $res['status'], $res['response']);
});






/**
* Update a dog data from specific id
*
* @return 20# | 40# and response
*
**/

$app->patch('/client_dog/{id}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();


  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());

  $params = $request->getParsedBody();
  $res = $Client->updateDog($args['id'], $params);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Create a new dog for a user
*
* @return 20# | 40# and response
*
**/

$app->post('/client_dogs', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());

  $params = $request->getParsedBody();
  $res = $Client->createDog($params);

  return $Output->response($response, $res['status'], $res['response']);
});
