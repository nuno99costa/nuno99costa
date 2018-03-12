<?php
$champNameSheet = file_get_contents("champions.json");
$champNameSheetDecoded = json_decode($champNameSheet, true);
foreach(champNameSheetDecoded['data'] as $key => $val) {
      if ($val[id] = 123){
          echo $key;
      }
}
?>
