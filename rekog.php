<?php

require_once("common.php");
date_default_timezone_set("Asia/Tokyo");

require 'aws-autoloader.php';

$fname = $_GET["fname"];

use Aws\Credentials\CredentialProvider;
$ini = 'credentials.ini';
$iniProvider = CredentialProvider::ini('rekog', $ini);
$iniProvider = CredentialProvider::memoize($iniProvider);

$s3client = new Aws\S3\S3Client([
    'region'   => 'us-west-2',
    'version'  => '2006-03-01',
    'credentials' => $iniProvider
]);

//バケットに入れる。
$bucketName = 'rekog-sweetelectronics';
$keyName = $fname;
$srcFile = $fname;

$s3client->putObject([
    'Bucket' => $bucketName,
    'Key' => $keyName,
    'SourceFile' => $srcFile,
    'ContentType'=> mime_content_type($srcFile)
]);

// 認識
$client = new Aws\Rekognition\RekognitionClient([
    'region'   => 'us-west-2',
    'version'  => '2016-06-27',
    'credentials' => $iniProvider
]);

try{
    $result = $client->detectLabels([
        'Image' => [ // REQUIRED
            'S3Object' => [
                'Bucket' => 'rekog-sweetelectronics',
                'Name' => $fname
            ],
        ],
        'MaxLabels' => 3
    ]);

    $result = json_encode( (array)$result );
    $result = str_replace("\u0000", "", $result);
    $result = json_decode( $result, true );
    $result = $result["Aws\Resultdata"]["Labels"];

    foreach( $result as $r ){
        echo $r["Name"].",";
    }

}catch(Exception $e){
    echo $e->getMessage();
}

