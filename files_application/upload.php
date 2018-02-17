<?php
function imagethumb( $image_src , $image_dest = NULL , $max_size = 100, $expand = FALSE, $square = FALSE )
{
    if( !file_exists($image_src) ) return FALSE;

    // Récupère les infos de l'image
    $fileinfo = getimagesize($image_src);
    if( !$fileinfo ) return FALSE;

    $width     = $fileinfo[0];
    $height    = $fileinfo[1];
    $type_mime = $fileinfo['mime'];
    $type      = str_replace('image/', '', $type_mime);

    if( !$expand && max($width, $height)<=$max_size && (!$square || ($square && $width==$height) ) )
    {
        // L'image est plus petite que max_size
        if($image_dest)
        {
            return copy($image_src, $image_dest);
        }
        else
        {
            header('Content-Type: '. $type_mime);
            return (boolean) readfile($image_src);
        }
    }

    // Calcule les nouvelles dimensions
    $ratio = $width / $height;

    if( $square )
    {
        $new_width = $new_height = $max_size;

        if( $ratio > 1 )
        {
            // Paysage
            $src_y = 0;
            $src_x = round( ($width - $height) / 2 );

            $src_w = $src_h = $height;
        }
        else
        {
            // Portrait
            $src_x = 0;
            $src_y = round( ($height - $width) / 2 );

            $src_w = $src_h = $width;
        }
    }
    else
    {
        $src_x = $src_y = 0;
        $src_w = $width;
        $src_h = $height;

        if ( $ratio > 1 )
        {
            // Paysage
            $new_width  = $max_size;
            $new_height = round( $max_size / $ratio );
        }
        else
        {
            // Portrait
            $new_height = $max_size;
            $new_width  = round( $max_size * $ratio );
        }
    }

    // Ouvre l'image originale
    $func = 'imagecreatefrom' . $type;
    if( !function_exists($func) ) return FALSE;

    $image_src = $func($image_src);
    $new_image = imagecreatetruecolor($new_width,$new_height);

    // Gestion de la transparence pour les png
    if( $type=='png' )
    {
        imagealphablending($new_image,false);
        if( function_exists('imagesavealpha') )
            imagesavealpha($new_image,true);
    }

    // Gestion de la transparence pour les gif
    elseif( $type=='gif' && imagecolortransparent($image_src)>=0 )
    {
        $transparent_index = imagecolortransparent($image_src);
        $transparent_color = imagecolorsforindex($image_src, $transparent_index);
        $transparent_index = imagecolorallocate($new_image, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
        imagefill($new_image, 0, 0, $transparent_index);
        imagecolortransparent($new_image, $transparent_index);
    }

    // Redimensionnement de l'image
    imagecopyresampled(
        $new_image, $image_src,
        0, 0, $src_x, $src_y,
        $new_width, $new_height, $src_w, $src_h
    );

    // Enregistrement de l'image
    $func = 'image'. $type;
    if($image_dest)
    {
        $func($new_image, $image_dest);
    }
    else
    {
        header('Content-Type: '. $type_mime);
        $func($new_image);
    }

    // Libération de la mémoire
    imagedestroy($new_image);

    return TRUE;
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('status' => false));
    exit;
}

$path = 'files/';
$pathVign = 'miniatures/';

if (isset($_FILES['file'])) {

    $originalName = $_FILES['file']['name'];
    $mimetype = mime_content_type($_FILES['file']['tmp_name']);
    $mimes = explode("/",$mimetype);
    $ext = '.'.pathinfo($originalName, PATHINFO_EXTENSION);
    $name=str_replace($ext, "", $originalName);
    error_log($name);
    $data = array(
        'year'=> $_POST['year'],
        'dossier' => $_POST['dossier'],
        'ext'=>$ext,
        'name'=>$name,
        'type'=>$mimes[0],
        'TrueExt'=>$mimes[1]
    );
# Create a connection
    $url = 'http://API_URL/upload';
    $ch = curl_init($url);
# Form data string
    $postString = http_build_query($data, '', '&');
# Setting our options
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = [
        'Content-Type: application/json;charset=utf-8',
        'X-Auth-Token: '.$_POST['token'],
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
# Get the response
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response,true);
    if ($result['message']=='Success'){
        if (!file_exists($path.$_POST['year'])) {
            mkdir($path.$_POST['year'], 0777, true);
        }
        if (!file_exists($pathVign.$_POST['year'])) {
            mkdir($pathVign.$_POST['year'], 0777, true);
        }
        $path.=$_POST['year']."/";
        $pathVign.=$_POST['year']."/";
        if (!file_exists($path.$_POST['dossier'])) {
            mkdir($path.$_POST['dossier'], 0777, true);
        }
        if (!file_exists($pathVign.$_POST['dossier'])) {
            mkdir($pathVign.$_POST['dossier'], 0777, true);
        }
        $path.=$_POST['dossier']."/";
        $pathVign.=$_POST['dossier']."/";
        if (!is_writable($path)) {
            echo json_encode(array(
                'status' => false,
                'msg'    => 'Destination directory not writable.'
            ));
            exit;
        }
        $filePath = $path.$result['name'];
        $fileMin = $pathVign.$result['name'];
        if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
            imagethumb($filePath, $fileMin, 500,true);
            echo json_encode(array(
                'status'        => true,
                'originalName'  => $originalName,
                'generatedName' => $generatedName
            ));
        }

    }


}
else {
    echo json_encode(
        array('status' => false, 'msg' => 'No file uploaded.')
    );
    exit;
}

?>