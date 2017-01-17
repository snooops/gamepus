<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Overwatch
 *
 * @author Snooops
 */
namespace \Games\Blizzard\Hearthstone;

class Hearthstone extends Blizzard implements Game {
   
    /**
    *	Constructor
    **/
    public function __construct(){
        
    }
    
    /**
    *	returns the current Hearthstone rank of the initialized user
    *	@return int
    **/
    public function getRank(){
        return $rank;   
    }
}
