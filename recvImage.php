<?php

require_once("common.php");

// スマートフォンから送信された画像をサーバ上に保存

if( isset($_GET["fname"]) ){
    $fname = $_GET["fname"];
    $num = str_replace(".jpg","",$fname);

    if( isset($_GET["id"] )){
        // idが含まれる場合はLINEに問い合わせる
        $id = $_GET["id"];
        $url = "https://api.line.me/v2/bot/message/".$id."/content";
        $headers = [
            "Authorization: Bearer {$ini["line"]["access_token"]}"
        ];
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $temp = curl_exec($curl);
    }else{
        // fnameだけ指定されている場合（＝Monacaからの送信）
        $temp = file_get_contents("php://input");
        $temp = urldecode($temp);
        $temp = str_replace("image=data:image/jpeg;base64,","",$temp);
        $temp = base64_decode($temp);
    }
    file_put_contents("temp.jpg",$temp);
    list($w, $h) = getimagesize("temp.jpg");
    if($w > $h){
        $diff  = ($w - $h) * 0.5;
        $diffW = $h;
        $diffH = $h;
        $diffY = 0;
        $diffX = $diff;
    }elseif($w < $h){
        $diff  = ($h - $w) * 0.5;
        $diffW = $w;
        $diffH = $w;
        $diffY = $diff;
        $diffX = 0;
    }elseif($w === $h){
        $diffW = $w;
        $diffH = $h;
        $diffY = 0;
        $diffX = 0;
    }
    $thumbW = 300;
    $thumbH = 300;
    $thumbnail = imagecreatetruecolor($thumbW, $thumbH);
    $baseImage = imagecreatefromjpeg("temp.jpg");
    imagecopyresampled($thumbnail, $baseImage, 0, 0, $diffX, $diffY, $thumbW, $thumbH, $diffW, $diffH);
    imagejpeg($thumbnail,$fname, 60);
}

require_once("db.php");
$db = new DB();

// Alchemyに送信しタグ出力

require_once("alchemy.php");
$alchemy = new Alchemy();
$alchemy_keywords = $alchemy->sendUrl($base_url.$fname);
$db->setFeature($num,"feature",$alchemy_keywords);
echo $alchemy_keywords."<br>";

// GCPでタグ取得

$gcp_keywords = file_get_contents($base_url."gcp.php?url=".$base_url.$fname);
$db->setFeature($num,"feature2",$gcp_keywords);
echo $gcp_keywords."<br>";

// Amazon Rekognitionでタグ取得
$url = $base_url."rekog.php?fname=".$fname;
echo $url;
$rekog_keywords = file_get_contents($url);
$db->setFeature($num,"feature3",$rekog_keywords);
echo $rekog_keywords."<br>";

// 取得したテキストをDBに保存

//$db->setDesc($num,$keywords);
$db->setFeature3($num,$alchemy_keywords,$gcp_keywords,$rekog_keywords);