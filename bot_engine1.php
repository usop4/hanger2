<?php

$text = $_GET["text"];

if( preg_match("/サザエ/i",$text)){
    echo $text." でございま〜す";
}else{
    echo $text." でございます";
}

