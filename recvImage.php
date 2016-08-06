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

// 保存されたデータをAlchemyに送信しタグ出力

require_once("alchemy.php");

$alchemy = new Alchemy();
$keywords = $alchemy->sendUrl($base_url.$fname);

// 取得したテキストをDBに保存

require_once("db.php");
$db = new DB();
$db->setDesc($num,$keywords);