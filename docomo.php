<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 17/02/25
 * Time: 14:17
 */

require_once("common.php");

class Docomo{

    public $ini = '';

    function Docomo(){
        $this->ini = parse_ini_file("api.ini",true)["docomo"];
    }

    function initCurl(){

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return $curl;
    }

    function sendMessage($text){

        $curl = $this->initCurl();
        $url = "https://api.apigw.smt.docomo.ne.jp/dialogue/v1/dialogue?APIKEY=".$this->ini["key"];
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));

        //$context = "";
        $context = file_get_contents($base_url."context");

        $postfields = [
            "utt"=>$text,
            "context"=>$context,
            "nickname"=>"光",
            "nickname_y"=>"ヒカリ",
            "sex"=>"女",
            "bloodtype"=>"B",
            "birthdateY"=>"1997",
            "birthdateM"=>"5",
            "birthdateD"=>"30",
            "age"=>"16",
            "constellations"=>"双子座",
            "place"=>"東京",
            "mode"=>"dialog"
        ];

        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postfields));
        $json = curl_exec($curl);

        $re = json_decode($json,true);
        file_put_contents("context",$re["context"]);
        return $re["utt"];
    }

}

$ul = new Docomo();

if( isset($_GET["text"]) ){
    $text = $_GET["text"];
    $re = $ul->sendMessage($text);
    echo $re;
}
