<?php
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
//require('../vendor/fkubis/teamspeak-php-framework/TeamSpeak3/TeamSpeak3.php');
$tslogin = sprintf('serverquery://%s:%s@%s/?server_port=%u', $CONF_TS3_USER, $CONF_TS3_PASS, $CONF_TS3_ADDRESS, $CONF_TS3_VSERVERPORT, $CONF_TS3_VSERVERPORT);
$TS3 = \TeamSpeak3\TeamSpeak3::factory($tslogin);
$MyTeamSpeak = new Gamepus\MyTeamSpeak($TS3);

$f3->set('MyTeamSpeak', $MyTeamSpeak);



$f3->route('GET|HEAD|POST /Login',
    function ($f3){
        $Db = $f3->get('Db');
        
        // instanciate global authenticate mechanism
        $db_mapper = new \DB\SQL\Mapper($Db, 'users');
        $Auth = new \Auth($db_mapper, array('id' => 'username', 'pw' => 'password'));
        $f3->set('Auth', $Auth);
        
        $Login = new Login;
        $Login->setF3($f3);
        
        return $Login->checkLogin($f3->get('REQUEST.username'), $f3->get('REQUEST.password'));
    }
);


$f3->set('title','Gamepus');
$f3->route('GET /',
    function ($f3){
        $f3->set('name','world');
        $f3->set('buddy',array('Tom','Dick','Harry'));
        echo \Template::instance()->render('../templates/gp_fancy/index.html');
    }
);


$f3->run();
