<?php
$champNameSheet = file_get_contents("champions.json");
$champNameSheetDecoded = json_decode($champNameSheet, true);
foreach(champNameSheetDecoded['data'] as $key => $val) {
      if ($val['key'] = "1"){
          echo $key;
      }
}
?>
