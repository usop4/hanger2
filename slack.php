<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16/04/30
 * Time: 11:50
 */

require_once("common.php");
require('external/Pusher.php');


date_default_timezone_set("Asia/Tokyo");

class Slack{

    public $ini;
    public $base_url;

    function Slack(){
        $this->ini = parse_ini_file("api.ini",true)["slack"];
        $this->base_url = $this->ini["base_url"];
    }

    function sendMessage($text,$color=null){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $this->ini["hook"]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        if( $color!=null ){
            $postfields = '{"text":"'.$text.'","attachments":[{"color":"'.$color.'","text":"'.$color.'"}]}';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        }else{
            $postfields = [
                'text'=>$text
            ];
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postfields));
        }
        return curl_exec($curl);
    }

    function sendImage($num){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $this->ini["hook"]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        $image_url = $this->base_url.$num.".jpg";
        $postfields = '{"text":"# '.$num.'","attachments":[{"image_url":"'.$image_url.'"}]}';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        return curl_exec($curl);
    }

}

if( isset($_GET["sendImage"]) ){
    $num = $_GET["sendImage"];
    $slack = new Slack();
    $slack->sendImage($num);
}

if( isset($_POST["text"]) ){
    $text = $_POST["text"];
    $slack = new Slack();

    if( strpos($text,'#') === false){ // # を含む場合は処理を除外

        $re = file_get_contents("http://barcelona-prototype.com/sandbox/hanger2/selector.php?text=".$text);
        $slack->sendMessage("# ".$re);

        $base_url = "http://barcelona-prototype.com/sandbox/hanger2/";
        if( preg_match("/[0-9]{4}/i",$text)){
            // 数字４桁の場合、シミュレータに送信
            $color = file_get_contents($base_url."db.php?hanger=".$text);
            $slack->sendMessage("# hanger ".$text,$color);

            $pusher_ini = parse_ini_file("api.ini",true)["pusher"];
            $pusher = new Pusher(
                $pusher_ini["key"],
                $pusher_ini["secret"],
                $pusher_ini["app_id"],
                ['encrypted'=>true]
            );
            $data['message'] = $text;
            $pusher->trigger('test_channel', 'my_event', $data);

        }

        elseif( preg_match("/[0-9]/i",$text)){
            // 数字１桁の場合、特定のハンガーを光らせる
            $color = file_get_contents($base_url."db.php?on=".$text);
            //$slack->sendMessage("# on ".$text,$color);
            $slack->sendImage($text);
        }

        elseif( preg_match("/リセット/i",$text)){
            // リセット
            file_get_contents($base_url."db.php?reset");
            $slack->sendMessage("# リセットしました");
        }

        // dbに格納されているキーワードと一致したら、服の番号を返す（Slackのみ）
        $results = file_get_contents($slack->base_url."db.php?query=".$text);
        $results = json_decode($results);
        foreach($results as $result){
            file_get_contents($slack->base_url."db.php?on=".$result);
            $slack->sendImage($result);
        }

    }

}
