<?php
$region = 'euw1';

// get riot api key from text file. if instead, you want to have it in the file, replace line 9 e 10 with:
// $riotapikey = 'YOUR API KEY HERE';

$riotkey = "RGAPI-42caac85-2bfc-453a-9be5-2d67848f2ee9";
$riotapikey = $riotkey;
$champNameSheet = file_get_contents("champions.json");
$champNameSheetDecoded = json_decode($champNameSheet, true);
$champID1= 145;
$i = 0;
    $flag = false;
    while ($flag == false){
            if ($champNameSheetDecoded['data'][$i]["key"] == $champID1){
                $flag = true;
                $champName1 = $champNameSheetDecoded['data'][$i];
            }else{
                $i++;
            }
    }
?>
