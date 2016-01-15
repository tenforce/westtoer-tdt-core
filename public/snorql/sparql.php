<?php

extract($_POST);
extract($_GET);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://westtoer-003.openminds.be:8890/sparql");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"query=".urlencode($query) . "&format=json" );

// receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec ($ch);
$res = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close ($ch);
header("HTTP/1.0 " . $res);
echo $server_output;
