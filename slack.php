<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16/04/30
 * Time: 11:50
 */

require_once("common.php");

class Slack{

    public $ini;

    function Slack(){
        $this->ini = parse_ini_file("api.ini",true)["slack"];
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

        if( preg_match("/^[0-9]{4}/i",$text)){
            // 数字４桁の場合、シミュレータに送信
            $hanger = $text;
            $color = file_get_contents($base_url."db.php?hanger=".$text);
            //$slack->sendMessage("# ".$text,$color);
            pushData($text);
        }
        elseif( preg_match("/^[0-9]{1}/i",$text)){
            // 数字１桁の場合、特定のハンガーを光らせる
            $message = file_get_contents($base_url."db.php?on=".$text);
            //$slack->sendMessage($message);

            pushData($message);
        }
        elseif( preg_match("/ルーレット/",$text)){
            pushData("0777");
        }
        else{
            // それ意外の場合、指定したエンジンに送る
            $temp = preg_replace('/&lt;[0-9]&gt;/','',$text);
            $message = file_get_contents($base_url."selector.php?text=".$temp);
            $slack->sendMessage("# ".$message);
            $message = preg_replace("/&lt;[0-9]&gt;/","",$message);
            pushData($message);
        }

    }

    // 「こんにちは<1><2>」というテキストを受け取ったら、１番、２番のハンガーを光らせる
    preg_match_all("/&lt;[0-9]&gt;/",$text,$out,PREG_PATTERN_ORDER);
    foreach($out[0] as $hanger){
        $hanger = preg_replace(["(&lt;)","(&gt;)"],"",$hanger);
        if( $hanger != 0 ){
            pushData($hanger."999");
        }else{
            pushData("0000");
        }
        sleep(0.2);
    }

}
