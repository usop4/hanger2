<?php
date_default_timezone_set('Asia/Tokyo');

require_once("slack.php");
$slack = new Slack();

$temp = file_get_contents('php://input');
$slack->dump($temp);

if( isset($_POST["text"]) ){
    $text = $_POST["text"];
    if( strpos($text,'#') === false){ // # を含む場合は処理を除外

        if( preg_match("/[0-9]{4}/i",$text)){
            // 数字４桁の場合、シミュレータに送信
            $color = file_get_contents($slack->base_url."db.php?hanger=".$text);
            $slack->sendMessage("# hanger ".$text,$color);
        }

        elseif( preg_match("/[0-9]/i",$text)){
            // 数字１桁の場合、特定のハンガーを光らせる
            $color = file_get_contents($slack->base_url."db.php?on=".$text);
            //$slack->sendMessage("# on ".$text,$color);
            $slack->sendImage($text);
        }

        elseif( preg_match("/リセット/i",$text)){
            // リセット
            file_get_contents($slack->base_url."db.php?reset");
            $slack->sendMessage("# リセットしました");
        }

        elseif( preg_match("/こんにちは/i",$text)){
            // 「こんにちは」の場合、Repl-AIを初期化
            $re = file_get_contents($slack->base_url."repl.php?init");
            $slack->sendMessage("# ".$re);
        }

        else{

            $results = file_get_contents($slack->base_url."db.php?query=".$text);
            $results = json_decode($results);
            foreach($results as $result){
                file_get_contents($slack->base_url."db.php?on=".$result);
                $slack->dump($result);
                $slack->sendImage($result);
            }

            // それ以外の場合、Repl-AIに委ねる
            //$re = file_get_contents($slack->base_url."repl.php?text=".$text);
            //$slack->sendMessage("# ".$re);
        }
    }
}
?>
