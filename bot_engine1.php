<?php

require_once("common.php");
require_once("db.php");

$text = $_GET["text"];

if( preg_match("/(help)|(こんにちは)/i",$text)){
    $hangers = "";
    $array = [];
    while( sizeof($array) < 5 ){ // LINEのカルーセルで表示できる上限（＝５）
        $num = rand(1,9);
        if( !in_array($num,$array) ){
            array_push($array,$num);
            $hangers = $hangers."<0".$num."> ";
        }
    }
    echo "こんにちは。ハンガーbotです。"
        ."天気、流行、好みの色"
        ."などの言葉を元に、おすすめの服を選びます。"
        .$hangers;
}

elseif( preg_match("/リセット/",$text)){
    file_get_contents($base_url."db.php?reset");

    // Livedoorの天気を利用
    // http://weather.livedoor.com/forecast/rss/area/130010.xml
    // 10日（日）の天気は晴時々曇、最高気温は32℃ 最低気温は22℃でしょう。
    $debug = false; // trueにすると、Livedoorに問い合わせず、ローカルファイルを利用
    $url = "http://weather.livedoor.com/forecast/rss/area/130010.xml";
    if( $debug != true ){
        file_put_contents("weather_temp",file_get_contents($url));
    }

    echo "# DBをリセットしました<00>";
}

elseif( preg_match("/(色)|(流行)/",$text)){
    echo "流行りの色はブルーです。<02> <08>";
}

elseif( preg_match("/(天気)|(暑)|(寒)|(涼)|(暖)/",$text) ){

    $xml = simplexml_load_file("weather_temp");

    if( preg_match("/(明日)/",$text) ){
        $desc = $xml->channel->item[2]->description;
        echo $desc;
        echo "<00><05><06>";

    }elseif( preg_match("/(明後日)/",$text) ){
        $desc = $xml->channel->item[3]->description;
        echo $desc;
        echo "<00><07><08>";
    }else{
        // 何も指定しない場合は今日の天気
        $desc = $xml->channel->item[1]->description;
        echo $desc;
        echo "<00><03><04>";
    }

    $pattern = '/[0-9]{2}/';
    preg_match_all($pattern,$desc,$matches);
    $high = $matches[0][1];
    $low = @$matches[0][2] ?: $high-10;// 今日の天気の場合、最低気温が表示されないので-10にする

    /*
    $db = new DB;
    echo $db->showByTemperature($high,$low);
    */

    //if( $low < 18 ){
        echo "カーディガンもお忘れなく<05>";
    //}

}

elseif( preg_match("/(色)|(流行)/",$text)){
    echo "流行りの色はカーキ、白、ブルーです。<00> <02> <04>";
}

elseif( preg_match("/(チャレンジ)|(着てない)|(勝負服)/",$text)){
    echo "こちらがチャレンジコーデです。";
    echo "<0>";
    $db = new DB;
    echo $db->showOldest();
}

else{
    //echo file_get_contents($base_url."userlocal.php?text=".$text);
    echo file_get_contents($base_url."docomo.php?text=".$text);
    //echo "<00><0".rand(2,8).">";
}


