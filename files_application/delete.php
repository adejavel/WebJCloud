<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');


$token = str_replace(' ', '+', $_GET['token']);
$id=$_GET['id'];
$isDoc=strval($_GET['doc']);


if ($isDoc=='1'){
    $url = 'http://API_URL/documents/delete/'.$id;
    $res = json_decode(isAllowed($token,$url),true);
    if ($res["message"]=='success'){
        $pathFile = 'files/'.$res['year'].'/'.$res['folder'].'/'.$res['url'];
        $pathMin = 'miniatures/'.$res['year'].'/'.$res['folder'].'/'.$res['url'];
        unlink($pathFile);
        unlink($pathMin);
        echo json_encode(
            array('status' => true, 'msg' => 'success')
        );
    }
    echo json_encode(
        array('status' => true, 'url' => $url,'token'=>$token,'id'=>$id)
    );

}

if ($isDoc=='0'){
    $url='http://API_URL/folders/delete/'.$id;
    $res = json_decode(isAllowed($token,$url),true);
    if ($res['message']=='success'){
        $year = $res['year'];
        $dossier = $res['url'];
        $dirFile = 'files/'.$year.'/'.$dossier;
        $dirMin = 'miniatures/'.$year.'/'.$dossier;
        if (is_dir($dirFile)) {
            $objects = scandir($dirFile);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {

                    if (filetype($dirFile."/".$object) == "dir") rmdir($dirFile."/".$object); else unlink($dirFile."/".$object);
                }
            }
            reset($objects);
            rmdir($dirFile);

        }
        if (is_dir($dirMin)) {
            $objects = scandir($dirMin);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {

                    if (filetype($dirMin."/".$object) == "dir") rmdir($dirMin."/".$object); else unlink($dirMin."/".$object);
                }
            }
            reset($objects);
            rmdir($dirMin);

        }
        echo json_encode(
            array('status' => true, 'msg' => 'success')
        );
    }
}


function isAllowed($token,$url){

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = [
        'X-Auth-Token: '.$token,
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($response,true);
    return $response;


    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
            "x-auth-token: ".$token
        ),
    ));


    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    return $response;
    
}