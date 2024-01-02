<?php

function resize_image($file, $new_size)
{
    try {
        if(file_exists($file)) {

            $type = array_slice(explode('/', mime_content_type($file)), -1, 1)[0];


            if ($type == 'jpg' || $type == 'jpeg') {
                $original_image = imagecreatefromjpeg($file);
            } else if ($type == 'png') {
                $original_image = imagecreatefrompng($file);
            } else if ($type == 'webp') {
                $original_image = imagecreatefromwebp($file);
            } else {
                $_SESSION['errorType'] = 'resize_image function error: unknown image extension...'.$type;
                header('Location: ../error.php');
                die();
            }

            $original_width = imagesx($original_image);
            $original_height = imagesy($original_image);

            # calculating aspect ratio and set new parameters
            if ($original_width < $original_height) {
                $ratio = $new_size / $original_width;
                $new_width = $new_size;
                $new_height = $original_height * $ratio;



                $x_offset = 0;
                $y_offset = ($original_height - $original_width) / 2;
            } else {
                $ratio = $new_size / $original_height;
                $new_height = $new_size;
                $new_width = $original_width * $ratio;

                $x_offset = ($original_width - $original_height) / 2;
                $y_offset = 0;
            }
            #





            #creating and saving new image

            $new_image = imagecreatetruecolor($new_size, $new_size);
            imagecopyresampled($new_image, $original_image, 0, 0, $x_offset, $y_offset, $new_width, $new_height, $original_width, $original_height);
            imagejpeg($new_image, $file);


        } else {
            $_SESSION['errorType'] = 'resize_image function error: file does not exist. '.$file;
            header('Location: ../error.php');
            die();
        }

    } catch (Exception $e) {
    $_SESSION['errorType'] = 'resize image function failed: '.$e;
    header('Location: ../error.php');
    die();
}

}