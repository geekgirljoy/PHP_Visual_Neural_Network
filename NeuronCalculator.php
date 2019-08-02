<?php
$color_bit_depth = 8;    // 8 bit color 0:00000000 - 255:11111111
$number_of_channels = 3; // RGB = 3 RGBA = 4
$size_of_pixel_matrix = 2*2; // Pixel matrix width * height

// Number of Input & Output Neurons: 96
echo 'Number of Input & Output Neurons: ' . ($color_bit_depth * $number_of_channels) * $size_of_pixel_matrix;
