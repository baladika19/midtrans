<?php

$server_key = "SB-Mid-server-fAKt3b3pFpXiogKvuXsEz8el";


$is_production = false;
//jika api nya production dan bukan sandbox
$api_url = $is_production ? 'https://app.midtrans.com/snap/v1/transactions' : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

//memastikan permintaan mengandung biaya
if(!strpos($_SERVER['REQUEST_URI'], '/charge')){
    http_response_code(404);
    echo "Hmm..., make sure its `/charge` "; exit();
}


//jika post menampilkan error 404
if($_SERVER['REQUEST_METHOD'] != 'POST'){
    http_response_code(404);
    echo "Something wrong :( "; exit();
}

//untuk mendapatan http post
$request_body = file_get_contents('php://input');
header('Content-Type: application/json');

$charhe_result = chargeAPI($api_url, $server_key, $request_body);

http_response_code($charhe_result['http_code']);

echo $charhe_result['body'];

function chargeAPI($api_url, $server_key, $request_body){
    $ch = curl_init();
    $curl_options = array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANFER => 1,
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        //tambahkan header ke permintaan, termasuk otorasi yang dihasilkan dari kunci sever
        CURLOPT_HTTPHEADER => array(
            'Content-Type: applications/json',
            'Accept: applications/josn',
            'Authorization: Basic ' . base64_decode($server_key . ':')
        ),
        CURLOPT_POSTFIELDS => $request_body
    );
    curl_setopt_array($ch, $curl_options);
    $result = array(
        'body' => curl_exec($ch),
        'http_code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
    );
    return $result;
}


?>