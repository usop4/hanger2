<?php

date_default_timezone_set("Asia/Tokyo");

class DB{

    public $hex = ["00","08","10","30","50","70","90","A0","C0","FF"];

    function initDb(){
        date_default_timezone_set('Asia/Tokyo');
        $pdo = new PDO('sqlite:db');
        return $pdo;
    }

    function createDb(){
        $pdo = $this->initDb();
        $pdo->query("DROP TABLE db"); // 最初の１回だけコメントアウト
        $pdo->query("CREATE TABLE db(num,cmd,color,feature)");
        $stmt = $pdo->prepare("INSERT INTO db VALUES(?,?,?,?)");
        $stmt->execute([1,"900","900","red"]);
        $stmt->execute([2,"009","009","blue"]);
        $stmt->execute([3,"090","090","green"]);
        $stmt->execute([4,"609","609","purple"]);
        $stmt->execute([5,"990","990","yellow"]);
        $stmt->execute([6,"909","909",""]);
        $stmt->execute([7,"777","777","grey"]);
        $stmt->execute([8,"900","900","red"]);
        $stmt->execute([9,"090","090","green"]);
    }

    function resetDb(){
        $this->dump("resetDb");
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE db SET cmd=? WHERE num=?");
        for($i=0;$i<10;$i++){
            $stmt->execute(["000",$i]);
        }
    }

    function clear(){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE db SET cmd=null");
        $stmt->execute();
    }

    function setColor($digit){

        //$this->dump("setColor");
        //$this->dump($digit);
        $num = substr($digit,0,1);
        $color = substr($digit,1,3);

        if( $num == "0" ){
            $this->resetDb();
            return "#000000";
        }else{
            $pdo = $this->initDb();
            $stmt = $pdo->prepare("UPDATE db SET cmd=? WHERE num=?");
            $stmt->execute([$color,$num]);
            $r = $this->hex[ $color[0] ];
            $g = $this->hex[ $color[1] ];
            $b = $this->hex[ $color[2] ];
            return "#".$r.$g.$b;
        }
    }

    function onHanger(){
        $pdo = $this->initDb();

        if( isset($_GET["on"]) ){
            $on = $_GET["on"];
            $sql = "SELECT * FROM db WHERE num=?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$on]);
            while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                $color = $result["color"];
                $stmt = $pdo->prepare("UPDATE db SET cmd=? WHERE num=?");
                if( $result["color"] == $result["cmd"] ){
                    // 既に点灯していたら消す
                    $stmt->execute(["000",$on]);
                    echo $on."000";
                }else{
                    // 消えていたらcolorの値をcmdにコピー
                    $stmt->execute([$color,$on]);
                    echo $on.$result["color"];
                }
            }
        }
    }

    function getResults(){
        $pdo = $this->initDb();
        $sql = "SELECT * FROM db";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = [];
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($results,$result);
        }
        return $results;
    }

    function query($query){
        $pdo = $this->initDb();
        $sql = 'SELECT num FROM db WHERE feature LIKE ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["%".$query."%"]);
        $results = [];
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($results,$result["num"]);
        }
        return $results;
    }

    function show(){
        $results = $this->getResults();
        foreach($results as $result){
            var_dump($result);
            echo "<br>".PHP_EOL;
        }
    }

    function simHanger(){
        $results = $this->getResults();
        foreach($results as $result){
            $r = $this->hex[ $result["cmd"][0] ];
            $g = $this->hex[ $result["cmd"][1] ];
            $b = $this->hex[ $result["cmd"][2] ];
            $str = '<span style="color:#'.$r.$g.$b.'">'.$result["num"].'</font>';
            echo $str;
        }
    }

    function dump($array){
        var_dump($array);
        echo "<BR>".PHP_EOL;
        ob_start();
        var_dump($array);
        $out = ob_get_contents();
        ob_end_clean();
        file_put_contents("log",date(DATE_RFC2822)." ".$out.PHP_EOL,FILE_APPEND);
    }

}

$db = new DB();
if( isset($_GET["create"]) ){
    $db->createDb();
}

if( isset($_GET["reset"]) ){
    $db->resetDb();
}

if( isset($_GET["clear"]) ){
    $db->clear();
}

if( isset($_GET["on"]) ){
    $db->onHanger();
}

if( isset($_GET["hanger"]) ){
    echo $db->setColor($_GET["hanger"]);
}

if( isset($_GET["query"]) ){
    $results = $db->query($_GET["query"]);
    echo json_encode($results);
}
