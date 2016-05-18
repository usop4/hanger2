<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
<script>
    function checkHanger(){
        $('#iframe_id')[0].contentDocument.location.reload(true);
    }
    setInterval(checkHanger,3000);
</script>

test page<br>

<iframe src="simHanger.php" id="iframe_id"></iframe>

<form method="POST" action="index.php">
    <input type="text" name="text" value="0000">
    <input type="submit">
</form>

<ul>
    <li>0000：リセット（全て黒：消灯）</li>
    <li>1900：ハンガー１が赤</li>
    <li>2090：ハンガー２が緑</li>
</ul>

<?php
require_once("slack.php");

if( isset($_POST["text"]) ){
    $text = $_POST["text"];
    $slack = new Slack();
    $slack->sendMessage($text);
}