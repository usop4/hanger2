<?php

require_once("common.php");

class Alchemy{

    public $ini;

    function __construct(){
        $this->ini = parse_ini_file("api.ini",true)["alchemy"];
    }

    function sendUrl($url=$base_url."1.jpg"){
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
        $kw = "";
        foreach($keywords as $keyword){
            $kw = $kw . $keyword["text"] . ",";
        }
        return $kw;
    }

}

if( isset($_GET["img"]) ){
    $img = $_GET["img"];
    $alchemy = new Alchemy();
    echo $alchemy->sendUrl($img);
}
