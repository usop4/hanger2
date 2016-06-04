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
                <!--
                <span class="date"><span class="month">Jul<span>y</span></span> <span class="day">14</span><span class="year">, 2016</span></span>
                -->
                <span class="date"><span id="command">command</span></span>
                <ul class="stats">
                    <li><a id="hanger1" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger2" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger3" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger4" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger5" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger6" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger7" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger8" href="#" class="icon fa-file"></a></li>
                    <li><a id="hanger9" href="#" class="icon fa-file"></a></li>
                </ul>
            </div>
            <p>このプロジェクトは、LINEやSlackと・・・（後で書く
            </p>
        </article>

        <!-- Post -->

    </div>
</div>

<!-- Sidebar -->
<div id="sidebar">

    <!-- Logo -->
    <h1 id="logo"><a href="#">hanger2</a></h1>

    <!-- Search -->
    <section class="box">
        <form method="post" action="index.php">
            <input type="text" class="text" name="text" placeholder="Command" />
        </form>
    </section>


    <!-- Nav -->
    <nav id="nav">
        <ul>
            <li class="current"><a href="#">debug</a></li>
            <li><a href="http://barcelona-prototype.com/sandbox/hanger2/selector.php">selector</a></li>
            <li><a href="https://github.com/usopyon/hanger2">github</a></li>
            <li><a href="https://sweetelectronics.wordpress.com/">project</a></li>
        </ul>
    </nav>

    <!-- Copyright -->
    <ul id="copyright">
        <li>&copy; sweet electronics</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
    </ul>

</div>

<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/skel.min.js"></script>
<script src="assets/js/util.js"></script>
<!--[if lte IE 8]><script src="assets/js/ie/respond.min.js"></script><![endif]-->
<script src="assets/js/main.js"></script>
<script src="https://js.pusher.com/3.1/pusher.min.js"></script>
<script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('558d88d3ce23e25aaf24', {
        encrypted: true
    });

    var channel = pusher.subscribe('test_channel');
    channel.bind('my_event', function(data) {
        //alert(data.message);
        $("#hanger").html(data.message);
        setHanger(data.message);
    });

    function setHanger(text){
        var num = text.toString().substr(0,1);
        var c1 = text.toString().substr(1,1);
        var c2 = text.toString().substr(2,1);
        var c3 = text.toString().substr(3,1);

        var temp = '#'+toHex(c1)+toHex(c2)+toHex(c3);
        console.log(temp);

        if( text == "0000" ){
            for(var i=0;i<10;i++){
                $("#hanger"+i).css('color','black');
            }
        }else{
            $("#hanger"+num).css('color',temp);
        }
        $("#command").html(text);
    }

    function toHex(num){
        num = parseInt(num);
        if( num == 0 ){
            return "00";
        }else{
            return parseInt(255*num/10).toString(16);
        }
    }

</script>
</body>
</html>
<?php
require_once("slack.php");

if( isset($_POST["text"]) ){
    $text = $_POST["text"];
    $slack = new Slack();
    $slack->sendMessage($text);
}
