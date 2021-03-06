<?php

/*
 * db.php?json
 *
 */

require_once("common.php");
date_default_timezone_set("Asia/Tokyo");

class DB{

    public $hex = ["00","08","10","30","50","70","90","A0","C0","FF"];

    function initDb(){
        $pdo = new PDO('sqlite:db');
        return $pdo;
    }

    function createDb(){
        $pdo = $this->initDb();
        $pdo->query("DROP TABLE hangers"); // 最初の１回だけコメントアウト
        $pdo->query("CREATE TABLE hangers(num,cmd,color,low,high,last,feature,feature2,feature3)");
        $stmt = $pdo->prepare("INSERT INTO hangers VALUES(?,?,?,?,?,?,?,?,?)");
        // nullの場合は-10を設定,今日 15-25,明日 23-33,明後日 22-32
        $stmt->execute(["1","999","955","10","30","2016-06-11","person,","clothing,sleeve,t shirt,","Cloak,Poncho,Cardigan,"]);
        $stmt->execute(["2","009","559","12","22","2016-06-10","person,wedding,","clothing,sleeve,outerwear,","People,Person,Human,"]);
        $stmt->execute(["3","090","595","14","24","2016-06-01","person","pink,clothing,sleeve,","People,Person,Human,"]);
        $stmt->execute(["4","099","599","16","26","2016-06-10","person,clothing,","person,clothing,","t shirt,white,clothing,","People,Person,Human,"]);
        $stmt->execute(["5","990","995","18","28","2016-06-10","person,wedding","clothing,sleeve,outerwear,","People,Person,Human,"]);
        $stmt->execute(["6","909","959","20","30","2016-06-10","person,shirt,men,t-shirt,design,fabric,","clothing,sleeve,t shirt,","Cloak,Poncho,Cardigan,"]);
        $stmt->execute(["7","777","777","22","32","2016-06-10","person,clothing,fashion,dress,","clothing,sleeve,t shirt,","People,Person,Human,"]);
        $stmt->execute(["8","900","955","24","34","2016-06-10","person,dress,clothing,fashion,","clothing,sleeve,blouse,","People,Person,Human,"]);
        $stmt->execute(["9","090","595","26","36","2016-06-01","person,clothing,","clothing,day dress,sleeve,","People,Person,Human,"]);

        $pdo->query("DROP TABLE users"); // 最初の１回だけコメントアウト
        $pdo->query("CREATE TABLE users(uid,hanger,date)");

    }

    function resetDb(){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE hangers SET cmd=? WHERE num=?");
        for($i=0;$i<10;$i++){
            $stmt->execute(["000",$i]);
        }
    }

    function clear(){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE hangers SET cmd=null");
        $stmt->execute();
    }

    function setFav($uid,$hanger){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE users SET hanger=?,time=? WHERE uid=?");
        $stmt->execute([$hanger,time(),$uid]);
        $rowCount = $stmt->rowCount();
        if( $rowCount == 0 ){
            $stmt = $pdo->prepare("INSERT INTO users VALUES(?,?,?)");
            $stmt->execute([$uid,$hanger,0]);
        }
    }

