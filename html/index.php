<?php
$f3 = require('../vendor/bcosca/fatfree/lib/base.php');
$f3->clear('CACHE');

require('../config.php');



$f3->set('CONF_LOL_API_KEY', $CONF_LOL_API_KEY);
$f3->set('CACHE','memcache=localhost');
//$f3->set('CACHE',true);
new Session();

$f3->set('AUTOLOAD','../Controller/;../Models/');



// initialzing Database connection
$Db = new \DB\SQL('mysql:host='.$CONF_DB_HOST.';port='.$CONF_DB_PORT.';dbname='.$CONF_DB_DB, $CONF_DB_USER, $CONF_DB_PASS);
$f3->set('Db', $Db);


$Log = new \Helper\Log('mysql');
$Log->setupChannelMySQL($Db);
$f3->set('Log',$Log);

// initializing Teamspeak3 connection
//require('../vendor/fkubis/teamspeak-php-framework/TeamSpeak3/TeamSpeak3.php');
/*
$tslogin = sprintf('serverquery://%s:%s@%s/?server_port=%u', $CONF_TS3_USER, $CONF_TS3_PASS, $CONF_TS3_ADDRESS, $CONF_TS3_VSERVERPORT, $CONF_TS3_VSERVERPORT);
$TS3 = \TeamSpeak3\TeamSpeak3::factory($tslogin);
$MyTeamSpeak = new Gamepus\MyTeamSpeak($TS3);
$f3->set('MyTeamSpeak', $MyTeamSpeak);
*/



