<?php
echo "GD Loaded: " . (extension_loaded('gd') ? 'Yes' : 'No') . "\n";
echo "Function exists (imagecreatefromjpeg): " . (function_exists('imagecreatefromjpeg') ? 'Yes' : 'No') . "\n";
echo "Function exists (imagecreatefromwebp): " . (function_exists('imagecreatefromwebp') ? 'Yes' : 'No') . "\n";
echo "Function exists (imagecreatefrompng): " . (function_exists('imagecreatefrompng') ? 'Yes' : 'No') . "\n";
echo "Function exists (imagecreatetruecolor): " . (function_exists('imagecreatetruecolor') ? 'Yes' : 'No') . "\n";
echo "Function exists (gd_info): " . (function_exists('gd_info') ? 'Yes' : 'No') . "\n";
if (function_exists('imagetypes')) {
    echo "Image Types: " . imagetypes() . "\n";
    echo "JPEG Support: " . (defined('IMG_JPEG') && (imagetypes() & IMG_JPEG) ? 'Yes' : 'No') . "\n";
    echo "WEBP Support: " . (defined('IMG_WEBP') && (imagetypes() & IMG_WEBP) ? 'Yes' : 'No') . "\n";
}
if (function_exists('gd_info')) {
    print_r(gd_info());
}
?>
