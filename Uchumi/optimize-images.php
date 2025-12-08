<?php
// optimize-images.php
function optimizeImage($sourcePath, $maxWidth = 800, $quality = 80) {
    $info = getimagesize($sourcePath);
    $mime = $info['mime'];
    
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }
    
    $width = imagesx($image);
    $height = imagesy($image);
    
    // Calculate new dimensions
    if ($width > $maxWidth) {
        $newHeight = ($height / $width) * $maxWidth;
        $newWidth = $maxWidth;
    } else {
        $newWidth = $width;
        $newHeight = $height;
    }
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG
    if ($mime == 'image/png') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize image
    imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save optimized image
    $optimizedPath = str_replace('.jpg', '-optimized.jpg', $sourcePath);
    
    if ($mime == 'image/jpeg') {
        imagejpeg($newImage, $optimizedPath, $quality);
    } elseif ($mime == 'image/png') {
        imagepng($newImage, $optimizedPath, 9);
    }
    
    imagedestroy($image);
    imagedestroy($newImage);
    
    // Replace original with optimized
    if (filesize($optimizedPath) < filesize($sourcePath)) {
        unlink($sourcePath);
        rename($optimizedPath, $sourcePath);
    } else {
        unlink($optimizedPath);
    }
    
    return true;
}

// Optimize all images in folders
$folders = ['hero', 'products', 'categories'];
foreach ($folders as $folder) {
    $files = glob("images/$folder/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
    foreach ($files as $file) {
        optimizeImage($file);
        echo "Optimized: $file<br>";
    }
}
echo "Optimization complete!";
?>