<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16/05/02
 * Time: 21:25
 */

require_once("common.php");

class Userlocal{

    public $ini = '';

    function Userlocal(){
        $this->ini = parse_ini_file("api.ini",true)["userlocal"];
    }

    function initCurl(){

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $curl;
    }

    function sendMessage($text){
        $curl = $this->initCurl();
        $url = "https://chatbot-api.userlocal.jp/api/chat?message=".$text."&key=".$this->ini["key"];
        curl_setopt($curl, CURLOPT_URL, $url);
        $json = curl_exec($curl);
        return json_decode($json,true)["result"];
    }

}

$ul = new Userlocal();

if( isset($_GET["text"]) ){
    $text = $_GET["text"];
    $re = $ul->sendMessage($text);
    echo $re;

}
