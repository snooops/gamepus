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
namespace Gamepus\Blizzard;

class Overwatch extends Blizzard implements \Gamepus\Game {
   
    protected   $Db,
                $GameId,
                $TeamspeakGroupMap
            ;
    
    /**
     * 
     * @param \DB\SQL $Db
     * @param type $GameId
     */
    public function __construct(\DB\SQL $Db, $GameId) {
        $this->Db = $Db;
        $this->GameId = $GameId;
        
        
        $dbRanks = $this->Db->exec('SELECT * FROM teamspeakGroupMap WHERE gameId = '.$this->GameId.' ORDER BY rankLimit ASC');
        foreach ($dbRanks as $dbRank) {
            $this->TeamspeakGroupMap[$dbRank['rankLimit']] = $dbRank['teamspeakGroupId'];
        }
    }
    
    /**
     * 
     * @return TeamspeakGroupMap
     */
    public function getTeamSpeakGroupMap() {
        return $this->TeamspeakGroupMap;
    }
    /**
     * 
     * @param type $Battletag
     * @return type \Gamepus\Rank::createRankData($rank, $tsgroup)
     */
    public function getPlayerRank($Battletag){
        
        # preparing request data
        $BattletagReqReady = str_replace('#', '-', $Battletag);

        # fetching json data from overwatch API
        $overwatch_data = json_decode(file_get_contents('https://api.lootbox.eu/pc/eu/'. $BattletagReqReady .'/profile'));
        
        foreach ($this->TeamspeakGroupMap as $rank => $tsgroupId ) {

            if ($overwatch_data->data->competitive->rank >= $rank) {
                $new_tsgroupId = $tsgroupId;
            }
        }

        return \Gamepus\Rank::createRankData($overwatch_data->data->competitive->rank, $new_tsgroupId);
    }
}
