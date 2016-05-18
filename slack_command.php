<?php
date_default_timezone_set('Asia/Tokyo');

require_once("slack.php");
$slack = new Slack();
//$slack->dump("slack_command.php",true);

$temp = file_get_contents('php://input');

if( isset($_POST["text"]) ){
    $text = $_POST["text"];
    if( strpos($text,'#') === false){ // # を含む場合は処理を除外
        if( preg_match("/[0-9]{4}/i",$text)){ // 数字４桁の場合、シミュレータに送信
            $color = file_get_contents("http://barcelona.sakura.ne.jp/sandbox/hanger2/db.php?hanger=".$text);
            $slack->sendMessage("# ".$text,$color);
        }elseif( preg_match("/こんにちは/i",$text)){ // 「こんにちは」の場合、Repl-AIを初期化
            $re = file_get_contents("http://barcelona.sakura.ne.jp/sandbox/hanger2/repl.php?init");
            $slack->sendMessage("# ".$re);
        }else{ // それ以外の場合、Repl-AIに委ねる
            $re = file_get_contents("http://barcelona.sakura.ne.jp/sandbox/hanger2/repl.php?text=".$text);
            $slack->sendMessage("# ".$re);
        }

    }
}
?>
