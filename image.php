<?php
    session_start();

    // simple image to display URL (cut off)
    $im = imagecreate(255, 170);

    // white background and blue text
    $bg = imagecolorallocate($im, 255, 255, 255);
    $textcolor = imagecolorallocate($im, 0, 0, 255);

    $textgen = "abc123";

    $_SESSION["captcha"] = $textgen;

    /*if(!isset($_SESSION["captcha"]))
    {
        $_SESSION["captcha"] = $textgen;
    }
    */

    // write the string at the top left
    imagestring($im, 5, 0, 0, $textgen, $textcolor);

    // Output the image
    header('Content-type: image/png');

    ob_start();
    imagepng($im);
    $image_bin = ob_get_contents();
    ob_end_clean();

    imagedestroy($im);

    echo $image_bin;

    exit();
?>