<?php
$rankInfoURL = "https://euw1.riotgames.com/lol/league/v3/positions/by-summoner/91248124?api_key=RGAPI-42caac85-2bfc-453a-9be5-2d67848f2ee9";
$rankInfoResult = file_get_contents($rankInfoURL);
$rankInfoDecoded = json_decode($rankInfoResult, true);
echo $rankInfoDecoded;
?>
