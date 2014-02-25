<?php
//Send a generated image to the browser
create_image();
exit();

function create_image()
{
    global $_GET;
    if (!filter_var($_GET["q"], FILTER_VALIDATE_REGEXP, array("options" => array('regexp' => '/^[a-z0-9]+$/i')))) {
    	return;
    }
    
  
    $pass = "www.anwesha.info/njath/tchest.php?q=" . $_GET["q"];

    $width = 640;
    $height = 400; 

    $image = ImageCreate($width, $height);  

    $white = ImageColorAllocate($image, 255, 255, 255);
    $black = ImageColorAllocate($image, 0, 0, 0);
    $grey = ImageColorAllocate($image, 204, 204, 204);
    
    

    ImageFill($image, 0, 0, $black); 
    
    $msg = "Try visiting";
    ImageString($image, 12, 35, 13, $msg, $grey);
    ImageString($image, 13, 35, 43, $pass, $white); 

    header("Content-Type: image/jpeg"); 

    ImageJpeg($image);
   
    ImageDestroy($image);
}
?> 