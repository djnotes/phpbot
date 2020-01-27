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
  const REGISTER_SAVE_INFO = "register_save_info"

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
          break;

      }

      //Message was not handled in the menu. So process based on session

      switch(self::$sessions[$peerId]['state']) {
        case STATE_SEND_USERNAME:
          self::$sessions[$peerId]['username'] = filter_var($message, FILTER_SANITIZE_STRING);
          self::$sessions[$peerId]['state'] = REGISTER_SEND_PASSWORD;
          break;
        case REGISTER_SEND_PASSWORD:
          self::$sessions[$peerId]['password'] = filter_var($message, FILTER_SANITIZE_STRING);
          self::$sessions[$peerId]['state'] = REGISTER_SAVE_INFO;
          break;

        case REGISTER_SAVE_INFO:
          if($this->registerUser($peerId)) {
            //clear session
            self::$sessions[$peerId] = [];
            yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Registration successful. Now you may log in."])
          }
          else {
            yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Registration failed. Sorry!"])
          }
          break;


      }



  }

  public function onUpdateNewChannelMessage($update){
    yield $this->logger($update);
  }

  public function onUpdateBotCallbackQuery($update){
    $peerId = $update['user_id'];
    switch($update['data']){
      case 'callback_register':
        self::$sessions[$peerId]['session']['state'] = self::REGISTER_SEND_USERNAME;
        yield $this->messages->sendMessage(['peer' => $peerId, 'message' => "Please enter a username to register"]);
        break;
    }
    yield $this->logger($update);
  }

  protected function registerUser (long $peerId) {
    //TODO: save user info in db
    return false;
  }

}
