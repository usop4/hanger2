<html>
<head>
    <title>Hanger Project</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!--[if lte IE 8]><script src="assets/js/ie/html5shiv.js"></script><![endif]-->
    <link rel="stylesheet" href="assets/css/main.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="assets/css/ie8.css" /><![endif]-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script>
        function checkHanger(){
            $('#iframe_id')[0].contentDocument.location.reload(true);
        }
        setInterval(checkHanger,3000);
    </script>

</head>
<body>

<!-- Content -->
<div id="content">
    <div class="inner">

        <!-- Post -->
        <article class="box post post-excerpt">
            <header>
                <h2><a href="#">Debug Page</a></h2>
                <p>Interactive hanger with LINE or slack.</p>
            </header>
            <div class="info">
                <span class="date"><span class="month">Jul<span>y</span></span> <span class="day">14</span><span class="year">, 2016</span></span>
                <ul class="stats">
                    <li><a href="#" class="icon fa-comment">16</a></li>
                    <li><a href="#" class="icon fa-heart">32</a></li>
                    <li><a href="#" class="icon fa-twitter">64</a></li>
                    <li><a href="#" class="icon fa-facebook">128</a></li>
                </ul>
            </div>
            <iframe src="simHanger.php" id="iframe_id"></iframe>

            <p>
                <ul id="eventList">
                    <li>0000：リセット（全て黒：消灯）</li>
                    <li>1900：ハンガー１が赤</li>
                    <li>2090：ハンガー２が緑</li>
                    <li>1：ハンガー１を、あらかじめ登録された色（赤）で点灯</li>
                    <li>red：特徴にredが含まれているハンガーを点灯</li>
                </ul>
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

<script>
    var eventList = document.getElementById("eventList");
    var evtSource = new EventSource("sse.php");

    evtSource.onmessage = function(e) {
        var newElement = document.createElement("li");

        newElement.innerHTML = e.data;
        eventList.appendChild(newElement);
    }

    function stop(){
        evtSource.close();
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
