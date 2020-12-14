<?php

use \Output\Output;
use \Client\Client;
use \Provider\Provider;
use \Authentication\Authentication;








/**
* Get favorite list for authenticated user
*
* @return 200 | 40#
*
**/

$app->get('/favorites', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("CLIENT")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());
  $res = $Client->getFavorites();

  return $Output->response($response, $res['status'], $res['response']);
});








/**
* Delete a favorite for authenticated user
*
* @return 200 | 40#
*
**/

$app->delete('/favorite/{username}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("CLIENT")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());
  $res = $Client->deleteFavorite($args['username']);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Set new favorite for authenticated user
*
* @return 201 | 40#
*
**/

$app->post('/favorite/{username}', function ($request, $response, array $args) {

  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("CLIENT")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $Client = new Client($this->db, $Authentication->getToken());
  $res = $Client->addFavorite($args['username']);

  return $Output->response($response, $res['status'], $res['response']);
});
