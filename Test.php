<?php
// Subdirectory for the images
$directory = 'Images';


// What image to use as the input
$original_image = __DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . 'TheKiss.original.png';


// Load images
$original = imagecreatefrompng($original_image);
$width = imagesx($original);
$height = imagesy($original);
$new = imagecreatetruecolor($width, $height);

// Neuron output value must be equal 
// or greater than this value to be 
// converted to a 1.
$threshold = 0.9999999;

// Load ANN
//$train_file = (dirname(__FILE__) . "/MyVisualANN.train.net"); // Load training snapshot
//$train_file = (dirname(__FILE__) . "/MyVisualANN.final.net"); // Load final snapshot
$train_file = (dirname(__FILE__) . "/MyVisualANN.0.00017.net"); // A Pre-trained example
if (!is_file($train_file)){
    die("The file .net has not been created!" . PHP_EOL);
}

$ann = fann_create_from_file($train_file);

if ($ann) {
    
    for($row = 0; $row < $width; $row += 2){
        for($col = 0; $col < $height; $col+= 2){
            // Iterate through the pixels on a 2x2
            // grid considering each pixel only once.
            // movement is from left to right, top to bottom
            //    C0   C1   C2   C3 
            //R0 [p1] [p2] [..] [..] ...
            //R1 [p3] [p4] [..] [..] ...
            //R2 [..] [..] [..] [..] ...
            //R3 [..] [..] [..] [..] ...
            //...
            
            // Get Colors
            $p1 = @imagecolorat($original, $row, $col);
            $p2 = @imagecolorat($original, $row, $col+1);        
            $p3 = @imagecolorat($original, $row+1, $col);
            $p4 = @imagecolorat($original, $row+1, $col+1);

            // Process decimal values into strings of binary & pad to 8 bits
            $p1_r = str_pad(decbin(($p1 >> 16) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p1_g = str_pad(decbin(($p1 >> 8) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p1_b = str_pad(decbin($p1 & 0xFF), 8, 0, STR_PAD_LEFT);
            $p2_r = str_pad(decbin(($p2 >> 16) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p2_g = str_pad(decbin(($p2 >> 8) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p2_b = str_pad(decbin($p2 & 0xFF), 8, 0, STR_PAD_LEFT);
            $p3_r = str_pad(decbin(($p3 >> 16) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p3_g = str_pad(decbin(($p3 >> 8) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p3_b = str_pad(decbin($p3 & 0xFF), 8, 0, STR_PAD_LEFT);
            $p4_r = str_pad(decbin(($p4 >> 16) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p4_g = str_pad(decbin(($p4 >> 8) & 0xFF), 8, 0, STR_PAD_LEFT);
            $p4_b = str_pad(decbin($p4 & 0xFF), 8, 0, STR_PAD_LEFT);

            // Build Input Array
            $input = str_split("$p1_r$p1_g$p1_b$p2_r$p2_g$p2_b$p3_r$p3_g$p3_b$p4_r$p4_g$p4_b");
            
            // Bot Calc
            $result = fann_run($ann, $input);

            $current_color_channel = -1;
            $colors = array();
            
            // Output Neurons to binary bits
            foreach($result as $index=>&$bit){
                if(!is_float($index/8)){
                    $current_color_channel++;
                }
                
                if($bit > $threshold){
                    $bit = '1';
                }
                else{
                    $bit = '0';
                }
                @$colors[$current_color_channel] .= $bit;
            }
                        
    
            // 96 bits as 8 bit binary values
            // converted to decimal
            $p1_r = bindec($colors[0]); // bits 0 - 7
            $p1_g = bindec($colors[1]); // bits 8 - 15
            $p1_b = bindec($colors[2]); // bits 16 - 23
            $p2_r = bindec($colors[3]); // bits 24 - 31
            $p2_g = bindec($colors[4]); // bits 32 - 39
            $p2_b = bindec($colors[5]); // bits 40 - 47
            $p3_r = bindec($colors[6]); // bits 48 - 55
            $p3_g = bindec($colors[7]); // bits 56 - 63
            $p3_b = bindec($colors[8]); // bits 64 - 71
            $p4_r = bindec($colors[9]); // bits 72 - 79
            $p4_g = bindec($colors[10]);// bits 80 - 87
            $p4_b = bindec($colors[11]);// bits 88 - 95
            
            // Allocate colors
            $p1_color = imagecolorallocate($new, $p1_r, $p1_g, $p1_b); 
            $p2_color = imagecolorallocate($new, $p2_r, $p2_g, $p2_b); 
            $p3_color = imagecolorallocate($new, $p3_r, $p3_g, $p3_b); 
            $p4_color = imagecolorallocate($new, $p4_r, $p4_g, $p4_b); 
            
            // Paint pixels
            imagesetpixel($new, $row, $col, $p1_color); // p1
            imagesetpixel($new, $row, $col+1, $p1_color); // p2
            imagesetpixel($new, $row+1, $col, $p1_color); // p3
            imagesetpixel($new, $row+1, $col+1, $p1_color); // p4
            
        }
        echo 'Processing Row ' . $row . PHP_EOL;
    }
    
    imagedestroy($original); // remove original from memory
    imagepng($new, __DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . 'new.png'); // Save
    imagedestroy($new);      // remove new from memory
    fann_destroy($ann); // Remove ANN from memory
} else {
    die("Invalid file format" . PHP_EOL);
}

echo 'All Done!';
