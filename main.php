<?php

include __DIR__ . '/vendor/autoload.php';

$mtproto = new \danog\MadelineProto\API(__DIR__ . '/session.madeline');
$mtproto->async(true);
$mtproto->loop(function() use ($mtproto) {
  yield $mtproto->start();
  yield $mtproto->setEventHandler('PhpBot\BotEventHandler');

});

$mtproto->loop();

 ?>
