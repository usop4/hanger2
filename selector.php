<?php

date_default_timezone_set("Asia/Tokyo");

require_once("common.php");

if( isset($_GET["text"]) ){

    $ini = parse_ini_file("api.ini",true)["selector"];

    $text = $_GET["text"];
    $bot = $ini[file_get_contents("selector")];

    echo file_get_contents($bot.$text);

}