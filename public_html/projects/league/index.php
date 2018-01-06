<?php
// your summoner name here
$summonerName = "NunoC99";
//your region here
$region = 'euw1';
//get riot api key from text file. if instead, you want to have it in the file, replace line 9 e 10 with:
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

//parse json file with champion name and ID
$champNameSheet = file_get_contents("champions.json");
$champNameSheetDecoded = json_decode($champNameSheet, true);

// fetch data dragon version, used to fetch summoner and champion icons
function serverInfo (){
global $region, $riotapikey, $ddragonVersion;
$versionURL = "https://". $region .".api.riotgames.com/lol/static-data/v3/realms?api_key=" . $riotapikey;
$versionResult = file_get_contents($versionURL);
$versionResult = json_decode($versionResult, true);
$ddragonVersion = $versionResult["dd"];
};

//fetch summoner ID, account ID, icon ID and lvl
function summonerInfo (){
global $region, $summonerName, $riotapikey, $summonerID, $accountID, $summonerIcon, $summonerLvl;
$accountInfoURL = "https://". $region .".api.riotgames.com/lol/summoner/v3/summoners/by-name/". $summonerName ."?api_key=" . $riotapikey;
$accountInfoResult = file_get_contents($accountInfoURL);
$accountInfoResult = json_decode($accountInfoResult, true);
$summonerID = $accountInfoResult["id"];
$accountID = $accountInfoResult["accountId"];
$summonerIcon = $accountInfoResult["profileIconId"];
$summonerLvl = $accountInfoResult["summonerLevel"];
};

//fetch ranked information (tier, rank, lp)
function rankInfo(){
global $region, $riotapikey, $summonerID, $tierSolo, $rankSolo, $lpSolo, $imgtierSolo, $tierFlexTT, $rankFlexTT, $lpFlexTT, $imgtierFlexTT, $tierFlexSR, $rankFlexSR, $lpFlexSR, $imgtierFlexSR;
//parse ranked information from Riot API
$rankInfoURL = "https://". $region .".api.riotgames.com/lol/league/v3/positions/by-summoner/". $summonerID ."?api_key=" . $riotapikey;
$rankInfoResult = file_get_contents($rankInfoURL);
$rankInfoDecoded = json_decode($rankInfoResult, true);
//get tier, rank and lp from the parsed information
for ($queue = 0; $queue <= 2; $queue ++){
    if ($rankInfoDecoded[$queue]['queueType'] === 'RANKED_FLEX_SR') {
      $tierFlexSR = $rankInfoDecoded[$queue]['tier'];
      $rankFlexSR = $rankInfoDecoded[$queue]['rank'];
      $lpFlexSR = $rankInfoDecoded[$queue]['leaguePoints'];
        //treat the $tier string, making it lower-cap and uppercasing the first letter
        $tierFlexSR = strtolower($tierFlexSR);
        $tierFlexSR = ucfirst($tierFlexSR);
        //use $tier to get the image path for the tier icon
        $imgtierFlexSR = "./base-icons/" . $tierFlexSR . ".png";
} elseif ($rankInfoDecoded[$queue]['queueType'] === 'RANKED_SOLO_5x5') {
      $tierSolo = $rankInfoDecoded[$queue]['tier'];
      $rankSolo = $rankInfoDecoded[$queue]['rank'];
      $lpSolo = $rankInfoDecoded[$queue]['leaguePoints'];
        //treat the $tier string, making it lower-cap and uppercasing the first letter
        $tierSolo = strtolower($tierSolo);
        $tierSolo = ucfirst($tierSolo);
        //use $tier to get the image path for the tier icon
        $imgtierSolo = "/base-icons/" . $tierSolo . ".png";
} else {
      $tierFlexTT = $rankInfoDecoded[$queue]['tier'];
      $rankFlexTT = $rankInfoDecoded[$queue]['rank'];
      $lpFlexTT = $rankInfoDecoded[$queue]['leaguePoints'];
        //treat the $tier string, making it lower-cap and uppercasing the first letter
        $tierFlexTT = strtolower($tierFlexTT);
        $tierFlexTT = ucfirst($tierFlexTT);
        //use $tier to get the image path for the tier icon
        $imgtierFlexTT = "/base-icons/" . $tierFlexTT . ".png";
};
};
};

// get top 3 champions in mastery rating
function masteryList(){
global $region, $riotapikey, $summonerID, $champNameURL1, $champNameURL2, $champNameURL3, $champNameSheetDecoded;
//parse mastery list from Riot API
$champMasteryURL = "https://". $region .".api.riotgames.com/lol/champion-mastery/v3/champion-masteries/by-summoner/". $summonerID ."?api_key=" . $riotapikey;
$champMasteryResult = file_get_contents($champMasteryURL);
$champMasteryDecoded = json_decode($champMasteryResult, true);
//get top 3 mastery champions by ID
$champID1 = $champMasteryDecoded[0]['championId'];
$champID2 = $champMasteryDecoded[1]['championId'];
$champID3 = $champMasteryDecoded[2]['championId'];
//take champion ID and turn it into champion name
$champName1 = $champNameSheetDecoded['data']["$champID1"]['name'];
$champName2 = $champNameSheetDecoded['data']["$champID2"]['name'];
$champName3 = $champNameSheetDecoded['data']["$champID3"]['name'];

//treating champion name by removing any possible spaces
$champNameURL1 = preg_replace(
    "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
    "",
    $champName1
);
$champNameURL2 = preg_replace(
    "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
    "",
    $champName2
);
$champNameURL3 = preg_replace(
    "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
    "",
    $champName3
);
};

