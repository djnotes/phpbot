<?php

namespace PhpBot;

include __DIR__ . '/../vendor/autoload.php';

use \danog\MadelineProto\EventHandler;

class BotEventHandler extends EventHandler {

  /**
  Used to store session status
  */
  static $sessions = [];

  static $bot_name = 'PHP Demo';




  const REGISTER_SEND_USERNAME = "register_send_username";
  const REGISTER_SEND_PASSWORD = "register_send_password";

  static $admin_markup = [
    '_' => 'replyInlineMarkup', 'rows' => [
      [
        '_' => 'keyboardButtonRow', 'buttons' => [
          ['_' => 'keyboardButtonCallback', 'text' => 'Register', 'data' => 'callback_register'],
          ['_' => 'keyboardButtonCallback', 'text' => 'Log In', 'data' => 'callback_register']
        ]
      ]

    ]
  ];

  public function onUpdateNewMessage($update){
      yield $this->logger($update);
      if(empty($update['message']['message'])){
        return;
      }

      if(isset($update['message']['out']) && $update['message']['out']) {
        return;
      }

      $peerId = $update['message']['from_id'];
      $me = yield $this->getPwrChat('me');
      $message = $update['message']['message'];

      yield $this->logger($me);

      switch($message){
        case '/start':
        if(isset($me['first_name'])){
          yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Welcome to the  {$me['first_name']} {$me['type']}"]);
          return;
        }
        else {
          yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Welcome to the  {$this::$bot_name} {$me['type']}"]);
          return;
        }
          break;
        case '/menu':
          yield $this->messages->sendMessage(['peer' => $peerId, 'message' => 'Select an option to continue', 'reply_markup' => self::$admin_markup]);
          return;
          break;

      }

      //Message was not handled in the menu. So process based on session

      if(!isset(self::$sessions[$peerId]['state'])) {
        self::$sessions[$peerId]['state'] = '';
      }
      switch(self::$sessions[$peerId]['state']) {
        case $this::REGISTER_SEND_USERNAME:
          self::$sessions[$peerId]['username'] = filter_var($message, FILTER_SANITIZE_STRING);
          self::$sessions[$peerId]['state'] = $this::REGISTER_SEND_PASSWORD;
          yield $this->messages->sendMessage(['peer' => $peerId, 'message' => 'Enter password']);
          break;
        case $this::REGISTER_SEND_PASSWORD:
          self::$sessions[$peerId]['password'] = filter_var($message, FILTER_SANITIZE_STRING);
          if($this->registerUser($peerId)) {
            //clear session
            self::$sessions[$peerId] = [];
            yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Registration successful. Now you may log in."]);
          }
          break;


      }



  }

  public function onUpdateNewChannelMessage($update){
    yield $this->logger($update);
  }

  public function onUpdateBotCallbackQuery($update){
    yield $this->logger($update);

    $peerId = $update['user_id'];
    switch($update['data']){
      case 'callback_register':

        self::$sessions[$peerId]['state'] = $this::REGISTER_SEND_USERNAME;
        yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Please enter a username to register"]);
        break;
    }
  }

  protected function registerUser (int $peerId) {
      $config = Amp\Mysql\ConnectionConfig::fromString("host=127.0.0.1 user=testuser password=testpass db=phpbot");

      $pool = Amp\Mysql\pool($config);

      $statement = yield $pool->prepare("INSERT INTO `users` (`username`, `password`) VALUES (:username, :password)");

      $result = yield $statement->execute(['username' => self::$sessions[$peerId]['username'], 'password' => self::$sessions[$peerId]['password']]);

    //TODO: save user info in db
    yield $this->echo("Result of insertion: ");
    yield $this->logger($result);
    if($result){return true;}
    return false;
  }

}
