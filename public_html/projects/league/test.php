<?php
$champNameSheet = file_get_contents("champions.json");
$champNameSheetDecoded = json_decode($champNameSheet, true);
echo '<pre>' . print_r($champNameSheetDecoded) . '</pre>';
echo print_r($champNameSheetDecoded["data"]["Aatrox"]);

echo print_r($champNameSheetDecoded["data"]["0"]);
?>
