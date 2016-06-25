<?php

date_default_timezone_set("Asia/Tokyo");

class Alchemy{

    public $ini;

    function __construct(){
        $this->ini = parse_ini_file("api.ini",true)["alchemy"];
    }

    function sendUrl($url="http://barcelona.sakura.ne.jp/sandbox/hanger2/1.jpg"){
        $key = $this->ini["key"];
        $url = "http://access.alchemyapi.com/calls/url/URLGetRankedImageKeywords"
            ."?apikey=".$key
            ."&forceShowAll=1&outputMode=json"
            ."&url=".$url;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        $response = curl_exec($curl);
        $keywords = json_decode($response,true)["imageKeywords"];
        return $keywords;
    }

}

if( isset($_GET["img"]) ){
    $img = $_GET["img"];
    $alchemy = new Alchemy();
    $keywords = $alchemy->sendUrl($img);
    foreach($keywords as $keyword){
        echo $keyword["text"].",";
    }
}
