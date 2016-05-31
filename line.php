<?php

require_once("common.php");

class Line{

    public $ini;

    function Line(){
        $this->ini = parse_ini_file("api.ini",true)["line"];
        mydump("temp","ini");
        mydump("temp",$this->ini);
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

        mydump("temp",$post);

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

        mydump("temp",$post);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($curl);
        mydump("temp",$output);
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

    $message = file_get_contents("http://barcelona-prototype.com/sandbox/hanger2/selector.php?text=".$text);

    mydump("temp",$text);

    $line = new Line();
    $line->sendMessage($from,$message);

}
