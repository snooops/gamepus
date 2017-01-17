<?php
$f3 = require('../vendor/bcosca/fatfree/lib/base.php');


$CONF_DB_HOST = 'localhost';
$CONF_DB_USER = 'development';
$CONF_DB_PASS = 'ieXahngae7hia4iSa1oizain';
$CONF_DB_DB = 'development';
$CONF_TEMPLATE = 'gp_fancy';

$f3->set('CACHE','memcache=localhost');


$f3->set('AUTOLOAD',array('../Controller/'));
$f3->set('my_template_path', '../templates/'.$CONF_TEMPLATE);




$f3->clear('CACHE');

new Session();

// instanciate global database connection
$Db = new \DB\SQL('mysql:host=localhost;port=3306;dbname=development','development','ieXahngae7hia4iSa1oizain');
$f3->set('Db', $Db);



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


/*
$f3->route('POST /Logout',
    function (){
        Login::logout();
    }
);
*/
$f3->set('title','Gamepus');
$f3->route('GET /',
    function ($f3){
        $f3->set('name','world');
        $f3->set('buddy',array('Tom','Dick','Harry'));
        echo \Template::instance()->render('../templates/gp_fancy/index.html');
    }
);


$f3->run();


/*
$player = new Player;


foreach ($player->getGames() as $game) {
    
    try{
        $GameId = new GameId($Player);
        $gameObject = new $game;
        $gameObject->setGameId($GameId);
        $rank = $gameObject->getRank();
    }
    catch (Exception $e) {
        echo 'Unkown Game detected!',  $e->getMessage(), "\n";
    }
}*/