<?php

$text = $_GET["text"];
$base_url = parse_ini_file("api.ini",true)["base_url"];

if( preg_match("/(help)|(こんにちは)/i",$text)){
    echo "こんにちは。ハンガーbotです。\n"
        ."天気、最近、\n"
        ."などの言葉を元に、おすすめの服を選ぶよ";
}

elseif( preg_match("/リセット/i",$text)){
    // リセット
    file_get_contents($base_url."db.php?reset");
    echo "# DBをリセットしました";
}

else{

    $results = file_get_contents($base_url."db.php?query=".$text);
    $results = json_decode($results);
    foreach($results as $result){
        file_get_contents($base_url."db.php?on=".$result);
        echo $result."\n";
    }

    echo "# ".$text." でございます";
}


