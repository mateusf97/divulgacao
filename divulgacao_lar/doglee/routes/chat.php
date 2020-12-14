<?php

use \Output\Output;
use \Client\Client;
use \Provider\Provider;
use \Chat\Chat;
use \Validator\Validator;
use \Authentication\Authentication;






/**
* Route create a new chat between client and provider
*
* @param: receptor, message
* @return json
*
**/

$app->post('/chat/{receptor}/new', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid("CLIENT")) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $params = $request->getParsedBody();
  $receptor = $args['receptor'];

  $Chat = new Chat($this->db, $this->db_chat);
  $res = $Chat->createNewChat($params, $receptor, $Authentication);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Route create a new chat between client and provider
*
* @param: receptor, message
* @return json
*
**/

$app->post('/chat/{receptor}/send', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $params = $request->getParsedBody();
  $receptor = $args['receptor'];

  $Chat = new Chat($this->db, $this->db_chat);
  $res = $Chat->sendMessage($params, $receptor, $Authentication);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Route get all chats ids and info for authenticated user
*
*
* @return json
*
**/

$app->get('/chats', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $authenticationType = $Authentication->getAuthenticationType();
  $chatList = array();

  $Chat = new Chat($this->db, $this->db_chat);

  if ($authenticationType === "CLIENT") {
    $Client = new Client($this->db, $Authentication->getToken());
    $chatList = $Chat->getClientChats($Client->getId());
  }

  if ($authenticationType === "PROVIDER") {
    $Provider = new Provider($this->db, $Authentication->getToken());
    $chatList = $Chat->getProviderChats($Provider->getId());
  }

  return $Output->response($response, 200, $chatList);
});







/**
* Route get all chats messages from specific chat
*
*
* @return json
*
**/

$app->get('/chat/{receptor}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $authenticationType = $Authentication->getAuthenticationType();

  if ($authenticationType === "CLIENT") {
    $Client = new Client($this->db, $Authentication->getToken());
    $senderId = $Client->getId();
  }

  if ($authenticationType === "PROVIDER") {
    $Provider = new Provider($this->db, $Authentication->getToken());
    $senderId = $Provider->getId();
  }

  $receptor = $args['receptor'];

  $Chat = new Chat($this->db, $this->db_chat);
  $res = $Chat->getChatMessages($receptor, $senderId, $authenticationType);

  return $Output->response($response, $res['status'], $res['response']);
});







/**
* Route delete selected messages from specific chat
*
*
* @return json
*
**/

$app->delete('/chat/{receptor}/{message_id}', function ($request, $response, array $args) {
  $Authentication = new Authentication($this->db, $request->getHeader('Authorization'));
  $Output = new Output();

  if (!$Authentication->isValid()) {
    return $Output->response($response, 403, 'NOT_ATHENTICATED');
  }

  $authenticationType = $Authentication->getAuthenticationType();

  if ($authenticationType === "CLIENT") {
    $Client = new Client($this->db, $Authentication->getToken());
    $userId = $Client->getId();
  }

  if ($authenticationType === "PROVIDER") {
    $Provider = new Provider($this->db, $Authentication->getToken());
    $userId = $Provider->getId();
  }

  $receptor = $args['receptor'];
  $messageId = (int) $args['message_id'];

  $Chat = new Chat($this->db, $this->db_chat);
  $res = $Chat->deleteChatMessage($receptor, $userId, $authenticationType, $messageId);

  return $Output->response($response, $res['status'], $res['response']);
});
