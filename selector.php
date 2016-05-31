<?php

date_default_timezone_set("Asia/Tokyo");

require_once("common.php");

if( isset($_GET["text"]) ){

    $text = $_GET["text"];
    $bot = file_get_contents("selector");
    echo file_get_contents($bot.$text);

}else{

    require_once("selector");

    echo <<<EOL
<form method="POST" action="selector.php">
    <p></p>
    <input type="radio" name="selector" value="http://barcelona-prototype.com/sandbox/hanger2/bot_simple.php?text=">bot_simple<br>
    <input type="radio" name="selector" value="http://barcelona-prototype.com/sandbox/hanger2/repl.php?text=">repl<br>
    <input type="submit">
</form>
<a href="index.php">index.php</a>
EOL;

    if( isset($_POST["selector"]) ){
        file_put_contents("selector",$_POST["selector"]);
    }

}
