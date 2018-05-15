<?php

$list = array(
	'JTiLYEX/N3fALMDuVBNkGQ=='
	);

$data['auth_token'] = '473a41042db508a9-a5f96125adb29ee8-148c50013a5326cb';
$data['text']       = 'Test Cron';
$data['type']       = 'text';
$data['receiver']   = 'JTiLYEX/N3fALMDuVBNkGQ==';

$ch                 = curl_init("https://chatapi.viber.com/pa/send_message");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));
    $result = curl_exec($ch);


?>