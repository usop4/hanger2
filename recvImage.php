<?php

require_once("common.php");

// スマートフォンから送信された画像をサーバ上に保存

if( isset($_GET["fname"]) ){
    $fname = $_GET["fname"];
    $num = str_replace(".jpg","",$fname);
}else{
    $fname = "temp";
    $num = 0;
}

$temp = file_get_contents("php://input");
$temp = urldecode($temp);
$temp = str_replace("image=data:image/jpeg;base64,","",$temp);
$temp = base64_decode($temp);

file_put_contents($fname,$temp);

require_once("db.php");
$db = new DB();

// Alchemyに送信しタグ出力

require_once("alchemy.php");
$alchemy = new Alchemy();
$alchemy_keywords = $alchemy->sendUrl($base_url.$fname);
$db->setFeature($num,"feature",$alchemy_keywords);
echo $alchemy_keywords."<br>";

// GCPでタグ取得

$gcp_keywords = file_get_contents("http://barcelona.sakura.ne.jp/sandbox/hanger2/gcp.php?url=".$base_url.$fname);
$db->setFeature($num,"feature2",$gcp_keywords);
echo $gcp_keywords."<br>";

// Amazon Rekognitionでタグ取得
$url = "http://barcelona.sakura.ne.jp/sandbox/hanger2/rekog.php?fname=".$fname;
echo $url;
$rekog_keywords = file_get_contents($url);
$db->setFeature($num,"feature3",$rekog_keywords);
echo $rekog_keywords."<br>";

// 取得したテキストをDBに保存

//$db->setDesc($num,$keywords);
$db->setFeature3($num,$alchemy_keywords,$gcp_keywords,$rekog_keywords);