<?php

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
        $pdo->query("CREATE TABLE db(num,color)");
        $stmt = $pdo->prepare("INSERT INTO db VALUES(?,?)");
        $stmt->execute([1,"123"]);
        $stmt->execute([2,"000"]);
        $stmt->execute([3,"000"]);
        $stmt->execute([4,"000"]);
        $stmt->execute([5,"000"]);
        $stmt->execute([6,"000"]);
        $stmt->execute([7,"000"]);
        $stmt->execute([8,"000"]);
        $stmt->execute([9,"009"]);
    }

    function resetDb(){
        $pdo = $this->initDb();
        $stmt = $pdo->prepare("UPDATE db SET color=? WHERE num=?");
        for($i=0;$i<10;$i++){
            $stmt->execute(["000",$i]);
        }
    }

    function setColor($digit){

        $num = substr($digit,0,1);
        $color = substr($digit,1,3);

        if( $num == "0" ){
            $this->resetDb();
            return "#000000";
        }else{
            $pdo = $this->initDb();
            $stmt = $pdo->prepare("UPDATE db SET color=? WHERE num=?");
            $stmt->execute([$color,$num]);
            $r = $this->hex[ $color[0] ];
            $g = $this->hex[ $color[1] ];
            $b = $this->hex[ $color[2] ];
            return "#".$r.$g.$b;
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
            $r = $this->hex[ $result["color"][0] ];
            $g = $this->hex[ $result["color"][1] ];
            $b = $this->hex[ $result["color"][2] ];
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
if( isset($_GET["hanger"]) ){
    echo $db->setColor($_GET["hanger"]);
}
/*
if(strpos($_SERVER["PHP_SELF"],'db.php') !== false){
    $db->show();
}
*/
