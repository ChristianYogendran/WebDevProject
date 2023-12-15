<?php
    require 'ImageResize.php';
    require 'ImageResizeException.php';

    $validimage = false;

    // file_upload_path() - Safely build a path String that uses slashes appropriate for our OS.
    // Default upload path is an 'uploads' sub-folder in the current folder.
    function file_upload_path($original_filename, $upload_subfolder_name = 'images') {
        $current_folder = dirname(__FILE__);
        
        // Build an array of paths segment names to be joins using OS specific slashes.
        $path_segments = [$current_folder, $upload_subfolder_name, basename($original_filename)];
        
        // The DIRECTORY_SEPARATOR constant is OS specific.
        return join(DIRECTORY_SEPARATOR, $path_segments);
     }
 
     // file_is_an_image() - Checks the mime-type & extension of the uploaded file for "image-ness".
     function file_is_an_image($temporary_path, $new_path) {
         $allowed_mime_types      = ['image/gif', 'image/jpeg', 'image/png'];
         $allowed_file_extensions = ['gif', 'jpg', 'jpeg', 'png'];
         
         $actual_file_extension   = pathinfo($new_path, PATHINFO_EXTENSION);
         $actual_mime_type        = getimagesize($temporary_path)['mime'];
         
         $file_extension_is_valid = in_array($actual_file_extension, $allowed_file_extensions);
         $mime_type_is_valid      = in_array($actual_mime_type, $allowed_mime_types);
         
         return $file_extension_is_valid && $mime_type_is_valid;
     }
     
     $image_upload_detected = isset($_FILES['imagefile']) && ($_FILES['imagefile']['error'] === 0);
     $upload_error_detected = isset($_FILES['imagefile']) && ($_FILES['imagefile']['error'] > 0);
 
     if ($image_upload_detected) { 
         $image_filename        = $_FILES['imagefile']['name'];
         $temporary_image_path  = $_FILES['imagefile']['tmp_name'];
         $new_image_path        = file_upload_path($image_filename);
         if (file_is_an_image($temporary_image_path, $new_image_path)) {
             $validimage = true;
             $medium_image = file_upload_path(pathinfo($image_filename, PATHINFO_FILENAME) . '_medium.' . pathinfo($image_filename, PATHINFO_EXTENSION));
             $image = new Gumlet\ImageResize($temporary_image_path);
             $image->resizeToWidth(400);
             $image->save($medium_image);
             move_uploaded_file($temporary_image_path, $new_image_path);
         }
     }

?>