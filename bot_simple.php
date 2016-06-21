<?php

$text = $_GET["text"];

if( preg_match("/サザエ/i",$text)){
    echo $text." でございま〜す";
}elseif( preg_match("/test/i",$text)){
    echo $text." でございます <1>";
}else{
    echo $text." でございます";
}

