<?php
$champNameSheet = file_get_contents("champions.json");
$array = json_decode($champNameSheet, true);
echo "<pre>";
echo print_r ($array);
echo "</pre>";
?>