    function getFav($uid){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE uid=?");
        $stmt->execute([$uid]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result;
    }


    function setColor($digit){

        $num = substr($digit,0,1);
        $color = substr($digit,1,3);

        if( $num == "0" ){
            $this->resetDb();
            return "#000000";
        }else{
            $pdo = $this->initDb();
            $stmt = $pdo->prepare("UPDATE hangers SET cmd=? WHERE num=?");
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
            if( $on == "0" || $on == "00" ){
                echo "00000";
            }else{
                $sql = "SELECT * FROM hangers WHERE num=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$on]);
                while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
                    $color = $result["color"];
                    $stmt = $pdo->prepare("UPDATE hangers SET cmd=? WHERE num=?");
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
    }

    function setDesc($hanger=1,$desc="red"){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE hangers SET feature=? WHERE num=?");
        $stmt->execute([$desc,$hanger]);
        $stmt = $pdo->prepare("UPDATE hangers SET last=? WHERE num=?");
        $stmt->execute([date("Y-m-d"),$hanger]);
    }

    function setFeature($hanger=1,$column,$desc){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE hangers SET ".$column."=? WHERE num=?");
        $stmt->execute([$desc,$hanger]);
    }

    function setFeature3($hanger=1,$feature,$feature2,$feature3){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE hangers SET feature=? WHERE num=?");
        $stmt->execute([$feature,$hanger]);
        $stmt = $pdo->prepare("UPDATE hangers SET feature2=? WHERE num=?");
        $stmt->execute([$feature2,$hanger]);
        $stmt = $pdo->prepare("UPDATE hangers SET feature3=? WHERE num=?");
        $stmt->execute([$feature3,$hanger]);
        $stmt = $pdo->prepare("UPDATE hangers SET last=? WHERE num=?");
        $stmt->execute([date("Y-m-d"),$hanger]);
    }

    function getResults(){
        $pdo = $this->initDb();
        $sql = "SELECT * FROM hangers";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = [];
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($results,$result);
            var_dump($result);
            echo "<br>";
        }
        return $results;
    }

    function getResultsByJSON(){
        $pdo = $this->initDb();
        $sql = "SELECT * FROM hangers";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $results = [];
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            array_push($results,$result);
        }
        return json_encode($results);
    }

    function query($query){
        $pdo = $this->initDb();
        $sql = 'SELECT num FROM hangers WHERE feature LIKE ?';
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
            echo "<br>".PHP_EOL;
        }
    }

    function showOldest(){
        $pdo = $this->initDb();

        $sql = 'SELECT min(last) as minlast FROM hangers';
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $minlast = $result["minlast"];

        $sql = 'SELECT num FROM hangers WHERE last=?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$minlast]);
        $str = "";
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $str = $str."<".$result["num"]."> ";
        }
        echo $str;
    }

    function showByTemperature($low,$high){
        $pdo = $this->initDb();
        $sql = 'SELECT num FROM hangers WHERE low<? AND high>?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$low,$high]);
        $str = "";
        while($result = $stmt->fetch(PDO::FETCH_ASSOC)){
            $str = $str."<".$result["num"]."> ";
        }
        echo $str;
    }
}

if( isset($_GET["create"]) ){
    $db = new DB();
    $db->createDb();
}

if( isset($_GET["reset"]) ){
    $db = new DB();
    $db->createDb();
    copy("default/1.jpg","1.jpg");
    copy("default/2.jpg","2.jpg");
    copy("default/3.jpg","3.jpg");
    copy("default/4.jpg","4.jpg");
    copy("default/5.jpg","5.jpg");
    copy("default/6.jpg","6.jpg");
    copy("default/7.jpg","7.jpg");
    copy("default/8.jpg","8.jpg");
    copy("default/9.jpg","9.jpg");
    header("Location: /sandbox/hanger2");
}

if( isset($_GET["clear"]) ){
    $db = new DB();
    $db->clear();
}

if( isset($_GET["on"]) ){
    $db = new DB();
    $db->onHanger();
}

if( isset($_GET["hanger"]) ){
    $db = new DB();
    echo $db->setColor($_GET["hanger"]);// 4桁指定

    if( isset($_GET["desc"]) ){ // hanger=1&desc=test
        $db = new DB();
        $db->setDesc($_GET["hanger"],$_GET["desc"]);
    }
}

if( isset($_GET["query"]) ){
    $db = new DB();
    $results = $db->query($_GET["query"]);
    echo json_encode($results);
}

if( isset($_GET["show"]) ){
    $db = new DB();
    $db->showByTemperature(15,25);
    //$db->showOldest();
    $db->show();
}

if( isset($_GET["json"]) ){
    $db = new DB();
    echo $db->getResultsByJSON();
}
