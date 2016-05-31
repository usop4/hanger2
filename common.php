<?php

function mydump($fname,$array){
    var_dump($array);
    echo "<BR>".PHP_EOL;
    ob_start();
    var_dump($array);
    $out = ob_get_contents();
    ob_end_clean();
    file_put_contents($fname,date(DATE_RFC2822)." ".$out.PHP_EOL,FILE_APPEND);
}
