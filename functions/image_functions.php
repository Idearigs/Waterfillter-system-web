<?php
/**
 * Image handling functions for partner logos
 */

/**
 * Upload a partner logo image
 * 
 * @param array $file The uploaded file ($_FILES['logo'])
 * @param string $partnerName The name of the partner
 * @return array Returns an array with status, message, and file path if successful
 */
function uploadPartnerLogo($file, $partnerName) {
    $result = [
        'status' => false,
        'message' => '',
        'path' => ''
    ];
    
    // Check if file was uploaded without errors
    if(isset($file) && $file['error'] == 0) {
        $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $file['name'];
        $filetype = $file['type'];
        $filesize = $file['size'];
        
        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) {
            $result['message'] = "Please select a valid file format (JPG, JPEG, PNG, GIF).";
            return $result;
        }
        
        // Verify file size - 5MB maximum
        $maxsize = 5 * 1024 * 1024;
        if($filesize > $maxsize) {
            $result['message'] = "File size is larger than the allowed limit (5MB).";
            return $result;
        }
        
        // Verify MIME type of the file
        if(in_array($filetype, $allowed)) {
            // Check whether file exists before uploading it
            $target_dir = "../assets/images/partners/";
            
            // Create directory if it doesn't exist
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Create a unique filename based on partner name and timestamp
            $sanitized_name = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($partnerName)));
            $new_filename = $sanitized_name . "_" . time() . "." . $ext;
            $target_file = $target_dir . $new_filename;
            
            // Upload file
            if(move_uploaded_file($file['tmp_name'], $target_file)) {
                // File uploaded successfully
                $result['status'] = true;
                $result['message'] = "Logo uploaded successfully.";
                $result['path'] = "assets/images/partners/" . $new_filename;
                return $result;
            } else {
                $result['message'] = "There was an error uploading your file.";
                return $result;
            }
        } else {
            $result['message'] = "There was a problem with the file upload. Please try again.";
            return $result;
        }
    } else {
        $result['message'] = "Please select a logo image to upload.";
        return $result;
    }
    
    return $result;
}

/**
 * Delete a partner logo image
 * 
 * @param string $filepath The path to the file to delete
 * @return boolean Returns true if file was deleted successfully, false otherwise
 */
function deletePartnerLogo($filepath) {
    $fullpath = "../" . $filepath;
    
    // Check if file exists
    if(file_exists($fullpath)) {
        // Delete the file
        if(unlink($fullpath)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Resize and optimize an image
 * 
 * @param string $source_path The source image path
 * @param string $target_path The target image path
 * @param int $max_width Maximum width of the image
 * @param int $max_height Maximum height of the image
 * @param int $quality Image quality (0-100)
 * @return boolean Returns true if image was resized successfully, false otherwise
 */
function resizeImage($source_path, $target_path, $max_width = 200, $max_height = 100, $quality = 80) {
    // Get image info
    $info = getimagesize($source_path);
    
    if($info === false) {
        return false;
    }
    
    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];
    
    // Calculate new dimensions
    $new_width = $width;
    $new_height = $height;
    
    if($width > $max_width) {
        $new_width = $max_width;
        $new_height = ($max_width / $width) * $height;
    }
    
    if($new_height > $max_height) {
        $new_height = $max_height;
        $new_width = ($max_height / $new_height) * $new_width;
    }
    
    // Create new image
    $source_image = null;
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Set transparency for PNG and GIF
    if($mime == 'image/png' || $mime == 'image/gif') {
        imagecolortransparent($new_image, imagecolorallocate($new_image, 0, 0, 0));
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
    }
    
    // Create source image based on mime type
    switch($mime) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    // Resize image
    imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    // Save image based on mime type
    $result = false;
    switch($mime) {
        case 'image/jpeg':
            $result = imagejpeg($new_image, $target_path, $quality);
            break;
        case 'image/png':
            $png_quality = 9 - round(($quality / 100) * 9);
            $result = imagepng($new_image, $target_path, $png_quality);
            break;
        case 'image/gif':
            $result = imagegif($new_image, $target_path);
            break;
    }
    
    // Free memory
    imagedestroy($source_image);
    imagedestroy($new_image);
    
    return $result;
}
?>
