<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$f3 = require('../vendor/bcosca/fatfree/lib/base.php');
$f3->clear('CACHE');

$CONF_DB_HOST = 'localhost';
$CONF_DB_USER = 'development';
$CONF_DB_PASS = 'ieXahngae7hia4iSa1oizain';
$CONF_DB_DB = 'development';
$CONF_TEMPLATE = 'gp_fancy';

$CONF_TS3_USER = 'serveradmin';
$CONF_TS3_PASS = 'HWOdvhjd';
$CONF_TS3_ADDRESS = '127.0.0.1:10011';
$CONF_TS3_VSERVERPORT = '9987';

$CONF_LOL_API_KEY = 'RGAPI-21285bd8-a56a-4d14-bd44-512089b296ac';

$f3->set('CONF_LOL_API_KEY', $CONF_LOL_API_KEY);

$f3->set('CACHE','memcache=localhost');

$f3->set('AUTOLOAD','../Controller/;../Models/');

// initialzing Database connection
$Db = new \DB\SQL('mysql:host=localhost;port=3306;dbname=development','development','ieXahngae7hia4iSa1oizain');
$f3->set('Db', $Db);

// initializing Teamspeak3 connection
//require('../vendor/fkubis/teamspeak-php-framework/TeamSpeak3/TeamSpeak3.php');
$tslogin = sprintf('serverquery://%s:%s@%s/?server_port=%u', $CONF_TS3_USER, $CONF_TS3_PASS, $CONF_TS3_ADDRESS, $CONF_TS3_VSERVERPORT, $CONF_TS3_VSERVERPORT);
$TS3 = \TeamSpeak3\TeamSpeak3::factory($tslogin);
$MyTeamSpeak = new Gamepus\MyTeamSpeak($TS3);

$f3->set('MyTeamSpeak', $MyTeamSpeak);


$f3->route('GET /cron/synchronizePlayers',
    function ($f3){
        
        $DB =           $f3->get('Db');
        $MyTeamSpeak =  $f3->get('MyTeamSpeak');
        
        // fetch all players of our database
        foreach($DB->exec('SELECT id, ts3id FROM players ORDER by id DESC') as $DbPlayer) {
            $player = new Gamepus\Player($DbPlayer['id'], $DbPlayer['ts3id'], $DB);
            
            foreach ($player->games() as $vendor => $games) {
                
               foreach ($games as $gameId => $gameAttribute){
                   
                    switch ($gameAttribute['gameName']) {
                        
                        case 'Overwatch':
                            $game = new Gamepus\Blizzard\Overwatch($DB, $gameId);
                        break;
                    
                        case 'LeagueOfLegends':
                            $game = new Gamepus\Riot\LeagueOfLegends($DB, $gameId, $f3->get('CONF_LOL_API_KEY'));
                        break;
                    }
                   
                    $ranks = $game->getPlayerRank($gameAttribute['playerName']);
                    $teamspeakGroupMap = $game->getTeamSpeakGroupMap();
                    
                    // $overwatch_ranks war nicht gesetzt, daraufhin kam dieser fehler: 
                    // in_array() expects parameter 2 to be array, null given
                    $MyTeamSpeak->assignServerGroup($DbPlayer['ts3id'], $ranks->newTeamSpeakGroupId, $teamspeakGroupMap);
               }
               
            }
            
        }        
    }
);

$f3->run();