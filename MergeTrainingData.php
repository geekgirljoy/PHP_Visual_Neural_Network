<?php
// Create partial data resources
$train_data_1 = fann_read_train_from_file("MyTrainingData_Subset_A.data");
$train_data_2 = fann_read_train_from_file("MyTrainingData_Subset_B.data");

// Merge into new data resource
$train_data = fann_merge_train_data($train_data_1, $train_data_2);

// Remove the partial data resources from memory 
fann_destroy_train($train_data_1);
fann_destroy_train($train_data_2);

// Save the new complete Data file
fann_save_train($train_data, "MyTrainingData_Complete.data");

// Remove the complete data resources from memory 
fann_destroy_train($train_data);
echo 'All Done!' . PHP_EOL;
