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

    
    public function getInfo(){
        
    }
    
    
    /**
     * 
     * @return type
     */
    public function games() {
        $dbGames = $this->Db->exec('SELECT * FROM playerAttributes LEFT JOIN games ON games.gameId = playerAttributes.gameId WHERE playerId = '.$this->PlayerId);
        foreach ($dbGames as $dbGame){
            $this->Games[$dbGame['gameVendor']][$dbGame['gameId']]['gameName'] = $dbGame['gameName'];
            $this->Games[$dbGame['gameVendor']][$dbGame['gameId']]['playerName'] = $dbGame['value'];
        }
        return $this->Games;
    }
}
