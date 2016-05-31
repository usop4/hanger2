<?php

header("Content-Type: text/event-stream\n\n");

$fname = "command";
$ftime = "";
$ftime_old = "";
$contents = "";
$contents_old = "";

while (1) {

    $ftime = filemtime($fname);

    if( $ftime != $ftime_old ){
        $contents = file_get_contents($fname);
        echo 'data:  '.str_replace($contents_old,"",$contents)."\n\n";
        $contents_old = $contents;
    }

    $ftime_old = $ftime;

    clearstatcache();
    ob_end_flush();
    //flush();
    sleep(1);
}