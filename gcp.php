<?php
$api_key = parse_ini_file("api.ini",true)["gcp"]["key"];

$image_path = $_GET["url"];

$feature = "LABEL_DETECTION";

$param = array("requests" => array());
$item["image"] = array("content" => base64_encode(file_get_contents($image_path)));
$item["features"] = array(array("type" => $feature, "maxResults" => 3));
$param["requests"][] = $item;

$json = json_encode($param);

$curl = curl_init() ;
curl_setopt($curl, CURLOPT_URL, "https://vision.googleapis.com/v1/images:annotate?key=" . $api_key);
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_TIMEOUT, 15);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json);
$res1 = curl_exec($curl);
$res2 = curl_getinfo($curl);
curl_close($curl);

$json = substr($res1, $res2["header_size"]);
$array = json_decode($json, true);

$temp = $array["responses"][0]["labelAnnotations"];

if( $temp ){
    foreach( $temp as $a ){
        if( $a ){
            echo $a["description"].",";
        }
    }
}
