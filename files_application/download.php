<?php

$year = $_GET['year'];
$dossier = $_GET['dossier'];
$doc = $_GET['doc'];
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
	$filePath='files/'.$year.'/'.$dossier.'/'.$doc;
	if(file_exists($filePath)) {
        $fileName = basename($filePath);
        $fileSize = filesize($filePath);

        // Output headers.
        header("Cache-Control: private");
        header("Content-Type: application/stream");
        header("Content-Length: ".$fileSize);
        header("Content-Disposition: attachment; filename=".$fileName);

        // Output file.
        readfile ($filePath);                   
        exit();
    }
    else {
        die('The provided file path is not valid.');
    }
}