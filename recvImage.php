<?php

date_default_timezone_set('Asia/Tokyo');

if( isset($_GET["fname"]) ){
    $fname = $_GET["fname"];
}else{
    $fname = "temp";
}

$temp = file_get_contents("php://input");
$temp = urldecode($temp);
$temp = str_replace("image=data:image/jpeg;base64,","",$temp);
$temp = base64_decode($temp);

file_put_contents($fname,$temp);

