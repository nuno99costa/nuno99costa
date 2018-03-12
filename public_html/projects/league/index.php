<?php

// Converts a number into a short version, eg: 1000 -> 1k
// Based on: http://stackoverflow.com/a/4371114
function number_format_short( $n, $precision = 1 ) {
	if ($n < 900) {
		// 0 - 900
		$n_format = number_format($n, $precision);
		$suffix = '';
	} else if ($n < 900000) {
		// 0.9k-850k
		$n_format = number_format($n / 1000, $precision);
		$suffix = 'k';
	} else if ($n < 900000000) {
		// 0.9m-850m
		$n_format = number_format($n / 1000000, $precision);
		$suffix = 'm';
	} else if ($n < 900000000000) {
		// 0.9b-850b
		$n_format = number_format($n / 1000000000, $precision);
		$suffix = 'b';
	} else {
		// 0.9t+
		$n_format = number_format($n / 1000000000000, $precision);
		$suffix = 't';
	}
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
	if ( $precision > 0 ) {
		$dotzero = '.' . str_repeat( '0', $precision );
		$n_format = str_replace( $dotzero, '', $n_format );
	}
	return $n_format . $suffix;
}

// your summoner name here

$summonerName = "NunoC99";

// your region here

$region = 'euw1';

// get riot api key from text file. if instead, you want to have it in the file, replace line 9 e 10 with:
// $riotapikey = 'YOUR API KEY HERE';

$riotkey = "RGAPI-42caac85-2bfc-453a-9be5-2d67848f2ee9";
$riotapikey = $riotkey;

// set variables that are used in the functions and in the html

$ddragonVersion = '';
$summonerID = '';
$summonerIcon = '';
$summonerLvl = '';
$accountID = '';
$tierFlexSR = '';
$rankFlexSR = '';
$lpFlexSR = '';
$imgtierFlexSR = '';
$tierSolo = '';
$rankSolo = '';
$lpSolo = '';
$imgtierSolo = '';
$tierFlexTT = '';
$rankFlexTT = '';
$lpFlexTT = '';
$imgtierFlexTT = '';
$champNameURL1 = '';
$champNameURL2 = '';
$champNameURL3 = '';
$lastMatchQueueReal = '';
$lastChampNameURL = '';
$lastChampName = '';
$lastMatchID = '';
$participantWLresult = '';
$participantWLcolor = '';
$participantDamageDealt = '';
$participantDeaths = '';
$participantKills = '';
$participantAssists = '';
$participantGold = '';
$participantCSMin = '';
$participantKeystone = '';
$participantSecondaryPath = '';

// parse json file with champion name and ID

$champNameSheet = file_get_contents("champions.json");
$champNameSheetDecoded = json_decode($champNameSheet, true);

// fetch data dragon version, used to fetch summoner and champion icons

function serverInfo()
{
	global $region, $riotapikey, $ddragonVersion;
	$versionURL = "https://" . $region . ".api.riotgames.com/lol/static-data/v3/realms?api_key=" . $riotapikey;
	$versionResult = file_get_contents($versionURL);
	$versionResult = json_decode($versionResult, true);
	$ddragonVersion = $versionResult["dd"];
};

// fetch summoner ID, account ID, icon ID and lvl

function summonerInfo()
{
	global $region, $summonerName, $riotapikey, $summonerID, $accountID, $summonerIcon, $summonerLvl;
	$accountInfoURL = "https://" . $region . ".api.riotgames.com/lol/summoner/v3/summoners/by-name/" . $summonerName . "?api_key=" . $riotapikey;
	$accountInfoResult = file_get_contents($accountInfoURL);
	$accountInfoResult = json_decode($accountInfoResult, true);
	$summonerID = $accountInfoResult["id"];
	$accountID = $accountInfoResult["accountId"];
	$summonerIcon = $accountInfoResult["profileIconId"];
	$summonerLvl = $accountInfoResult["summonerLevel"];
};

// fetch ranked information (tier, rank, lp)

