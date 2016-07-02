<?php

require_once("common.php");

if( isset($_GET["data"]) ){
    $data = $_GET["data"];
    pushData("$data");
}