// check if have a valid session
if ( $f3->get('SESSION.userId') > 0 ) {
    
    $f3->route('POST /PlayerSave',
        function ($f3){
            $Db = $f3->get('Db');
            
            // save ts3id into session
            $f3->set('SESSION.ts3id', $f3->get('REQUEST.ts3id') );
            
            $DbTs3Id = $Db->exec('SELECT ts3id FROM players WHERE userId <> ? AND ts3id = ?', array( $f3->get('SESSION.userId'), $f3->get('SESSION.ts3id') ) );
            
            if (sizeof($DbTs3Id) == 0) {
                $f3->set('SESSION.profilesaveok', 'true');
                
                // check if its create a new or update an existing entry for the user
                $DbUserExists = $Db->exec('SELECT id FROM players WHERE userId = ?', $f3->get('SESSION.userId'));
                if ( sizeof($DbUserExists) == 0) {
                    $Db->exec('INSERT INTO players (userId, ts3id) VALUES (?, ?)', array( $f3->get('SESSION.userId'), $f3->get('SESSION.ts3id') ) );
                    
                    $DbPlayer = $Db->exec('SELECT id FROM players WHERE userId = ?', $f3->get('SESSION.userId') );
                    $f3->set('SESSION.playerId', $DbPlayer[0]['id']);
                
                }
                else {
                    $Db->exec('UPDATE players SET userId = ?, ts3id = ? WHERE userId = ?', array( $f3->get('SESSION.userId'), $f3->get('SESSION.ts3id'), $f3->get('SESSION.userId') ) );
                }
            }
            
            else {
                $f3->set('SESSION.profilesaveerror', 'true');
            }
            $f3->reroute('/Profile');
        }  
    );
    
    $f3->route('GET /Profile',
        function ($f3){         
            $f3->set('navProfile', 'active');
            $f3->set('page_display', '../templates/gp_fancy/page.profile.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
            $f3->clear('SESSION.profilesaveok');
            $f3->clear('SESSION.profilesaveerror');
        }
    );
    
    $f3->route('GET /Teams',
        function ($f3){
            $f3->set('navTeams', 'active');
            $f3->set('page_display', '../templates/gp_fancy/page.teams.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
        }
    );
    
    $f3->route('GET /Dashboard',
        function ($f3){
            $f3->set('navDashboard', 'active');
            $f3->set('page_display', '../templates/gp_fancy/page.dashboard.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
        }
    );
    
     $f3->route('POST /Games/LoL',
        function (\Base $f3){
            $Db = $f3->get('Db');
            
            // if its not empty we want to create or update
            if ( strlen($f3->get('REQUEST.summonerName')) > 0){
                $DbPlayerGames = $Db->exec('SELECT gameLeagueOfLegendsId FROM gameLeagueOfLegends WHERE playerId = ?', $f3->get('SESSION.playerId'));
                
                // entry exists, we need to update
                if ( sizeof($DbPlayerGames) > 0 ){
                    $Db->exec('UPDATE gameLeagueOfLegends SET summonerName = ?, server = ? WHERE playerId = ?', array($f3->get('REQUEST.summonerName'), $f3->get('REQUEST.server'), $f3->get('SESSION.playerId')));
                }
                
                else {
                    $Db->exec('INSERT INTO gameLeagueOfLegends (summonerName, server, playerId) VALUES (?, ?, ?) ', array($f3->get('REQUEST.summonerName'), $f3->get('REQUEST.server'), $f3->get('SESSION.playerId')));
                }
            }
            
            // go for delete
            else {
                $Db->exec('DELETE FROM gameLeagueOfLegends WHERE playerId = ?', $f3->get('SESSION.playerId'));
            }
            $f3->set('SESSION.saveLoL', 'true');
            $f3->reroute('/Games');
        }
    );
    
    
    $f3->route('POST /Games/Overwatch',
        function (\Base $f3){
            $Db = $f3->get('Db');
            
            // if its not empty we want to create or update
            if ( strlen($f3->get('REQUEST.battletag')) > 0){
                $DbPlayerGames = $Db->exec('SELECT gameOverwatchId FROM gameOverwatch WHERE playerId = ?', $f3->get('SESSION.playerId'));
                
                // entry exists, we need to update
                if ( sizeof($DbPlayerGames) > 0 ){
                    $Db->exec('UPDATE gameOverwatch SET battletag = ? WHERE playerId = ?', array($f3->get('REQUEST.battletag'), $f3->get('SESSION.playerId')));
                    $f3->set('SESSION.Overwatch_update', 'true');
                }
                
                else {
                    $Db->exec('INSERT INTO gameOverwatch (battletag, playerId) VALUES (?, ?) ', array($f3->get('REQUEST.battletag'), $f3->get('SESSION.playerId')));
                    $f3->set('SESSION.Overwatch_insert', 'true');
                }
            }
            
            // go for delete
            else {
                $Db->exec('DELETE FROM gameOverwatch WHERE playerId = ?', $f3->get('SESSION.playerId'));
                $f3->set('SESSION.Overwatch_delete', 'true');
            }
            
            $f3->reroute('/Games');
        }
    );
    
    
    $f3->route('GET /Games',
        function ($f3){
            $Db = $f3->get('Db');
            $DbPlayer = $Db->exec('SELECT id FROM players WHERE userId = ?', $f3->get('SESSION.userId'));
            
            // we have allready a player for this user
            if ( sizeof($DbPlayer) == 1 ) {
                $DbGames = $Db->exec('SELECT games.gameId, games.gameVendor, games.gameName FROM player2games LEFT JOIN games ON player2games.gameId = games.gameId WHERE player2games.playerId = ?', $f3->get('SESSION.userId'));
                $f3->set('DbGames', $DbGames);
                
                foreach ($DbGames as $i => $game){
                    switch ($game['gameName']) {
                        
                        case 'LeagueOfLegends':
                            $LoLData = array();
                            $LoL = new \Gamepus\Riot\LeagueOfLegends($Db, $game['gameId'], $f3->get('CONF_LOL_API_KEY'));
                            $f3->set('LoLData', $LoL->getPlayerData($f3->get('SESSION.playerId')));
                            $LoLData = $LoL->getPlayerData($f3->get('SESSION.playerId'));
                            switch ($LoLData['server']){
                                case 'euw':
                                    $f3->set('euwactive', 'selected="selected"');
                                break;
                            
                                case 'eune':
                                    $f3->set('euneactive', 'selected="selected"');
                                break;
                                
                                case 'na':
                                    $f3->set('naactive', 'selected="selected"');
                                break;
                            
                                case 'lan':
                                    $f3->set('lanactive', 'selected="selected"');
                                break;
                            
                                case 'las':
                                    $f3->set('lasactive', 'selected="selected"');
                                break;
                            
                                case 'br':
                                    $f3->set('bractive', 'selected="selected"');
                                break;
                            
                                case 'tr':
                                    $f3->set('tractive', 'selected="selected"');
                                break;
                            
                                case 'ru':
                                    $f3->set('ruactive', 'selected="selected"');
                                break;
                            
                                case 'oce':
                                    $f3->set('oceactive', 'selected="selected"');
                                break;
                            
                                case 'jp':
                                    $f3->set('jpactive', 'selected="selected"');
                                break;
                            
                                case 'kr':
                                    $f3->set('kractive', 'selected="selected"');
                                break;
                            }
                        break;
                    
                        case 'Overwatch':
                            $Overwatch = new \Gamepus\Blizzard\Overwatch($Db, $game['gameId']);
                            $f3->set('battletag', $Overwatch->getPlayerData($f3->get('SESSION.playerId')));
                        break;
                    }
                }
            }
            else {
                $f3->set('error_noplayerdefined', 'true');
            }
            $f3->set('navGames', 'active');
            $f3->set('page_display', '../templates/gp_fancy/page.games.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
            $f3->clear('SESSION.Overwatch_update');
            $f3->clear('SESSION.Overwatch_insert');
            $f3->clear('SESSION.Overwatch_delete');
            $f3->clear('SESSION.LoL_update');
            $f3->clear('SESSION.LoL_insert');
            $f3->clear('SESSION.LoL_delete');
        }
    );
    
    
    $f3->route('GET /Logout',
        function ($f3){
            $f3->clear('SESSION.userId');
            $f3->clear('SESSION.username');
            $f3->clear('SESSION.loginerror');
            $f3->clear('SESSION.ts3id');
            $f3->clear('SESSION.playerId');
            $f3->reroute('/');
        }
    );
    
    $f3->route('GET /',
        function ($f3){
            $f3->reroute('/Games');
        }
    );
    
    
}



// not logged in, so we do login and register and password forget functions only
else {
    
    function randomPassword($length=12) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $length; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
    
    $f3->route('GET|POST /ResetPassword',
        function(\Base $f3){
            
            if ($f3->exists('REQUEST.email')){
                $Db =   $f3->get('Db');
                $DataDb = $Db->exec('SELECT id, username FROM users WHERE email = ?', $f3->get('REQUEST.email'));
                if (sizeof($DataDb) == 1) {
                    $newPassword = randomPassword();
                    $Db->exec('UPDATE users SET password = ? WHERE id = ?', array(md5($newPassword), $DataDb[0]['id']));

                    $smtp = new SMTP ( 'localhost', '25');
                    $smtp->set('From', '<no-reply@gamepus.org>');
                    $smtp->set('Reply-To', '<snooops84@gmail.com>');
                    $smtp->set('Errors-to', '<snooops84@gmail.com>');
                    $smtp->set('To', $DataDb[0]['username'] .' <'. $f3->get('REQUEST.email') .'>');
                    $smtp->set('Subject', 'Gamepus - your Password Reset');
                    $message = 'Hi '.$DataDb[0]['username'].','
                            . 'your new password is:'.$newPassword
                            . 'you can now proceed and login.'
                            . 'Best regards'
                            . 'Your Gamepus Team';
                    $smtp->send($message);
                }
                $f3->set('SESSION.resetPassword', 'true');
                $f3->reroute('/');
            }
            $f3->set('page_display', '../templates/gp_fancy/page.resetpassword.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
        }
    );
    
    $f3->route('POST /Login',
        function ($f3){
            $Db =   $f3->get('Db');
            $Log =  $f3->get('Log');


            $loggedinUser = $Db->exec('SELECT id, username, email FROM users WHERE username = ? AND password = ?', array($f3->get('REQUEST.username'), md5($f3->get('REQUEST.password'))));

            if ( sizeof($loggedinUser) == 1 ) {
                // persisting mostly static values
                $f3->set('SESSION.userId', $loggedinUser[0]['id']);
                $f3->set('SESSION.username', $f3->get('REQUEST.username'));
                $f3->set('SESSION.email', $loggedinUser[0]['email']);
                
                $DbTs3Id = $Db->exec('SELECT id, ts3id FROM players WHERE userId = ?', $f3->get('SESSION.userId') );
                if ( sizeof($DbTs3Id) == 1 ){
                    $f3->set('SESSION.ts3id', $DbTs3Id[0]['ts3id']);
                    $f3->set('SESSION.playerId', $DbTs3Id[0]['id']);
                }
                
                $f3->reroute('/Games');
            }
            else {
                $f3->set('SESSION.loginerror', 'true');
                $f3->reroute('/');
            }
        }
    );
    
    $f3->route('GET|POST /Register',
        function ($f3){
            
            
            if ( strlen($f3->get('REQUEST.username')) > 0) {
                $Db = $f3->get('Db');
                $Log = $f3->get('Log');

                // check if username or e-mail address already exists
                $users = array();
                $users = $Db->exec('SELECT username, email FROM users WHERE username = ? OR email = ?', array($f3->get('REQUEST.username'), $f3->get('REQUEST.email')));
                
                if ( sizeof($users) > 0) {
                    $f3->set('registerok', 'false');
                }
                else {
                    $f3->set('SESSION.registerok', 'true');
                    $Db->exec('INSERT INTO users (username, email, password) VALUES (?, ?, ?)', array($f3->get('REQUEST.username'), $f3->get('REQUEST.email'), md5($f3->get('REQUEST.password')) ));
                    $f3->reroute('/');
                }
            }
            
            $f3->set('page_display', '../templates/gp_fancy/page.register.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
        }
    );
    
    $f3->route('GET /',
        function ($f3){
            $f3->set('page_display', '../templates/gp_fancy/page.login.html');
            echo \Template::instance()->render('../templates/gp_fancy/index.html');
            $f3->clear('SESSION.registerok');
            $f3->clear('SESSION.resetPassword');
            $f3->clear('SESSION.loginerror');
        }
    );
}

$f3->run();