function rankInfo()
{
	global $region, $riotapikey, $summonerID, $tierSolo, $rankSolo, $lpSolo, $imgtierSolo, $tierFlexTT, $rankFlexTT, $lpFlexTT, $imgtierFlexTT, $tierFlexSR, $rankFlexSR, $lpFlexSR, $imgtierFlexSR;

	// parse ranked information from Riot API

	$rankInfoURL = "https://" . $region . ".api.riotgames.com/lol/league/v3/positions/by-summoner/" . $summonerID . "?api_key=" . $riotapikey;
	$rankInfoResult = file_get_contents($rankInfoURL);
	$rankInfoDecoded = json_decode($rankInfoResult, true);

	// get tier, rank and lp from the parsed information

	$n = count($rankInfoDecoded);
	if ($n === 0) {
		$tierFlexSR = "Unranked";
		$rankFlexSR = "1";
		$lpFlexSR = "0";
		$imgtierFlexSR = "/base-icons/Provisional.png";
		$tierFlexTT = "Unranked";
		$rankFlexTT = "1";
		$lpFlexTT = "0";
		$imgtierFlexTT = "/base-icons/Provisional.png";
		$tierSolo = "Unranked";
		$rankSolo = "1";
		$lpSolo = "0";
		$imgtierSolo = "/base-icons/Provisional.png";
	}
	elseif ($n === 1) {
		if ($rankInfoDecoded[0]['queueType'] == 'RANKED_FLEX_SR') {
			$tierFlexSR = $rankInfoDecoded[0]['tier'];
			$rankFlexSR = $rankInfoDecoded[0]['rank'];
			$lpFlexSR = $rankProvisionalInfoDecoded[0]['leaguePoints'];

			// treat the $tier string, making it lower-cap and uppercasing the first letter

			$tierFlexSR = strtolower($tierFlexSR);
			$tierFlexSR = ucfirst($tierFlexSR);

			// use $tier to get the image path for the tier icon

			$imgtierFlexSR = "/base-icons/" . $tierFlexSR . ".png";
			$tierFlexTT = "Unranked";
			$rankFlexTT = "1";
			$lpFlexTT = "0";
			$imgtierFlexTT = "/base-icons/Provisional.png";
			$tierSolo = "Unranked";
			$rankSolo = "1";
			$lpSolo = "0";
			$imgtierSolo = "/base-icons/Provisional.png";
		}
		elseif ($rankInfoDecoded[0]['queueType'] == 'RANKED_SOLO_5x5') {
			$tierSolo = $rankInfoDecoded[0]['tier'];
			$rankSolo = $rankInfoDecoded[0]['rank'];
			$lpSolo = $rankInfoDecoded[0]['leaguePoints'];

			// treat the $tier string, making it lower-cap and uppercasing the first letter

			$tierSolo = strtolower($tierSolo);
			$tierSolo = ucfirst($tierSolo);

			// use $tier to get the image path for the tier icon

			$imgtierSolo = "/base-icons/" . $tierSolo . ".png";
			$tierFlexTT = "Unranked";
			$rankFlexTT = "1";
			$lpFlexTT = "0";
			$imgtierFlexTT = "/base-icons/Provisional.png";
			$tierFlexSR = "Unranked";
			$rankFlexSR = "1";
			$lpFlexSR = "0";
			$imgtierFlexSR = "/base-icons/Provisional.png";
		}
		else {
			$tierFlexTT = $rankInfoDecoded[0]['tier'];
			$rankFlexTT = $rankInfoDecoded[0]['rank'];
			$lpFlexTT = $rankInfoDecoded[0]['leaguePoints'];

			// treat the $tier string, making it lower-cap and uppercasing the first letter

			$tierFlexTT = strtolower($tierFlexTT);
			$tierFlexTT = ucfirst($tierFlexTT);

			// use $tier to get the image path for the tier icon

			$imgtierFlexTT = "/base-icons/" . $tierFlexTT . ".png";
			$tierFlexSR = "Unranked";
			$rankFlexSR = "1";
			$lpFlexSR = "0";
			$imgtierFlexSR = "/base-icons/Provisional.png";
			$tierSolo = "Unranked";
			$rankSolo = "1";
			$lpSolo = "0";
			$imgtierSolo = "/base-icons/Provisional.png";
		};
	}
	else {
		$flag = 0;
		for ($queue = 0; $queue <= $n; $queue++) {
			if ($rankInfoDecoded[$queue]['queueType'] === 'RANKED_FLEX_SR') {
				$flag = $flag + 1;
				$tierFlexSR = $rankInfoDecoded[$queue]['tier'];
				$rankFlexSR = $rankInfoDecoded[$queue]['rank'];
				$lpFlexSR = $rankProvisionalInfoDecoded[$queue]['leaguePoints'];

				// treat the $tier string, making it lower-cap and uppercasing the first letter

				$tierFlexSR = strtolower($tierFlexSR);
				$tierFlexSR = ucfirst($tierFlexSR);

				// use $tier to get the image path for the tier icon

				$imgtierFlexSR = "/base-icons/" . $tierFlexSR . ".png";
			}
			elseif ($rankInfoDecoded[$queue]['queueType'] === 'RANKED_SOLO_5x5') {
				$flag = $flag + 2;
				$tierSolo = $rankInfoDecoded[$queue]['tier'];
				$rankSolo = $rankInfoDecoded[$queue]['rank'];
				$lpSolo = $rankInfoDecoded[$queue]['leaguePoints'];

				// treat the $tier string, making it lower-cap and uppercasing the first letter

				$tierSolo = strtolower($tierSolo);
				$tierSolo = ucfirst($tierSolo);

				// use $tier to get the image path for the tier icon

				$imgtierSolo = "/base-icons/" . $tierSolo . ".png";
			}
			else {
				$flag = $flag + 3;
				$tierFlexTT = $rankInfoDecoded[$queue]['tier'];
				$rankFlexTT = $rankInfoDecoded[$queue]['rank'];
				$lpFlexTT = $rankInfoDecoded[$queue]['leaguePoints'];

				// treat the $tier string, making it lower-cap and uppercasing the first letter

				$tierFlexTT = strtolower($tierFlexTT);
				$tierFlexTT = ucfirst($tierFlexTT);

				// use $tier to get the image path for the tier icon

				$imgtierFlexTT = "/base-icons/" . $tierFlexTT . ".png";
			};
		};
		if ($flag = 3) {
			$tierFlexTT = "Unranked";
			$rankFlexTT = "1";
			$lpFlexTT = "0";
			$imgtierFlexTT = "/base-icons/Provisional.png";
		}
		elseif ($flag = 4) {
			$tierSolo = "Unranked";
			$rankSolo = "1";
			$lpSolo = "0";
			$imgtierSolo = "/base-icons/Provisional.png";
		}
		elseif ($flag = 5) {
			$tierFlexSR = "Unranked";
			$rankFlexSR = "1";
			$lpFlexSR = "0";
			$imgtierFlexSR = "/base-icons/Provisional.png";
		};
	};
};

