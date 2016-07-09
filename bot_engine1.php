<?php

require_once("common.php");
require_once("db.php");

$text = $_GET["text"];

if( preg_match("/(help)|(こんにちは)/i",$text)){
    echo "こんにちは。ハンガーbotです。\n"
        ."天気、流行、\n"
        ."などの言葉を元に、おすすめの服を選びます。<0>";
}

elseif( preg_match("/リセット/",$text)){
    file_get_contents($base_url."db.php?reset");
    echo "# DBをリセットしました<0>";
}

elseif( preg_match("/(色)|(流行)/",$text)){
    echo "流行りの色はカーキ、白、ブルーです。<0> <2> <4>";
}

elseif( preg_match("/(天気)|(暑)|(寒)|(涼)|(暖)/",$text) ){

    // Livedoorの天気を利用
    // http://weather.livedoor.com/forecast/rss/area/130010.xml
    // 10日（日）の天気は晴時々曇、最高気温は32℃ 最低気温は22℃でしょう。
    $url = "http://weather.livedoor.com/forecast/rss/area/130010.xml";
    $debug = true; // trueにすると、Livedoorに問い合わせず、ローカルファイルを利用
    if( $debug != true ){
        file_put_contents("weather_temp",file_get_contents($url));
    }
    $xml = simplexml_load_file("weather_temp");

    if( preg_match("/(明日)/",$text) ){
        $desc = $xml->channel->item[2]->description;
    }elseif( preg_match("/(明後日)/",$text) ){
        $desc = $xml->channel->item[3]->description;
    }else{
        // 何も指定しない場合は今日の天気
        $desc = $xml->channel->item[1]->description;
    }
    echo $desc;

    /* メモ
    今日 null-25(15-25)　→　カーディガン
    明日 23-33
    明後日 22-32

    nullの場合は-10を設定
    1.カーディガン minが20以下

    */

    $pattern = '/[0-9]{1,2}/';
    preg_match_all($pattern,$desc,$matches);
    $high = $matches[0][1];
    $low = @$matches[0][2] ?: $high-10;

    echo "カーディガンもお忘れなく<0><".rand(1,9).">";
}

elseif( preg_match("/(色)|(流行)/",$text)){
    echo "流行りの色はカーキ、白、ブルーです。<0> <2> <4>";
}

elseif( preg_match("/(チャレンジ)|(着てない)/",$text)){
    echo "こちらがチャレンジコーデです。";
    echo "<0>";
    $db = new DB;
    echo $db->showOldest();
}

else{

    $results = file_get_contents($base_url."db.php?query=".$text);
    $results = json_decode($results);
    foreach($results as $result){
        file_get_contents($base_url."db.php?on=".$result);
        echo $result."\n";
    }

    echo $text." でございます";
}


