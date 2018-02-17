<?php


$year = $_GET['year'];
$dossier = $_GET['dossier'];
$doc = $_GET['doc'];
$vign=strval($_GET['vign']);
$token = str_replace(' ', '+', $_GET['token']);

error_log($token);
error_log($year);
error_log($dossier);
error_log($doc);

$url = 'http://API_URL/users/valid';
$ch = curl_init($url);
# Form data string
$data = array();
$postString = http_build_query($data, '', '&');
# Setting our options
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$headers = [
	'X-Auth-Token: '.$token,
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
# Get the response
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response,true);
if ($result['message']=='success'){
    if ($vign=='0'){
        readfile('files/'.$year.'/'.$dossier.'/'.$doc);
    }
    else if ($vign=='1'){
        readfile('miniatures/'.$year.'/'.$dossier.'/'.$doc);
    }

}