// get top 3 champions in mastery rating

function masteryList()
{
	global $region, $riotapikey, $summonerID, $champNameURL1, $champNameURL2, $champNameURL3, $champNameSheetDecoded;

	// parse mastery list from Riot API

	$champMasteryURL = "https://" . $region . ".api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/" . $summonerID . "?api_key=" . $riotapikey;
	$champMasteryResult = file_get_contents($champMasteryURL);
	$champMasteryDecoded = json_decode($champMasteryResult, true);

	// get top 3 mastery champions by ID

	$champID1 = $champMasteryDecoded[0]['championId'];
	$champID2 = $champMasteryDecoded[1]['championId'];
	$champID3 = $champMasteryDecoded[2]['championId'];

	// take champion ID and turn it into champion name

	$champName1 = $champNameSheetDecoded['data']["$champID1"]['name'];
	$champName2 = $champNameSheetDecoded['data']["$champID2"]['name'];
	$champName3 = $champNameSheetDecoded['data']["$champID3"]['name'];

	// treating champion name by removing any possible spaces

	$champNameURL1 = preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/", "", $champName1);
	$champNameURL2 = preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/", "", $champName2);
	$champNameURL3 = preg_replace("/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/", "", $champName3);
};

// get last match id and metadata about it

function lastMatchesInfo()
{
	global $region, $riotapikey, $accountID, $lastMatchID, $lastMatchQueueReal, $lastChampName, $lastChamp, $lastChampNameURL, $champNameSheetDecoded;

	// parse last 20 matches from Riot API

	$matchInfoURL = "https://" . $region . ".api.riotgames.com/lol/match/v3/matchlists/by-account/" . $accountID . "/recent?api_key=" . $riotapikey;
	$matchInfoResult = file_get_contents($matchInfoURL);
	$matchInfoResultDecoded = json_decode($matchInfoResult, true);

	// get queue, champion ID and game ID from latest games in the parsed information

	$lastMatchQueue = $matchInfoResultDecoded['matches'][0]["queue"];
	$lastMatchChampionID = $matchInfoResultDecoded['matches'][0]["champion"];
	$lastMatchID = $matchInfoResultDecoded['matches'][0]["gameId"];

	// look for queue type: ranked or normal

	if (in_array($lastMatchQueue, ["4", "6", "9", "41", "42", "410", "420", "440", "470"])) {
		$lastMatchQueueReal = "Ranked";
	}
	else {
		$lastMatchQueueReal = "Normal";
	};
    $i = 0;
    while (!$flag){
        if ($champNameSheetDecoded['data'][$i]["id"] = $lastMatchChampionID){
            $flag = true;
            $lastChampName = $champNameSheetDecoded['data']["$lastMatchChampionID"]['name'];
            $lastChampNameURL = $champNameSheetDecoded['data'][$i];
        }else{
            $i++;
        }
    }
	$lastChampNameURL = preg_replace('/[^A-Za-z0-9\-]/', '', $lastChampNameURL);
    $lastChampNameURL = str_replace(' ', '', $lastChampNameURL);
};

