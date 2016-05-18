<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16/04/30
 * Time: 11:50
 */

class Slack{

    public $ini;

    function Slack(){
        $this->ini = parse_ini_file("api.ini",true)["slack"];
    }

    function sendMessage($text,$color=null){

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Accept: application/json",
            "Content-type: application/json"
        ]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $this->ini["hook"]);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");

        if( $color!=null ){
            $postfields = '{"text":"'.$text.'","attachments":[{"color":"'.$color.'","text":"'.$color.'"}]}';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        }else{
            $postfields = [
                'text'=>$text
            ];
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postfields));
        }

        return curl_exec($curl);
    }

    function dump($var,$reset=false){
        if( $reset ){
            file_put_contents("log","");
        }
        var_dump($var);
        echo "<BR>".PHP_EOL;
        ob_start();
        var_dump($var);
        $out = ob_get_contents();
        ob_end_clean();
        file_put_contents("log",date(DATE_RFC2822)." ".$out.PHP_EOL,FILE_APPEND);
    }

}
/*
$slack = new Slack();
$slack->sendMessage("1234");
*/