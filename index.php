<?php
var_dump($_POST);
if( isset($_POST["selector"]) ){
    $selector = $_POST["selector"];
    file_put_contents("selector",$selector);
}
?>
<html>
<head>
    <title>Hanger Project</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="assets/css/main.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->

</head>
<body>

<!-- Content -->
<div id="content">
    <div class="inner">

        <!-- Post -->
        <article class="box post post-excerpt">
            <header>
                <h2><a href="#">Interactive hanger with LINE/slack</a></h2>
            </header>
            <div class="info">
                <span class="date"><span id="command">00000</span></span>
                <ul class="stats">
                    <li><a id="hanger01" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger02" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger03" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger04" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger05" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger06" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger07" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger08" href="#" class="icon fa-file">000</a></li>
                    <li><a id="hanger09" href="#" class="icon fa-file">000</a></li>
                </ul>
            </div>
            <p>
                このプロジェクトは、LINEやSlackと・・・（後で書く
            </p>

            <h3>選択中のbot</h3>
            <p>
                <form method="POST" action="index.php">
                    <input type="radio" name="selector" id="bot_engine1" value="bot_engine1">bot_engine1<br>
                    <input type="radio" name="selector" id="bot_simple" value="bot_simple">bot_simple<br>
                    <input type="radio" name="selector" id="repl" value="repl">repl<br>
                    <input type="radio" name="selector" id="userlocal" value="userlocal">userlocal<br>
                    <input type="submit">
                </form>
            </p>

        </article>

        <!-- Post -->

    </div>
</div>

<!-- Sidebar -->
<div id="sidebar">

    <!-- Logo -->
    <h1 id="logo"><a href="#">hanger2</a></h1>

    <!-- Nav -->
    <nav id="nav">
        <ul>
            <li class="current"><a href="#">debug</a></li>
            <li><a href="db.php?reset">reset</a></li>
            <li><a href="db.php?show">db</a></li>
            <li><a href="https://github.com/usopyon/hanger2">github</a></li>
            <li><a href="https://sweetelectronics.wordpress.com/">project</a></li>
        </ul>
    </nav>

    <!-- Copyright -->
    <ul id="copyright">
        <li>&copy; sweet electronics</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
    </ul>

</div>

<iframe id="iframe" src="" width="1" height="1"></iframe>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
<script src="assets/js/main.js"></script>
<script src="https://js.pusher.com/3.1/pusher.min.js"></script>
<script>

    if( window.location.href.match(/https/) ){
        alert("httpに移動します");
        location.href = "http://barcelona-prototype.com/sandbox/hanger2/"
    }

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('558d88d3ce23e25aaf24', {
        encrypted: true
    });

    var channel = pusher.subscribe('test_channel');
    channel.bind('my_event', function(data) {
        //alert(data.message);
        $("#hanger").html(data.message);
        if( data.message.match(/^[0-9]{4,5}$/) ){
            // 5桁だったらハンガーを光らせる
            //setHanger(data.message);
        }else{
            // それ以外だったら
            console.log(data.message);
            playVoice(data.message);
        }
    });

    function setHanger(text){

        console.log(text);

        var num = text.toString().substr(0,2);
        var c1 = text.toString().substr(2,1);
        var c2 = text.toString().substr(3,1);
        var c3 = text.toString().substr(4,1);

        var temp = '#'+toHex(c1)+toHex(c2)+toHex(c3);
        console.log(temp);

        if( text == "00000" ){
            for(var i=0;i<10;i++){
                $("#hanger"+i).css('color','black');
            }
        }else{
            $("#hanger"+num).css('color',temp);
            $("#hanger"+num).html(c1+c2+c3);
        }
        $("#command").html(text);
        //$.getJSON("http://127.0.0.1:8946/arduino/"+text);
    }

    function toHex(num){
        num = parseInt(num);
        if( num == 0 ){
            return "00";
        }else{
            return parseInt(255*num/10).toString(16);
        }
    }

    function playVoice(text){
        url = "voice.php?text="+text
        $("#iframe").attr("src",url);
    }

    // 現在、どのbotが選択されているか
    $.ajax({
        url: "selector",
        cache:false
    }).then(function(data){
        console.log(data);
        $("#"+data).attr("checked","checked");
    }, function(){
        console.log("fail");
    });

    // 初期化
    $.getJSON("db.php?clear");
    //$.getJSON("http://127.0.0.1:8946/arduino/00000");

</script>
</body>
</html>
