<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LeagueOfLegends
 *
 * @author Snooops
 */
namespace Gampus\Riot;

class LeagueOfLegends extends Riot implements \Gamepus\Game {
   
    protected   $Db,
                $GameId,
                $TeamspeakGroupMap,
                $API_Key
            ;
    
    /**
     * 
     * @param \DB\SQL $Db
     * @param type $GameId
     */
    public function __construct(\DB\SQL $Db, $GameId, $API_Key) {
        $this->Db = $Db;
        $this->GameId = $GameId;
        $this->API_Key = $API_Key;
        
        
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
     * @param type $SummonerName
     * @return type \Gamepus\Rank::createRankData($rank, $tsgroup)
     */
    public function getPlayerRank($SummonerName){
        
        # fetching json data from leagueoflegends API
        $overwatch_data = json_decode(file_get_contents('https://na.api.pvp.net/api/lol/euw/v1.4/summoner/by-name/'.$SummonerName.'?api_key='.$this->API_Key));
        
        foreach ($this->TeamspeakGroupMap as $rank => $tsgroupId ) {

            if ($overwatch_data->data->competitive->rank >= $rank) {
                $new_tsgroupId = $tsgroupId;
            }
        }

        return \Gamepus\Rank::createRankData($overwatch_data->data->competitive->rank, $new_tsgroupId);
    }
}
