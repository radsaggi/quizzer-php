<?php
//Send a generated image to the browser
create_image();
exit();

function endsWith($haystack, $needle) {
    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
}

function create_image()
{
    global $_GET;
    if (!filter_var($_GET["q"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[a-z0-9]+$/i')))) {
    	return;
    }
    if (!filter_var($_GET["img"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[a-z0-9]+\.(jpg|png)$/i')))) {
    	return;
    }
    
    if (!file_exists($_GET["img"])) {
    	echo "File does not exist!!";
    	return;
    }
    
    $image_orig = ImageCreateFromPNG($_GET["img"]);
    
    if (!$image_orig) {
        var_dump($_GET);
    	echo "Joke create";
    	return;
    }
    
    $pass = "www.anwesha.info/njath/tchest.php?q=" . $_GET["q"];

    $width = 640;
    $height = 500; 

    $image = ImageCreateTrueColor($width, $height);  
    
    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 204, 204, 204);

    ImageCopyResampled($image, $image_orig, 0, 0, 0, 0, 640, 400, 640, 400);
    
    $msg = "Try visiting";
    ImageString($image, 12, 35, 413, $msg, $grey);
    ImageString($image, 13, 35, 443, $pass, $white); 

    header("Content-Type: image/jpeg"); 

    ImageJpeg($image);
    ImageDestroy($image_orig);
    ImageDestroy($image);
}
?> 