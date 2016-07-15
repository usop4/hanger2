<?php

require_once("common.php");

class Line{

    public $ini;

    function Line(){
        $this->ini = parse_ini_file("api.ini",true)["line"];
    }

    function sendMessage($from,$text){

        $path = "/v1/events";
        $url = "https://trialbot-api.line.me{$path}";

        $headers = [
            "Content-Type: application/json",
            "X-Line-ChannelID: {$this->ini['channel_id']}",
            "X-Line-ChannelSecret: {$this->ini['channel_secret']}",
            "X-Line-Trusted-User-With-ACL: {$this->ini['mid']}"
        ];

        mydump("temp",$headers);

        $post = json_encode([
            "to"=>[$from],
            "toChannel"=>1383378250,
            "eventType"=>"138311608800106203",
            "content"=>[
                "toType"=>1,
                "contentType"=>1,
                "text"=>$text
            ]
        ]);

        $event_type = "138311608800106203";
        $post = <<< EOM
{
    "to":["{$from}"],
    "toChannel":1383378250,
    "eventType":"{$event_type}",
    "content":{
        "toType":1,
        "contentType":1,
        "text":"{$text}"
    }
}
EOM;

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
    $content = $json_object->result{0}->content;
    $text = $content->text;
    $from = $content->from;
    $message_id = $content->id;
    $content_type = $content->contentType;

    if( preg_match("/[0-9]{4,5}/i",$text)){
        // 数字5桁の場合、シミュレータに送信
        pushData($text);

    }
    elseif( preg_match("/[0-9]{1}/i",$text)){
        // 数字１桁の場合、特定のハンガーを光らせる
        $message = file_get_contents($base_url."db.php?on=".$text);
        pushData($message);
    }
    else{
        $message = file_get_contents($base_url."selector.php?text=".$text);
        $message = str_replace("\n","\\n",$message);

        $line = new Line();
        $temp = preg_replace('/<[0-9]{2}>/','',$message);
        $line->sendMessage($from,$temp);
        pushData($temp);

        preg_match_all("/<[0-9]{2}>/",$message,$out,PREG_PATTERN_ORDER);
        foreach($out[0] as $hanger){
            $hanger = preg_replace(["(<)","(>)"],"",$hanger);
            if( $hanger != 0 ){
                pushData($hanger."999");
            }else{
                pushData("00000");
            }
            sleep(0.2);
        }

    }

}