// fetch last match info about the summoner in use

function lastMatchInfo()
{
	global $region, $riotapikey, $lastMatchID, $summonerID, $champNameURL1, $champNameURL2, $champNameURL3, $participantDamageDealt, $participantDeaths, $participantKills, $participantAssists, $participantGold, $participantCSMin, $participantWLresult, $participantWLcolor, $participantKeystone, $participantSecondaryPath, $gametime;
	$participantIDorder = $participantID = '';

	// parse lastest match information from Riot API

	$lastMatchInfoURL = "https://" . $region . ".api.riotgames.com/lol/match/v3/matches/" . $lastMatchID . "?api_key=" . $riotapikey;
	$lastMatchInfoResult = file_get_contents($lastMatchInfoURL);
	$lastMatchInfoResult = json_decode($lastMatchInfoResult, true);

	// search in the 10 participants in the game for your participant ID

	for ($i = 0; $i <= 9; $i++) {
		if ($lastMatchInfoResult["participantIdentities"][$i]["player"]["summonerId"] == $summonerID) {
			$participantID = $i + 1;
			$participantIDorder = $i;
		};
	};

	// fetch game win, game time, damage dealt, deaths, kills, assists, gold and cs by your player

	$participantWL = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["win"];
	$participantDamageDealt = number_format_short($lastMatchInfoResult["participants"][$participantIDorder]["stats"]["totalDamageDealtToChampions"]);
	$participantDeaths = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["deaths"];
	$participantKills = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["kills"];
	$participantAssists = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["assists"];
	$participantGold = number_format_short($lastMatchInfoResult["participants"][$participantIDorder]["stats"]["goldEarned"]);
	$participantCS = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["totalMinionsKilled"] + $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["neutralMinionsKilledEnemyJungle"] + $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["neutralMinionsKilledTeamJungle"];
	$participantGameTime = $lastMatchInfoResult["gameDuration"];

	// transform seconds into average minutes of game

	$participantGameTime = round($participantGameTime / 60);
	$participantKeystone = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["perk0"];
	$participantSecondaryPath = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["perkSubStyle"];

	// calculate cs per min
	$participantCSMin = round($participantCS / $participantGameTime, 1);

    // calculate game time in minutes

	$participantGameTime = $lastMatchInfoResult["gameDuration"];
    $participantGameTimeMin = intdiv($participantGameTime, 60);
    $participantGameTimeSec = $participantGameTime % 60;
    $gametime = $participantGameTimeMin .": ". $participantGameTimeSec;

	// get color for the chip, red for loss, green for win

	if ($participantWL == true) {
		$participantWLresult = "Win";
		$participantWLcolor = "green";
	}
	else {
		$participantWLresult = "Loss";
		$participantWLcolor = "red";
	};
};
summonerInfo();
rankInfo();
masteryList();
lastMatchesInfo();
lastMatchInfo();

    $ddragonVersion = "8.5.2";
