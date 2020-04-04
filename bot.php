<?php

include __DIR__ . '/vendor/autoload.php';


use PhpBot\Constants;

$request = curl_init();

$method = "getUpdates";

curl_setopt($request, CURLOPT_URL, Constants::BASE_URL . '/' . 'bot' . Constants::BOT_TOKEN . '/' . $method);
curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($request, CURLOPT_POST, true);

$offset = 0;


while(true){
	echo "Loop: " . PHP_EOL; 
	curl_setopt($request, CURLOPT_POSTFIELDS, ['offset' => $offset] );
    $response = curl_exec($request);
    echo "$response\n";
    


    $a = json_decode($response, true);
    if(is_array($a)) {
	$updates = $a['result'];
	foreach($updates as $update) {
		echo "Update: \n";
		if($update['message']['text'] == 'Hello') {
			sendMessage($update['message']['chat']['id'], "Hello  👍 ");
		}
		print_r($update);
		$offset = $update['update_id'] + 1;
	}
    }
       
}





function sendMessage($peer, $message) {
	$curl_msg = curl_init();
	$method = 'sendMessage';
	curl_setopt($curl_msg, CURLOPT_URL, Constants::BASE_URL . '/' . 'bot' . Constants::BOT_TOKEN . '/' . $method);
	curl_setopt($curl_msg, CURLOPT_POST, true);
	curl_setopt($curl_msg, CURLOPT_POSTFIELDS, ['chat_id' => $peer, 'text' => $message]);
	curl_exec($curl_msg);

}
