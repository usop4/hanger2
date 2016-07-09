<?php

$key = parse_ini_file("api.ini",true)["docomo"]["key"];

if( isset($_GET["text"]) ){

    $text = $_GET["text"];
    $apiUrl = "https://api.apigw.smt.docomo.ne.jp/voiceText/v1/textToSpeech?APIKEY=".$key;
    //echo $apiUrl;

    $header = [
        "Content-Type: application/x-www-form-urlencoded"
    ];
    $content = http_build_query([
        "text"=>$text,
        "speaker"=>"hikari"
    ]);
    $context = [
        "http"=>[
            "method"=>"POST",
            "header"=>implode("¥r¥n",$header),
            "content"=>$content
        ]
    ];

    $response = file_get_contents($apiUrl,false,stream_context_create($context));
    echo '<audio autoplay src="data:audio/wav;base64,'.base64_encode($response).'"/>';
    echo $response;
}
