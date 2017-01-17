<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GameId
 *
 * @author Snooops
 */
namespace \Gamepus\GameId;

class GameId {
    
    protected $GameId;
    
    public function __construct(Player $Player) {
        $this->GameId = $this->fetchGameId($Player->getPlayerId());
    }
    
    private function fetchGameId($id) {
        // magic shit here
        return $GameId;
    }
}
