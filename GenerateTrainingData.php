<?php

// Subdirectory for the images
$directory = 'Images';


// You can has glob all the images?
// this only works if there is an original & edited for each image
$original_images = glob(__DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . "*.original.png");
$edited_images = glob(__DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . "*.edited.png");


// Technically, this is unnecessary... but I like the 
// cleanliness and ease of use of having both the
// original & edited images on the same array index
$images = array();
foreach($original_images as $index=>$image){
    $images[$index]['original'] = $image;
    $images[$index]['edited'] = $edited_images[$index];
}
$original_images = NULL;
$edited_images = NULL;
unset($original_images);
unset($edited_images);


// Training Data Variables
$training_file = fopen('MyVisualTraningData.data', 'w');
$number_of_training_data = 0;


// ANN info
$number_of_inputs = 96;
$number_of_outputs = 96;
$size_of_pixel_matrix = 2*2; // Pixel matrix width * height

// Loop through the set of images and create training data
foreach($images as $index=>$image_set){
    
    // Load images
    $original = imagecreatefrompng($image_set['original']);
    $edited = imagecreatefrompng($image_set['edited']);
    
    // Get image sizes
    if(imagesx($original) == imagesx($edited) && imagesy($original) == imagesy($edited)){
        $width = imagesx($original);
        $height = imagesy($original);        
    }
    else{
        die('Original and Edited Images are not the same size.');
    }
    
    if($index == 0){
        // this assumes all input and output images are the same size
        // and this function would need to be modified to do this
        // calculation for all images and add the results before it can 
		// handle different size images
        $number_of_training_data = floor(($width * $height) / $size_of_pixel_matrix);
        $number_of_training_data *= count($images);
        
        // write header
        fwrite($training_file, "$number_of_training_data $number_of_inputs $number_of_outputs");
    }
    

    // Iterate through the pixels on a 2x2
    // grid considering each pixel only once.
    // movement if from left to right, top to bottom
    //    C0   C1   C2   C3 
    //R0 [p1] [p2] [..] [..] ...
    //R1 [p3] [p4] [..] [..] ...
    //R2 [..] [..] [..] [..] ...
    //R3 [..] [..] [..] [..] ...
    //...
    for($row = 0; $row < $width; $row += 2){
        for($col = 0; $col < $height; $col+= 2){
            // Get Original colors
            $p1_original = @imagecolorat($original, $row, $col);
            // R+G+B
            $p1_original_r = ($p1_original >> 16) & 0xFF;
            $p1_original_g = ($p1_original >> 8) & 0xFF;
            $p1_original_b = $p1_original & 0xFF;
            
            // Get color
            $p2_original = @imagecolorat($original, $row, $col+1);
            // R+G+B
            $p2_original_r = ($p2_original >> 16) & 0xFF;
            $p2_original_g = ($p2_original >> 8) & 0xFF;
            $p2_original_b = $p2_original & 0xFF;

            
            // Get color
            $p3_original = @imagecolorat($original, $row+1, $col);
            // R+G+B
            $p3_original_r = ($p3_original >> 16) & 0xFF;
            $p3_original_g = ($p3_original >> 8) & 0xFF;
            $p3_original_b = $p3_original & 0xFF;

            
            // Get color
            $p4_original = @imagecolorat($original, $row+1, $col+1);
            // R+G+B
            $p4_original_r = ($p4_original >> 16) & 0xFF;
            $p4_original_g = ($p4_original >> 8) & 0xFF;
            $p4_original_b = $p4_original & 0xFF;
            
            
            // Get Edited colors
            $p1_edited = @imagecolorat($edited, $row, $col);
            // R+G+B
            $p1_edited_r = ($p1_edited >> 16) & 0xFF;
            $p1_edited_g = ($p1_edited >> 8) & 0xFF;
            $p1_edited_b = $p1_edited & 0xFF;
            
            // Get color
            $p2_edited = @imagecolorat($edited, $row, $col+1);
            // R+G+B
            $p2_edited_r = ($p2_edited >> 16) & 0xFF;
            $p2_edited_g = ($p2_edited >> 8) & 0xFF;
            $p2_edited_b = $p2_edited & 0xFF;

            
            // Get color
            $p3_edited = @imagecolorat($edited, $row+1, $col);
            // R+G+B
            $p3_edited_r = ($p3_edited >> 16) & 0xFF;
            $p3_edited_g = ($p3_edited >> 8) & 0xFF;
            $p3_edited_b = $p3_edited & 0xFF;

            
            // Get color
            $p4_edited = @imagecolorat($edited, $row+1, $col+1);
            // R+G+B
            $p4_edited_r = ($p4_edited >> 16) & 0xFF;
            $p4_edited_g = ($p4_edited >> 8) & 0xFF;
            $p4_edited_b = $p4_edited & 0xFF;
            
         
            // Process values into binary & Pad to 8 bits
            $p1_original_r = str_pad(decbin($p1_original_r), 8, 0, STR_PAD_LEFT);
            $p1_original_g = str_pad(decbin($p1_original_g), 8, 0, STR_PAD_LEFT);
            $p1_original_b = str_pad(decbin($p1_original_b), 8, 0, STR_PAD_LEFT);
            $p2_original_r = str_pad(decbin($p2_original_r), 8, 0, STR_PAD_LEFT);
            $p2_original_g = str_pad(decbin($p2_original_g), 8, 0, STR_PAD_LEFT);
            $p2_original_b = str_pad(decbin($p2_original_b), 8, 0, STR_PAD_LEFT);
            $p3_original_r = str_pad(decbin($p3_original_r), 8, 0, STR_PAD_LEFT);
            $p3_original_g = str_pad(decbin($p3_original_g), 8, 0, STR_PAD_LEFT);
            $p3_original_b = str_pad(decbin($p3_original_b), 8, 0, STR_PAD_LEFT);
            $p4_original_r = str_pad(decbin($p4_original_r), 8, 0, STR_PAD_LEFT);
            $p4_original_g = str_pad(decbin($p4_original_g), 8, 0, STR_PAD_LEFT);
            $p4_original_b = str_pad(decbin($p4_original_b), 8, 0, STR_PAD_LEFT);
            $p1_edited_r = str_pad(decbin($p1_edited_r), 8, 0, STR_PAD_LEFT);
            $p1_edited_g = str_pad(decbin($p1_edited_g), 8, 0, STR_PAD_LEFT);
            $p1_edited_b = str_pad(decbin($p1_edited_b), 8, 0, STR_PAD_LEFT);
            $p2_edited_r = str_pad(decbin($p2_edited_r), 8, 0, STR_PAD_LEFT);
            $p2_edited_g = str_pad(decbin($p2_edited_g), 8, 0, STR_PAD_LEFT);
            $p2_edited_b = str_pad(decbin($p2_edited_b), 8, 0, STR_PAD_LEFT);
            $p3_edited_r = str_pad(decbin($p3_edited_r), 8, 0, STR_PAD_LEFT);
            $p3_edited_g = str_pad(decbin($p3_edited_g), 8, 0, STR_PAD_LEFT);
            $p3_edited_b = str_pad(decbin($p3_edited_b), 8, 0, STR_PAD_LEFT);
            $p4_edited_r = str_pad(decbin($p4_edited_r), 8, 0, STR_PAD_LEFT);
            $p4_edited_g = str_pad(decbin($p4_edited_g), 8, 0, STR_PAD_LEFT);
            $p4_edited_b = str_pad(decbin($p4_edited_b), 8, 0, STR_PAD_LEFT);
            
            //  We may be summoning the apocalypse but here goes... 
            $training_data_input =  implode(' ', str_split("$p1_original_r$p1_original_g$p1_original_b$p2_original_r$p2_original_g$p2_original_b$p3_original_r$p3_original_g$p3_original_b$p4_original_r$p4_original_g$p4_original_b"));
            $training_data_output =  implode(' ', str_split("$p1_edited_r$p1_edited_g$p1_edited_b$p2_edited_r$p2_edited_g$p2_edited_b$p3_edited_r$p3_edited_g$p3_edited_b$p4_edited_r$p4_edited_g$p4_edited_b"));

            // YAY!!! No Cthulhu. ^(;;;)^
            
            // Convert 0's to -1's now that the spaces are in place
            // not needed if using FANN_SIGMOID (or other 0-1 ranged activation)
            // needed if using FANN_SIGMOID_SYMMETRIC (or other -1 - 1 ranged activation)
            $training_data_input = str_replace("0","-1", $training_data_input);
            $training_data_output = str_replace("0","-1", $training_data_output);

            
             // Write training data
            fwrite($training_file, PHP_EOL . $training_data_input . PHP_EOL . $training_data_output);
        }
    }
    
    // Remove Image Resources from memory    
    imagedestroy($original);
    imagedestroy($edited);
}


echo "Number of Training Samples: $number_of_training_data" . PHP_EOL;
echo 'All Done!' . PHP_EOL;

