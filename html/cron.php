<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$f3 = require('../vendor/bcosca/fatfree/lib/base.php');
$f3->clear('CACHE');

require('../config.php');


$f3->set('CONF_LOL_API_KEY', $CONF_LOL_API_KEY);
$f3->set('CACHE','memcache=localhost');
$f3->set('AUTOLOAD','../Controller/;../Models/');

// initialzing Database connection
$Db = new \DB\SQL('mysql:host='.$CONF_DB_HOST.';port='.$CONF_DB_PORT.';dbname='.$CONF_DB_DB, $CONF_DB_USER, $CONF_DB_PASS);
$f3->set('Db', $Db);

$Log = new \Helper\Log('mysql');
$Log->setupChannelMySQL($Db);

$f3->set('Log',$Log);

// initializing Teamspeak3 connection
$tslogin = sprintf('serverquery://%s:%s@%s/?server_port=%u', $CONF_TS3_USER, $CONF_TS3_PASS, $CONF_TS3_ADDRESS, $CONF_TS3_VSERVERPORT, $CONF_TS3_VSERVERPORT);
$TS3 = \TeamSpeak3\TeamSpeak3::factory($tslogin);
$MyTeamSpeak = new Gamepus\MyTeamSpeak($TS3);

$f3->set('MyTeamSpeak', $MyTeamSpeak);


$f3->route('GET /cron/synchronizePlayers',
    function ($f3){
        
        $DB =           $f3->get('Db');
        $MyTeamSpeak =  $f3->get('MyTeamSpeak');
        $Log =          $f3->get('Log');
        
        // fetch all players of our database
        foreach($DB->exec('SELECT id, ts3id FROM players ORDER by id DESC') as $DbPlayer) {
            $player = new Gamepus\Player($DbPlayer['id'], $DbPlayer['ts3id'], $DB);
            
            foreach ($player->games() as $vendor => $games) {
                $gameName = $games['gameName'];
                $gameId =   $games['gameId'];
                
                switch ($gameName) {
                        
                    case 'Overwatch':
                        $checkDb = $DB->exec('SELECT gameOverwatchId FROM gameOverwatch WHERE playerId = ?', $DbPlayer['id']);
                        if (sizeof($checkDb) == 1) {
                            $game = new Gamepus\Blizzard\Overwatch($DB, $gameId);
                        }
                    break;

                    case 'LeagueOfLegends':
                        $checkDb = $DB->exec('SELECT gameLeagueOfLegendsId FROM gameLeagueOfLegends WHERE playerId = ?', $DbPlayer['id']);
                        if (sizeof($checkDb) == 1) {
                            $game = new Gamepus\Riot\LeagueOfLegends($DB, $gameId, $f3->get('CONF_LOL_API_KEY'));
                        }
                        
                    break;
                }

                $ranks = $game->getPlayerRank($DbPlayer['id']);
                $teamspeakGroupMap = $game->getTeamSpeakGroupMap();

                // $overwatch_ranks war nicht gesetzt, daraufhin kam dieser fehler: 
                // in_array() expects parameter 2 to be array, null given
                try{
                    $MyTeamSpeak->assignServerGroup($DbPlayer['ts3id'], $ranks->newTeamSpeakGroupId, $teamspeakGroupMap);
                }
                catch (Exception $e){
                    $Log->message('Client nicht Online:'. $player->tsid().'\nException: '.$e);
                }
               
            }
            
        }        
    }
);

$f3->run();