?>
<!doctype html>
    <html lang="pt">

    <head>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-106754475-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-106754475-1');

        </script>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <meta name="google-site-verification" content="jEvvPKp7Ly44Bvb26KLoQq4j707GKmibZY2vRxPB6uI" />
        <title>Nuno Costa - Web Developer</title>
        <meta name="keywords" content="curriculum,resume,nuno,costa,pacos de ferreira,nuno costa,nuno99costa,front-end,developer">
        <meta name="description" content="Nuno Costa Portfolio">
        <link rel="icon" sizes="any" href="../../images/favicon.png">
        <meta name="theme-color" content="#212121">
        <meta name="author" content="Nuno Costa">
    </head>

    <body>
        <header>
            <nav>
                <ul>
                    <li><img onclick="window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });" src="../../images/favicon.png"></li>
                    <li>
                        <div>Nuno Costa</div>
                    </li>
                </ul>
            </nav>
        </header>
        <div id="card1">
            <img id="mainImage" src="http://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/profileicon/<?php echo $summonerIcon ?>.png">
            <div class="centeredBoxwithImage">
                <div id="leagueIDfb">
                    <h1><?php echo $summonerName ?></h1>
                    <span>Lvl <?php echo $summonerLvl?></span>
                </div>
            </div>
        </div>
        <div class="contentCard">
            <div class="centeredBox">
                <h2>Rank</h2>
                <div class="flexbox">
                    <div class="rankfb" id="flexsr">
                        <h3>5v5 Flex Queue</h3>
                        <p><?php echo $tierFlexSR ." ". $rankFlexSR .", ". $lpFlexSR ." lp"; ?></p>
                        <img src=".<?php echo $imgtierFlexSR?>">
                    </div>
                    <div class="rankfb" id="solosr">
                        <h3>5v5 Solo Queue</h3>
                        <p><?php echo $tierSolo ." ". $rankSolo .", ". $lpSolo ." lp"; ?>
                        </p>
                        <img src=".<?php echo $imgtierSolo?>">
                    </div>
                    <div class="rankfb" id="flextt">
                        <h3>3v3 Flex Queue</h3>
                        <p>
                            <?php echo $tierFlexTT ." ". $rankFlexTT .", ". $lpFlexTT .' lp'; ?>
                        </p>
                        <img src=".<?php echo $imgtierFlexTT?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="contentCard">
            <div class="centeredBox">
                <h2>Champion Mastery</h2>
                <div class="mastery">
                    <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion . "/img/champion/" . $champNameURL2 ?>.png">
                    <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion . "/img/champion/" . $champNameURL1 ?>.png">
                    <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion . "/img/champion/" . $champNameURL3 ?>.png">
                </div>
            </div>
        </div>
        <div class="contentCard">
            <div class="centeredBox">
                <div id="leagueIDfb">
                    <h2>My Last Game (<?php echo $lastMatchQueueReal ?>)</h2>
                    <span style="background-color: <?php echo $participantWLcolor ?>"><?php echo $participantWLresult ?></span>
                </div>
                <div class="lastgame">
                    <img src="https://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/champion/<?php echo $lastChampNameURL ?>.png">
                    <img src="./perk/<?php echo $participantKeystone ?>.png">
                    <img src="./perkStyle/<?php echo $participantSecondaryPath ?>.png" id="secondary">
                </div>
                    <div class="flexbox">
                        <p>
                            <?php echo $participantKills ."/". $participantDeaths ."/". $participantAssists ?>
                        </p>
                        <p>
                            <?php echo $participantCSMin ." CS/min" ?>
                        </p>
                    </div>
                    <div class="flexbox">
                        <p>
                            <?php echo $participantDamageDealt ." damage dealt" ?>
                        </p>
                        <p>
                            <?php echo $participantGold ." gold earned" ?>
                        </p>
                    </div>
                </div>
        </div>
        <footer>
            <p>Made by Nuno Costa, 2017</p>
        </footer>
        <script type="text/javascript">
            /* style CSS File */
            var stylecss = document.createElement('link');
            stylecss.rel = 'stylesheet';
            stylecss.href = '../../css/style.css';
            stylecss.type = 'text/css';
            var godefer = document.getElementsByTagName('link')[0];
            godefer.parentNode.insertBefore(stylecss, godefer);

            /* font CSS File */
            var fontsCSS = document.createElement('link');
            fontsCSS.rel = 'stylesheet';
            fontsCSS.href = 'https://fonts.googleapis.com/css?family=Roboto|Rubik:700';
            var godefer2 = document.getElementsByTagName('link')[0];
            godefer2.parentNode.insertBefore(fontsCSS, godefer2);

            /* icon CSS File */
            var iconsCSS = document.createElement('link');
            iconsCSS.rel = 'stylesheet';
            iconsCSS.href = 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css';
            var godefer3 = document.getElementsByTagName('link')[0];
            godefer3.parentNode.insertBefore(iconsCSS, godefer3);

        </script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script async language="JavaScript" type="text/javascript" src="../../js/index.js"></script>
    </body>

    </html>
