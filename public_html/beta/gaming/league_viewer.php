<?php
//fetch data from the previous page (summoner name and region)
if(isset($_GET["summonerName"])) $summonerName = $_GET["summonerName"];
if(isset($_GET["region"])) $region = $_GET["region"];

//get riot api key from text file. if instead, you want to have it in the file, replace line 8 e 9 with:
// $riotapikey = 'YOUR API KEY HERE';
$riotapikey = file_get_contents("riot_key.txt");

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
        $imgtierFlexSR = "images/base-icons/" . $tierFlexSR . ".png";
} elseif ($rankInfoDecoded[$queue]['queueType'] === 'RANKED_SOLO_5x5') {
      $tierSolo = $rankInfoDecoded[$queue]['tier'];
      $rankSolo = $rankInfoDecoded[$queue]['rank'];
      $lpSolo = $rankInfoDecoded[$queue]['leaguePoints'];
        //treat the $tier string, making it lower-cap and uppercasing the first letter
        $tierSolo = strtolower($tierSolo);
        $tierSolo = ucfirst($tierSolo);
        //use $tier to get the image path for the tier icon
        $imgtierSolo = "images/base-icons/" . $tierSolo . ".png";
} else {
      $tierFlexTT = $rankInfoDecoded[$queue]['tier'];
      $rankFlexTT = $rankInfoDecoded[$queue]['rank'];
      $lpFlexTT = $rankInfoDecoded[$queue]['leaguePoints'];
        //treat the $tier string, making it lower-cap and uppercasing the first letter
        $tierFlexTT = strtolower($tierFlexTT);
        $tierFlexTT = ucfirst($tierFlexTT);
        //use $tier to get the image path for the tier icon
        $imgtierFlexTT = "images/base-icons/" . $tierFlexTT . ".png";
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

serverInfo();
summonerInfo();
rankInfo();
masteryList();
lastMatchesInfo();
lastMatchInfo();
?>
    <!doctype html>

    <html>

    <head>
        <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({
                google_ad_client: "ca-pub-6711196968099723",
                enable_page_level_ads: true
            });

        </script>
        <!-- Global Site Tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-106754475-1"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments)
            };
            gtag('js', new Date());

            gtag('config', 'UA-106754475-1');

        </script>

        <title>Nuno Costa</title>
        <meta charset="UTF-8">
        <meta name="keywords" content="curriculum,resume,nuno,costa,pacos de ferreira,nuno costa,nuno99costa,materialize">
        <meta name="description" content="Resume made by Nuno Costa">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="css/materialize.min.css" media="screen,projection" />
        <link rel="icon" sizes="192x192" href="images/favicon.png">
        <style>
            body {
                background-image: url(images/background.jpg);
                background-repeat: no-repeat;
                background-attachment: fixed;
            }

        </style>
        <meta name="theme-color" content="#3f51b5">
    </head>

    <body>
        <a id="home"></a>
        <div class="navbar-fixed">
            <nav>
                <div class="nav-wrapper indigo">
                    <div class="row">
                        <div class="col offset-s1">
                            <span class="brand-logo left">Nuno Costa</span>
                        </div>
                        <ul class="right hide-on-med-and-down">
                            <li><a href="index.html"><i class="material-icons left">fingerprint</i>About me</a></li>
                            <li><a href="gaming.php"><i class="material-icons left">videogame_asset</i>Gaming</a></li>
                            <li><a href="music.php"><i class="material-icons left">music_note</i>Music</a></li>
                            <li><a href="contact.php"><i class="material-icons left">mail</i>Contact Me</a></li>
                        </ul>
                        <ul id="dropdown1" class="dropdown-content">
                            <li><a href="index.html">About</a></li>
                            <li><a href="gaming.php">Gaming</a></li>
                            <li><a href="music.php">Music</a></li>
                            <li><a href="contact.php">Contact</a></li>
                        </ul>
                        <ul class="right hide-on-large-only">
                            <li><a class="dropdown-button" href="#!" data-activates="dropdown1">More<i class="material-icons right">arrow_drop_down</i></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
        <br>
        <div class="row">
            <div class="col s6 offset-s3 l2 offset-l5 valign-wrapper center">
                <a class="waves-effect waves-light btn" href="league_form.html"><i class="material-icons left">refresh</i>Again?</a>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l4 offset-l4">
                <div class="card-panel grey lighten-5 z-depth-1 hoverable">
                    <div class="chip right">
                        <?php echo "Lvl ". $summonerLvl ?>
                    </div>
                    <div class="row">
                        <div class="col l5 s6 valign-wrapper">
                            <img src="http://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/profileicon/<?php echo $summonerIcon ?>.png" class="responsive-img center-align hoverable">
                        </div>
                        <div class="col l7 s6 valign-wrapper">
                            <h3 class="flow-text center-align">
                                <?php echo $summonerName ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l4">
                <div class="card-panel grey lighten-5 z-depth-1 hoverable">
                    <h5 class="center-align">Ranked Flex 5v5</h5>
                    <div class="row">
                        <div class="col l5 s6 valign-wrapper">
                            <img src="<?php echo $imgtierFlexSR?>" class="responsive-img center-align">
                        </div>
                        <div class="col l7 s6">
                            <br>
                            <p class="flow-text">
                                <?php echo $tierFlexSR ." ". $rankFlexSR .", ". $lpFlexSR ."lp"; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel grey lighten-5 z-depth-1 hoverable">
                    <h5 class="center-align">Ranked Solo</h5>
                    <div class="row">
                        <div class="col l5 s6 valign-wrapper">
                            <img src="<?php echo $imgtierSolo?>" class="responsive-img center-align">
                        </div>
                        <div class="col l7 s6">
                            <br>
                            <p class="flow-text">
                                <?php echo $tierSolo ." ". $rankSolo .", ". $lpSolo .' lp'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col s12 l4">
                <div class="card-panel grey lighten-5 z-depth-1 hoverable">
                    <h5 class="center-align">Ranked Flex 3v3</h5>
                    <div class="row">
                        <div class="col l5 s6 valign-wrapper">
                            <img src="<?php echo $imgtierFlexTT ?>" class="responsive-img center-align">
                        </div>
                        <div class="col l7 s6">
                            <br>
                            <p class="flow-text">
                                <?php echo $tierFlexTT ." ". $rankFlexTT .", ". $lpFlexTT .' lp'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col l6 s12 offset-l3">
                <div class="card-panel grey lighten-5 z-depth-1 hoverable">
                    <div class="row">
                        <h5 class="center-align">My Top 3 Mastery Champions</h5>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col s3 valign-wrapper">
                            <div class="center-align">
                            <img class="responsive-img circle hoverable" src="http://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/champion/<?php echo $champNameURL2 ?>.png">
                            </div>
                        </div>
                        <div class="col s3 offset-s1 valign-wrapper">
                            <div class="center-align">
                            <img class="responsive-img circle hoverable" src="http://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/champion/<?php echo $champNameURL1 ?>.png">
                            </div>
                        </div>
                        <div class="col s3 offset-s1 valign-wrapper">
                            <div class="center-align">
                            <img class="responsive-img circle hoverable" src="http://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/champion/<?php echo $champNameURL3 ?>.png">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col s12 l4 offset-l4">
                <div class="card-panel grey lighten-5 z-depth-1 hoverable">
                    <div class="row">
                        <div class="col s8 offset-s2">
                            <h5 class="center-align">My Last Game</h5>
                        </div>
                        <div class="col s2 valign-wrapper">
                            <div class="chip right <?php echo $participantWLcolor ?>">
                                <?php echo $participantWLresult ?>
                            </div>
                        </div>
                    </div>
                    <div class="center-align">
                        <img class="responsive-img circle" src="http://ddragon.leagueoflegends.com/cdn/<?php echo $ddragonVersion ?>/img/champion/<?php echo $lastChampNameURL ?>.png">
                    </div>
                    <p class="center-align flow-text">
                        <?php echo $lastMatchQueueReal ." with ". $lastChampName?>
                    </p>
                    <p class="flow-text center-align">
                        <?php echo $participantKills ."/". $participantDeaths ."/". $participantAssists ?>
                    </p>
                    <p class="flow-text center-align">
                        <?php echo $participantCSMin ." CS/min" ?>
                    </p>
                    <p class="flow-text center-align">
                        <?php echo $participantGold ." gold earned" ?>
                    </p>
                    <p class="flow-text center-align">
                        <?php echo $participantDamageDealt ." damage dealt" ?>
                    </p>
                </div>
            </div>
        </div>
        <a id="footer"></a>
        <footer class="page-footer blue">
            <div class="container">
                <div class="row">
                    <div class="col l6 s12 hide-on-med-and-down">
                        <h5 class="white-text">Libraries Used</h5>
                        <p class="grey-text text-lighten-4">MaterializeCSS</p>
                    </div>
                    <div class="col l4 offset-l2 s12">
                        <h5 class="white-text">Links</h5>
                        <ul>
                            <li><a class="grey-text text-lighten-3" href="https://twitter.com/n99costa">Twitter</a></li>
                            <li><a class="grey-text text-lighten-3" href="https://www.facebook.com/NunoCosta99">Facebook</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-copyright">
                <div class="container">
                    © 2017 Nuno Costa
                </div>
            </div>
        </footer>
        <!--  Scripts-->
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="js/materialize.min.js"></script>
        <script src="js/init.js"></script>
    </body>

    </html>
