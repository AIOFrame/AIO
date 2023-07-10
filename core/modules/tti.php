<?php

include_once('../includes/encrypt.php');
$e = Encrypt::initiate();

// Define Vars
$text = isset( $_GET['t'] ) ? $e->decrypt( $_GET['t'] ) : '';
$break = $_GET['b'] ?? '';
$size = $_GET['s'] ?? 12;
$rotate = $_GET['r'] ?? 0;
$padding = $_GET['p'] ?? 4;
$color = isset( $_GET['c'] ) ? json_decode( $_GET['c'], 1 ) : ['r'=>255,'g'=>255,'b'=>255];
$bg_color = isset( $_GET['bg'] ) ? json_decode( $_GET['bg'], 1 ) : ['r'=>0,'g'=>0,'b'=>0];
$transparent = $_GET['tr'] ?? 0;
/*echo '<pre>';
print_r( $text );
echo '</pre>';*/
$amount_of_lines= ceil(strlen($text)/$break)+substr_count($text, '\n')+1;
$all_lines = explode("\n", $text);
$text="";
$amount_of_lines = count($all_lines);
$text_final = '';
foreach($all_lines as $key=>$value){
    while( mb_strlen($value,'utf-8')>$break){
        $text_final .= mb_substr($value, 0, $break, 'utf-8')."\n";
        $value = mb_substr($value, $break, null, 'utf-8');
    }
    $text .= mb_substr($value, 0, $break, 'utf-8') . ( $amount_of_lines-1 == $key ? "" : "\n");
}
Header("Content-type: image/png");
$width = $height = $offset_x = $offset_y = 0;
$font = '../../assets/fonts/Lato/Lato-Regular.ttf';

// get the font height.
$bounds = ImageTTFBBox($size, $rotate, $font, "W");
if ($rotate < 0)        {$font_height = abs($bounds[7]-$bounds[1]); }
elseif ($rotate > 0)    {$font_height = abs($bounds[1]-$bounds[7]); }
else { $font_height = abs($bounds[7]-$bounds[1]);}
// determine bounding box.
$bounds = ImageTTFBBox($size, $rotate, $font, $text);
if ($rotate < 0){
    $width = abs($bounds[4]-$bounds[0]);
    $height = abs($bounds[3]-$bounds[7]);
    $offset_y = $font_height;
    $offset_x = 0;
}
elseif ($rotate > 0) {
    $width = abs($bounds[2]-$bounds[6]);
    $height = abs($bounds[1]-$bounds[5]);
    $offset_y = abs($bounds[7]-$bounds[5])+$font_height;
    $offset_x = abs($bounds[0]-$bounds[6]);
}
else{
    $width = abs($bounds[4]-$bounds[6]);
    $height = abs($bounds[7]-$bounds[1]);
    $offset_y = $font_height;
    $offset_x = 0;
}
$image = imagecreate($width+($padding*2)+1,$height+($padding*2)+1);

$background = ImageColorAllocate($image, $bg_color['r'], $bg_color['g'], $bg_color['b']);
$foreground = ImageColorAllocate($image, $color['r'], $color['g'], $color['b']);

if ($transparent) ImageColorTransparent($image, $background);
ImageInterlace($image, true);
ImageTTFText($image, $size, $rotate, $offset_x+$padding, $offset_y+$padding, $foreground, $font, $text);
imagealphablending($image, true);
imagesavealpha($image, true);
imagePNG($image);