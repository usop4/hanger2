<?php

function mydump($fname,$data){
    ob_start();
    var_dump($data);
    $out = ob_get_contents();
    ob_end_clean();
    file_put_contents($fname,date(DATE_RFC2822)." ".$out.PHP_EOL,FILE_APPEND);
}
