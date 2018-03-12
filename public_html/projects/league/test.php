<?php
$champNameSheet = file_get_contents("champions.json");
$array = json_decode($champNameSheet, true);
echo "<pre>";
echo print_r ($array);
echo "</pre>";
foreach ($array['data'] as $key => $value){
    echo $key;
    echo "<br>";
    echo $value['id'];
    if ($value['key'] = 10){
        echo $value['id'];
    }
};
?>