//get last match id and metadata about it
function lastMatchesInfo(){
global $region, $riotapikey, $accountID, $lastMatchID, $lastMatchQueueReal, $lastChampName, $lastChamp, $lastChampNameURL, $champNameSheetDecoded;
//parse last 20 matches from Riot API
$matchInfoURL = "https://". $region .".api.riotgames.com/lol/match/v3/matchlists/by-account/". $accountID ."/recent?api_key=" . $riotapikey;
$matchInfoResult = file_get_contents($matchInfoURL);
$matchInfoResultDecoded = json_decode($matchInfoResult, true);
//get queue, champion ID and game ID from latest games in the parsed information
$lastMatchQueue = $matchInfoResultDecoded['matches'][0]["queue"];
$lastMatchChampionID = $matchInfoResultDecoded['matches'][0]["champion"];
$lastMatchID = $matchInfoResultDecoded['matches'][0]["gameId"];
//look for queue type: ranked or normal
if (in_array($lastMatchQueue, ["4", "6", "9", "41", "42", "410", "420", "440", "470"]))
	{
	$lastMatchQueueReal = "Ranked";
	}
  else
	{
	$lastMatchQueueReal = "Normal";
	};
$lastChampName = $champNameSheetDecoded['data']["$lastMatchChampionID"]['name'];
$lastChampNameURL = preg_replace(
    "/(\t|\n|\v|\f|\r| |\xC2\x85|\xc2\xa0|\xe1\xa0\x8e|\xe2\x80[\x80-\x8D]|\xe2\x80\xa8|\xe2\x80\xa9|\xe2\x80\xaF|\xe2\x81\x9f|\xe2\x81\xa0|\xe3\x80\x80|\xef\xbb\xbf)+/",
    "",
    $lastChampName
);

};

//fetch last match info about the summoner in use
function lastMatchInfo(){
global $region, $riotapikey, $lastMatchID, $summonerID, $champNameURL1, $champNameURL2, $champNameURL3, $participantDamageDealt, $participantDeaths, $participantKills, $participantAssists, $participantGold, $participantCSMin, $participantWLresult, $participantWLcolor;
    $participantIDorder = $participantID = '';
//parse lastest match information from Riot API
$lastMatchInfoURL = "https://". $region .".api.riotgames.com/lol/match/v3/matches/". $lastMatchID ."?api_key=" . $riotapikey;
$lastMatchInfoResult = file_get_contents($lastMatchInfoURL);
$lastMatchInfoResult = json_decode($lastMatchInfoResult, true);
//search in the 10 participants in the game for your participant ID
for ($i = 0; $i <= 9; $i++) {
    if($lastMatchInfoResult["participantIdentities"][$i]["player"]["summonerId"] == $summonerID ){
        $participantID= $i + 1 ;
        $participantIDorder = $i;
    };
};
//fetch game win, game time, damage dealt, deaths, kills, assists, gold and cs by your player
$participantWL = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["win"];
$participantDamageDealt = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["totalDamageDealtToChampions"];
$participantDeaths = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["deaths"];
$participantKills = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["kills"];
$participantAssists = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["assists"];
$participantGold = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["goldEarned"];
$participantCS = $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["totalMinionsKilled"] + $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["neutralMinionsKilledEnemyJungle"] + $lastMatchInfoResult["participants"][$participantIDorder]["stats"]["neutralMinionsKilledTeamJungle"];
$participantGameTime = $lastMatchInfoResult["gameDuration"];
//transform seconds into average minutes of game
$participantGameTime = round($participantGameTime / 60);
//calculate cs per min
$participantCSMin = round($participantCS / $participantGameTime,1);
    //get color for the chip, red for loss, green for win
if($participantWL == true){
    $participantWLresult = "Win";
    $participantWLcolor = "green";
} else {
    $participantWLresult = "Loss";
    $participantWLcolor = "red";
};
};

summonerInfo();
rankInfo();
masteryList();
lastMatchesInfo();
lastMatchInfo();
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
        <link rel="icon" sizes="any" href="images/favicon.png">
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
            <img id="mainImage" src="http://ddragon.leagueoflegends.com/cdn/7.24.2/img/profileicon/<?php echo $summonerIcon ?>.png">
            <div class="centeredBoxwithImage">
                <div id="nameFlexbox-lol">
                    <h1><?php echo $summonerName ?></h1>
                    <span>Lvl <?php echo $summonerLvl?></span>
                </div>
            </div>
        </div>
        <div class="contentCard">
            <div class="centeredBox">
                <h1>Rank</h1>
                <div class="flexbox">
                    <div>
                        <p>5v5 Solo Queue</p>
                        <p><?php echo $tierSolo ." ". $rankSolo .", ". $lpSolo ; ?></p>
                        <img src=".<?php echo $imgtierSolo?>">
                    </div>
                    <div>
                        <p>5v5 Flex Queue</p>
                        <p><?php echo $tierFlexSR ." ". $rankFlexSR .", ". $lpFlexSR ."lp"; ?></p>
                        <img src=".<?php echo $imgtierFlexSR?>">
                    </div>
                    <div>
                        <p>3v3 Flex Queue</p>
                        <p><?php echo $tierFlexTT ." ". $rankFlexTT .", ". $lpFlexTT .' lp'; ?></p>
                        <img src=".<?php echo $imgtierFlexTT?>">
                    </div>
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