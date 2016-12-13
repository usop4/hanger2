<?php

require_once("common.php");

class Line{

    public $ini;

    function Line(){
        $this->ini = parse_ini_file("api.ini",true)["line"];
    }

    function replyMessage($token,$text){

        $path = "/v2/bot/message/reply";
        $url = "https://api.line.me{$path}";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->ini['access_token']}"
        ];

        $post = json_encode([
            "replyToken"=>$token,
            "messages"=>[
                ["type"=>"text","text"=>$text]
            ]
        ]);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($curl);

        return $output;

    }

    function replyPPAP($token){

        $path = "/v2/bot/message/reply";
        $url = "https://api.line.me{$path}";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->ini['access_token']}"
        ];

        $post = json_encode([
            "replyToken"=>$token,
            "messages"=>[
                [
                    "type"=>"image",
                    "originalContentUrl"=>"https://barcelona.sakura.ne.jp/sandbox/hanger2/ppap.jpg",
                    "previewImageUrl"=>"https://barcelona.sakura.ne.jp/sandbox/hanger2/ppap.jpg",
                ]
            ]
        ]);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($curl);

        return $output;

    }

}

$json_string = file_get_contents('php://input');

if( $json_string ){

    $json_object = json_decode($json_string);
    $content = $json_object->events[0];
    mydump("test",$content,FALSE);

    $text = $content->message->text;
    $replyToken = $content->replyToken;

    $temp = $replyToken;
    mydump("test",$temp);

    $line = new Line();

    if( preg_match("/[0-9]{4,5}/i",$text)){
        // 数字5桁の場合、シミュレータに送信
        pushData($text);

    }
    elseif( preg_match("/[0-9]{1}/i",$text)){
        // 数字１桁の場合、特定のハンガーを光らせる
        $message = file_get_contents($base_url."db.php?on=".$text);
        pushData($message);
    }
    elseif( preg_match("/ルーレット/",$text)){
        $message = "ルーレットを回しちゃうよ";
        $line->replyMessage($replyToken,$message);
        pushData("00777");
    }
    elseif( preg_match("/Ppap/",$text)){
        $line->replyPPAP($replyToken);
        pushData("00777");
    }
    elseif( preg_match("/リサイクル/",$text)){
        pushData("00000");
        pushData("02900");
    }
    else{
        $message = file_get_contents($base_url."selector.php?text=".$text);
        $message = str_replace("\n","\\n",$message);

        $temp = preg_replace('/<[0-9]{2}>/','',$message);
        $line->replyMessage($replyToken,$temp);
        pushData($temp);

        preg_match_all("/<[0-9]{2}>/",$message,$out,PREG_PATTERN_ORDER);
        foreach($out[0] as $hanger){
            $hanger = preg_replace(["(<)","(>)"],"",$hanger);
            if( $hanger != 0 ){
                pushData($hanger."909");
            }else{
                pushData("00000");
            }
            sleep(0.2);
        }

    }

}

