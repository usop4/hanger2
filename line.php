<?php

require_once("common.php");
require_once("db.php");

class Line{

    public $ini;

    function Line(){
        $this->ini = parse_ini_file("api.ini",true)["line"];
        $this->base_url = parse_ini_file("api.ini",true)["base_url"];
    }

    function pushMessage($to,$text){

        $path = "/v2/bot/message/push";
        $url = "https://api.line.me{$path}";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->ini['access_token']}"
        ];

        $post = json_encode([
            "to"=>$to,
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

    function replyConfirm($token,$text,$actions){

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
                    "type"=>"template",
                    "altText"=>$text,
                    "template"=>[
                        "type"=>"confirm",
                        "text"=>$text,
                        "actions"=>$actions
                    ]
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

    function replyCarousel($token,$array){

        $path = "/v2/bot/message/reply";
        $url = "https://api.line.me{$path}";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->ini['access_token']}"
        ];

        $column = [];
        foreach($array as $num){
            if( preg_match("/[0-9]{2}/",$num) ){
                if( preg_match("/(0)[1-9]{1}/",$num) ){
                    $num = str_replace("0","",$num);
                }
                array_push($column,[
                        "thumbnailImageUrl"=>$this->base_url.$num.".jpg",
                        "text"=>$num,
                        "actions"=>[
                            ["type"=>"postback","label"=>"on","data"=>$num],
                        ]
                ]);
            }
        }

        $post = json_encode([
            "replyToken"=>$token,
            "messages"=>[
                [
                    "type"=>"template",
                    "altText"=>"carousel",
                    "template"=>[
                        "type"=>"carousel",
                        "columns"=>$column
                    ]
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

    function replyButtons($token,$text,$buttons){

        $path = "/v2/bot/message/reply";
        $url = "https://api.line.me{$path}";

        $headers = [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->ini['access_token']}"
        ];

        $actions = [];
        foreach($buttons as $button){
            array_push($actions,[
                "type"=>"postback",
                "label"=>$button["label"],
                "data"=>$button["data"]
            ]);
        }

        $post = json_encode([
            "replyToken"=>$token,
            "messages"=>[
                [
                    "type"=>"template",
                    "altText"=>$text,
                    "template"=>[
                        "type"=>"buttons",
                        "text"=>$text,
                        "actions"=>$actions
                    ]
                ]
            ]
        ]);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($curl);
        mydump("event",$output);
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
                    "originalContentUrl"=>$this->base_url."ppap.jpg",
                    "previewImageUrl"=>$this->base_url."ppap.jpg",
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
$json_object = json_decode($json_string);

$line = new Line();
foreach ($json_object->events as $event) {

    $replyToken = $event->replyToken;
    mydump("event",$event,FALSE);

    if( $event->type == "beacon"){
        // ビーコンに近づくと、直近に光らせたハンガーを光らせる
        $db = new Db();
        $fav = $db->getFav($event->source->userId);
        $message = "0".$fav["hanger"]."900";
        pushData($message);
        //$line->replyMessage($replyToken,$message);
    }

    if( $event->type == "postback"){

        $data = $event->postback->data;

        // 数字１桁の場合、特定のハンガーを光らせ、DBに記録する
        if( preg_match("/^[0-9]{1}$/i",$data)){
            pushData("00000");
            pushData("0".$data."909");
            $db = new Db();
            $db->setFav($event->source->userId,$data);
        }

        // httpで始まる場合、アクセスする
        if( preg_match("/http/",$data)){
            file_get_contents($data);
            $line->replyMessage($replyToken,"設定しました");
        }

        // カルーセル
        if( preg_match("/carousel/",$data)){
            $hangers = explode(",",str_replace("carousel=","",$data));
            $line->replyCarousel($replyToken,$hangers);
        }

        // 画像の差し替え
        if( preg_match("/fname/",$data)){
            parse_str($data);
            $num = str_replace(".jpg","",$fname);

            $url = $base_url."recvImage.php?".$data;
            file_get_contents($url);
            // 差し替え画像
            //$line->replyMessage($replyToken,$num);
            $line->replyCarousel($replyToken,["0".$num]);
        }

        // 0の場合、全て消す
        if( $data == "0" ){
            pushData("00000");
        }

    }

    if( $event->type == "message"){

        if( $event->message->type == "image" ){
            $line->replyButtons($replyToken,"入れ替え先を選んでください",[
                ["label"=>"1","data"=>"fname=1.jpg&id=".$event->message->id],
                ["label"=>"2","data"=>"fname=2.jpg&id=".$event->message->id],
                ["label"=>"3","data"=>"fname=3.jpg&id=".$event->message->id],
                ["label"=>"4","data"=>"fname=4.jpg&id=".$event->message->id]
            ]);
        }

        $text = $event->message->text;

        if( preg_match("/[0-9]{4,5}/i",$text)){
            // 数字5桁の場合、シミュレータに送信
            pushData($text);

        }
        elseif( preg_match("/[0-9]{1}/i",$text)){
            // 数字１桁の場合、特定のハンガーを光らせる
            $message = file_get_contents($base_url."db.php?on=".$text);
            pushData("0".$message);
        }
        elseif( preg_match("/fav/",$text)){
            $db = new Db();
            $fav = $db->getFav($event->source->userId);
            $line->replyMessage($replyToken,$fav["hanger"]);
        }
        elseif( preg_match("/ルーレット/",$text)){
            $message = "ルーレットを回しちゃうよ";
            $line->replyMessage($replyToken,$message);
            pushData("00777");
        }
        elseif( preg_match("/カルーセル/",$text)){
            $line->replyCarousel($replyToken,["01","02","03","04","05"]);
        }
        elseif( preg_match("/Ppap/",$text)){
            $line->replyPPAP($replyToken);
            pushData("00777");
        }
        elseif( preg_match("/エンジン/",$text)){
            $current_engine=file_get_contents("http://barcelona.sakura.ne.jp/sandbox/hanger2/selector");
            $line->replyButtons($replyToken,"対話エンジンを選んでください：".$current_engine,[
                [
                    "label"=>"通常",
                    "data"=>$base_url."index.php?selector=bot_engine1"
                ],
                [
                    "label"=>"シンプル",
                    "data"=>$base_url."index.php?selector=bot_simple"
                ],
                [
                    "label"=>"ユーザーローカル",
                    "data"=>$base_url."index.php?selector=userlocal"
                ],
                [
                    "label"=>"Docomo",
                    "data"=>$base_url."index.php?selector=docomo"
                ]
            ]);
        }
        elseif( preg_match("/リサイクル/",$text)){
            pushData("00000");
            pushData("02900");
        }
        else{
            $message = file_get_contents($base_url."selector.php?text=".$text);
            $message = str_replace("\n","\\n",$message);

            $temp = preg_replace('/<[0-9]{2}>/','',$message);
            //$line->replyMessage($replyToken,$temp);
            pushData($temp);

            $carousel = "";
            preg_match_all("/<[0-9]{2}>/",$message,$out,PREG_PATTERN_ORDER);
            foreach($out[0] as $hanger){
                $hanger = preg_replace(["(<)","(>)"],"",$hanger);
                if( $hanger != 0 ){
                    $carousel = $carousel.$hanger.",";
                    pushData($hanger."909");
                }else{
                    pushData("00000");
                }
                sleep(0.2);
            }

            if( $carousel == "" ){

                $array = [];
                while( sizeof($array) < 5 ){ // LINEのカルーセルで表示できる上限（＝５）
                    $num = rand(1,9);
                    if( !in_array($num,$array) ){
                        array_push($array,$num);
                        $carousel = $carousel."0".$num.",";
                    }
                }

                $line->replyConfirm($replyToken,$temp,[
                    [
                        "type"=>"postback",
                        "label"=>"表示する",
                        "data"=>"carousel=".$carousel
                    ],
                    [
                        "type"=>"postback",
                        "label"=>"キャンセル",
                        "data"=>"0"
                    ]
                ]);
                pushData("00000");
                pushData("0".rand(0,9).rand(0,3).rand(0,3).rand(0,3));

            }else{
                $line->replyConfirm($replyToken,$temp,[
                    [
                        "type"=>"postback",
                        "label"=>"表示する",
                        "data"=>"carousel=".$carousel
                    ],
                    [
                        "type"=>"postback",
                        "label"=>"キャンセル",
                        "data"=>"0"
                    ]
                ]);
            }

        }

    }

}

