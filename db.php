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
        $pdo->query("CREATE TABLE db(num,cmd,color,low,high,last,feature)");
        $stmt = $pdo->prepare("INSERT INTO db VALUES(?,?,?,?)");
        $stmt->execute([1,"900","900","10","20","2016-06-10","red shirt"]);
        $stmt->execute([2,"009","009","12","22","2016-06-10","blue"]);
        $stmt->execute([3,"090","090","14","24","2016-06-10","green"]);
        $stmt->execute([4,"609","609","16","26","2016-06-10","purple"]);
        $stmt->execute([5,"990","990","18","28","2016-06-10","yellow"]);
        $stmt->execute([6,"909","909","20","30","2016-06-10",""]);
        $stmt->execute([7,"777","777","22","32","2016-06-10","grey"]);
        $stmt->execute([8,"900","900","24","34","2016-06-10","red"]);
        $stmt->execute([9,"090","090","26","36","2016-06-10","green"]);
    }

    function resetDb(){
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
            if( $on == "0" ){
                echo "0000";
            }else{
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
    }

    function setDesc($hanger=1,$desc="red"){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE db SET feature=? WHERE num=?");
        $stmt->execute([$desc,$hanger]);
    }

    function getResults(){
        $pdo = $this->initDb();
        $sql = "SELECT * FROM db";
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
            echo "<br>".PHP_EOL;
        }
    }


}

if( isset($_GET["create"]) ){
    $db = new DB();
    $db->createDb();
}

if( isset($_GET["reset"]) ){
    $db = new DB();
    $db->resetDb();
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
    $db->show();
}
