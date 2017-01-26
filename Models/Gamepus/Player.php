<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Player
 *
 * @author Snooops
 */

namespace Gamepus;

class Player {
    protected   $PlayerId,
                $ts3id,
                $Db,
                $Games
            ;
    
    /**
     * 
     * @param type $PlayerId
     * @param type $ts3id
     * @param \DB\SQL $Db
     */
    public function __construct($PlayerId, $ts3id, \DB\SQL $Db){
        $this->Db = $Db;
        $this->PlayerId = $PlayerId;
        $this->ts3id = $ts3id;
    }

    /**
     * 
     */
    public function getInfo(){
        
    }
    
    /**
     * 
     * @return type
     */
    public function tsid() {
        return $this->ts3id;
    }
    
    /**
     * 
     * @return type
     */
    public function games() {
        $dbGames = $this->Db->exec('SELECT games.gameId, games.gameName, games.gameVendor FROM games');
        foreach ($dbGames as $dbGame){
            $this->Games[$dbGame['gameVendor']]['gameName'] = $dbGame['gameName'];
            $this->Games[$dbGame['gameVendor']]['gameId'] = $dbGame['gameId'];
        }
        return $this->Games;
    }
}
