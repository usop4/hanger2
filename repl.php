<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16/05/02
 * Time: 21:25
 */
date_default_timezone_set('Asia/Tokyo');

class Repl{

    public $botId = 'first';
    public $appUserId = '';
    public $initTopicId = 'hanger';

    function Repl(){
        $temp = file_get_contents("user");
        if( $temp ){
            $this->appUserId = $temp;
        }else{
            $this->appUserId = $this->getUserId();
        }
    }

    function initCurl(){

        $ini = parse_ini_file("api.ini",true);

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "x-api-key: ".$ini["repl"]["key"],
            "Content-type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $curl;
    }

    function getUserId(){
        $curl = $this->initCurl();
        curl_setopt($curl, CURLOPT_URL, "https://api.repl-ai.jp/v1/registration");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode([
            'botId'=>$this->botId
        ]));
        $json = curl_exec($curl);
        $appUserId = json_decode($json,ture)["appUserId"];
        file_put_contents("user",$appUserId);
        return $appUserId;
    }

    function sendMessage($text,$init=false){
        $curl = $this->initCurl();
        curl_setopt($curl, CURLOPT_URL, "https://api.repl-ai.jp/v1/dialogue");
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        if( $init == true ){
            $text = "init";
            $recvTime = date('Y-m-d H:i:s');
            $this->appUserId = $this->getUserId();
        }else{
            $recvTime = file_get_contents("time");
        }
        $postfields = [
            'appUserId'=>$this->appUserId,
            'botId'=>$this->botId,
            'voiceText'=>$text,
            'initTalkingFlag'=>$init,
            'initTopicId'=>$this->initTopicId,
            'appRecvTime'=>$recvTime,
            'appSendTime'=>date('Y-m-d H:i:s')
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postfields));
        $json = curl_exec($curl);

        $re = json_decode($json,true);
        $serverSendTime = $re["serverSendTime"];
        file_put_contents("time",$serverSendTime);
        return $re;
    }

    function dump($var, $reset=null){
        if( $reset ){
            file_put_contents("log","");
        }
        var_dump($var);
        echo "<BR>".PHP_EOL;
        ob_start();
        var_dump($var);
        $out = ob_get_contents();
        ob_end_clean();
        file_put_contents("log",date(DATE_RFC2822)." ".$out.PHP_EOL,FILE_APPEND);
    }

}

$repl = new Repl();

if( isset($_GET["init"]) ){
    $re = $repl->sendMessage("init",true);
    echo $re["systemText"]["expression"];
}

if( isset($_GET["text"]) ){
    $text = $_GET["text"];

    if( preg_match("/こんにちは/i",$text)){
        // 「こんにちは」の場合、Repl-AIを初期化
        $re = $repl->sendMessage("init",true);
    }else{
        $re = $repl->sendMessage($text);
    }
    echo $re["systemText"]["expression"];

}
