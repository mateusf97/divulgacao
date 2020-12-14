<?php

namespace Chat;

use \Validator\Validator;
use \Authentication\Authentication;
use \Error\Error;
use \Client\Client;
use \Provider\Provider;

class Chat {
  public function __construct($db, $db_chat) {
    $this->db = $db;
    $this->db_chat = $db_chat;
  }








  /**
  * Get all chat ids from specific provider user
  *
  * @param $userId
  * @return list of ids (array)
  **/

  public function getProviderChats($userId) {
    $chats = $this->db->prepare("SELECT chats.start_date, c.username receptor, cp.name receptor_name, cp.profile_picture receptor_picture FROM chats
                                 INNER JOIN client_private_data cp ON chats.client_id = cp.id
                                 INNER JOIN client c ON chats.client_id = c.id
                                 WHERE chats.provider_id='" . $userId . "'");
    $chats->execute();
    $chats = $chats->fetchAll();

    return $chats;
  }








  /**
  * Get all chat ids from specific client user
  *
  * @param $userId
  * @return list of ids (array)
  **/

  public function getClientChats($userId) {
    $chats = $this->db->prepare("SELECT chats.start_date, p.username receptor, pv.profile_title receptor_name, pv.profile_picture receptor_picture FROM chats
                                 INNER JOIN provider_public_profile pv ON chats.provider_id = pv.id
                                 INNER JOIN provider p ON chats.provider_id = p.id
                                 WHERE chats.client_id='" . $userId . "'");
    $chats->execute();
    $chats = $chats->fetchAll();

    return $chats;
  }








  /**
  * Get chat id from specific users
  *
  * @param $id
  * @return id (int)
  **/

  private function getChatId($clientId, $providerId) {

    $chatId = $this->db->prepare("SELECT chat_id FROM chats WHERE client_id = ? AND provider_id = ?");
    $chatId->execute([$clientId, $providerId]);
    $chatId = $chatId->fetch()['chat_id'];

    return $chatId;
  }








  /**
  * Process message data before
  *
  **/

  private function commitMessageStore($authorType, $message, $tableName) {
    $sendMessage = $this->db_chat->prepare("INSERT INTO " . $tableName . " SET message = ?, hide = 0, author_type = ?");
    $sendMessage->execute([$message, $authorType]);
  }








  /**
  * Post message data in database
  *
  **/

  public function sendMessage($params, $receptor, $Authentication) {
    $Validator = new Validator($this->db);

    $validations = array(
      "message" => array(
        "empty" => false,
        "required" => true,
        "max-length" => 2000,
      ),
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $authorType = $Authentication->getAuthenticationType();

    if ($authorType === "CLIENT") {
      $Client = new Client($this->db, $Authentication->getToken());
      $Provider = new Provider($this->db);

      $id = $Provider->getIdByUsername($receptor);

      if (is_null($id) || empty($id)) {
        return array('status' => 400, 'response' => 'INVALID_USERNAME');
      }

      $Provider->setId($id);
    }

    if ($authorType === "PROVIDER") {
      $Provider = new Provider($this->db, $Authentication->getToken());
      $Client = new Client($this->db);

      $id = $Client->getIdByUsername($receptor);

      if (is_null($id) || empty($id)) {
        return array('status' => 400, 'response' => 'INVALID_USERNAME');
      }

      $Client->setId($id);
    }

    $chatId = $this->getChatId($Client->getId(), $Provider->getId());

    if (!$chatId || is_null($chatId || empty($chatId))) {
      return array('status' => 406, 'response' => 'CHAT_NOT_FOUND');
    }

    $tableName = "chat_" . $chatId;

    $this->commitMessageStore($authorType, $params['message'], $tableName);
    return array('status' => 200, 'response' => null);
  }








  /**
  * Get chat info
  *
  * @param $clientId, $providerId, $message
  * @return int - chat_id
  **/

  public function createNewChat($params, $receptor, $Authentication) {
    $Validator = new Validator($this->db);
    $Error = new Error();

    $validations = array(
      "message" => array(
        "empty" => false,
        "required" => true,
        "max-length" => 2000,
      ),
    );

    $validationError = $Validator->validateFields($params, $validations);

    if ($validationError) {
      return array('status' => 400, 'response' => $validationError);
    }

    $Client = new Client($this->db, $Authentication->getToken());
    $params['client_id'] = $Client->getId();

    $Provider = new Provider($this->db);
    $params['provider_id'] = $Provider->getIdByUsername($receptor);

    if (is_null($params['provider_id']) || empty($params['provider_id'])) {
      return array('status' => 400, 'response' => 'INVALID_USERNAME');
    }

    $alreadyExists = $this->db->prepare("SELECT chat_id FROM chats WHERE client_id = ? AND provider_id = ?");
    $alreadyExists->execute([$params['client_id'], $params['provider_id']]);

    $chatStatus['chat_id'] = $alreadyExists->fetch()['chat_id'];
    $chatStatus['already_exists'] = (boolean) $chatStatus['chat_id'];

    if ($chatStatus['already_exists']) {
      $response['status'] = 409;
      $response['response'] = $chatStatus;

      return $response;
    }

    $chat = $this->db->prepare("INSERT INTO chats SET client_id = ?, provider_id = ?");
    $chat->execute([$params['client_id'], $params['provider_id']]);


    $chatId = $this->db->prepare("SELECT LAST_INSERT_ID() AS id");
    $chatId->execute();
    $chatId = $chatId->fetch()["id"];

    $tableName = "chat_" . $chatId;
    $constraintName = "fk_" . $tableName;

    /**
    *
    * @param new_table was used with reference to the doglee chats database.
    * @see @method $commitMessageStore()
    **/

    $new_table = $this->db_chat->prepare("
      CREATE TABLE IF NOT EXISTS " . $tableName . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `message` TEXT,
        `hide` tinyint(1) DEFAULT 0,
        `author_type` varchar(20) NOT NULL,
        `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      ) ENGINE  = InnoDB;"
    );

    $new_table->execute();

    $this->commitMessageStore("CLIENT", $params['message'], $tableName);

    $chatStatus['chat_id'] = $chatId;
    $chatStatus['already_exists'] = (boolean) !$chatId;

    return array('status' => 201, 'response' => $chatStatus);
  }







  /**
  * Get chat info
  *
  * @param $id
  *
  **/

  private function getChatInfo($chatId) {

    $chatInfo = $this->db->prepare("SELECT * FROM chats WHERE chat_id = '" . $chatId . "'");
    $chatInfo->execute();
    $chatInfo = $chatInfo->fetch();

    return $chatInfo;
  }





  /**
  * Get chat messages
  *
  * @params $chatParamId, $sender, $authenticationType
  *
  **/

  public function getChatMessages($receptor, $senderId, $authenticationType) {
    if ($authenticationType === "CLIENT") {
      $Provider = new Provider($this->db);
      $receptorId = $Provider->getIdByUsername($receptor);

      $chat = $this->db->prepare("SELECT chat_id FROM chats WHERE client_id = ? AND provider_id = ?");
    }

    if ($authenticationType === "PROVIDER") {
      $Client = new Client($this->db);
      $receptorId = $Client->getIdByUsername($receptor);

      $chat = $this->db->prepare("SELECT chat_id FROM chats WHERE provider_id = ? AND client_id = ?");
    }

    $chat->execute([$senderId, $receptorId]);
    $chat = $chat->fetch();

    $chatId = (int) $chat['chat_id'];

    if (!$receptorId) {
      return array('status' => 403, 'response' => null);
    }

    if (!$chatId) {
      return array('status' => 404, 'response' => null);
    }

    $tableName = "chat_" . $chatId;

    $chatMessages = $this->db_chat->prepare("SELECT * FROM " . $tableName . "");
    $chatMessages->execute();
    $chatMessages = $chatMessages->fetchAll();

    $response = array(
      "receptor" => $receptor,
      "messages" => $chatMessages
    );

    return array('status' => 200, 'response' => $response);
  }








  /**
  * Delete specific chat messages
  *
  * @param $id
  *
  **/

  public function deleteChatMessage($receptor, $userId, $authenticationType, $messageId) {
    if ($authenticationType === "CLIENT") {
      $Provider = new Provider($this->db);
      $receptorId = $Provider->getIdByUsername($receptor);

      $chat = $this->db->prepare("SELECT chat_id FROM chats WHERE client_id = ? AND provider_id = ?");
    }

    if ($authenticationType === "PROVIDER") {
      $Client = new Client($this->db);
      $receptorId = $Client->getIdByUsername($receptor);

      $chat = $this->db->prepare("SELECT chat_id FROM chats WHERE provider_id = ? AND client_id = ?");
    }

    $chat->execute([$userId, $receptorId]);
    $chat = $chat->fetch();

    $chatId = (int) $chat['chat_id'];

    if (!$receptorId) {
      return array('status' => 403, 'response' => null);
    }

    if (!$chatId) {
      return array('status' => 404, 'response' => null);
    }

    $tableName = "chat_" . $chatId;

    $storedMessageId = $this->db_chat->prepare("SELECT id FROM " . $tableName . " WHERE author_type = ? AND id = ?");
    $storedMessageId->execute([$authenticationType, $messageId]);
    $storedMessageId = (int) $storedMessageId->fetch()['id'];

    if ($storedMessageId !== $messageId) {
      return array('status' => 403, 'response' => 'UNAUTHORIZED');
    }

    $delete = $this->db_chat->prepare("UPDATE " . $tableName . " SET hide = 1 WHERE author_type = ? AND id = ?");
    $delete->execute([$authenticationType, $messageId]);

    return array('status' => 200, 'response' => true);
  }

}
