<?php
$num_input = 96;
$num_output = 96;
$layers = array($num_input, 192, 160, 96, $num_output);

$ann = fann_create_standard_array (count($layers), $layers);

if ($ann) {
    fann_set_activation_function_hidden($ann, FANN_SIGMOID_SYMMETRIC);
    fann_set_activation_function_output($ann, FANN_SIGMOID);

    if (fann_save($ann, dirname(__FILE__) . "/random.net")){
        echo 'random.net' . PHP_EOL;
    }
    
    fann_destroy($ann);
}
