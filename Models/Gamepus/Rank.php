<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Gamepus;
class Rank {
    
    static function createRankData ($newrank, $tsid){
        $rank = new \stdClass();
        $rank->rank = $newrank;
        $rank->newTeamSpeakGroupId = $tsid;
        return $rank;
    }
}