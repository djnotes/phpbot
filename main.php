<?php

include __DIR__ . '/vendor/autoload.php';

$mtproto = new \danog\MadelineProto\API(__DIR__ . '/session.madeline');
$mtproto->async(true);
$mtproto->loop(function() use ($mtproto) {
  yield $mtproto->start();
  // yield $mtproto->messages->sendMedia(['peer' => '@djnotes', 'media' => ['_' => 'inputMediaUploadedDocument', 'attributes' => [['_' => 'documentAttributeVideo', 'supports_streaming' => true]], 'file' => 'media/alex.mov'], 'message' => 'Alex Jolig']);
  yield $mtproto->messages->sendMedia(['peer' => '@djnotes', 'media' => ['_' => 'inputMediaUploadedDocument', 'file' => 'media/alex.mp4', 'attributes' => [['_' => 'documentAttributeAnimated']]], 'message' => 'Animated']);
  yield $mtproto->messages->sendMedia(['peer' => '@djnotes', 'media' => ['_' => 'inputMediaUploadedDocument', 'file' => 'media/fa.tex', 'thumb' => 'media/girl.jpg', 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => 'Farsi Hello World TeX']]], 'message' => 'Farsi TeX file']);
  yield $mtproto->messages->sendMedia(['peer' => '@djnotes', 'media' => ['_' => 'inputMediaUploadedDocument', 'file' => 'media/fa.tex', 'attributes' => [['_' => 'documentAttributeFilename', 'file_name' => 'TeX Hello World for Farsi']]], 'message' => 'Farsi TeX file']);

  yield $mtproto->setEventHandler('PhpBot\BotEventHandler');

});



$mtproto->loop();

 ?>
