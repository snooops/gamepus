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
namespace Gamepus\Riot;

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
     * @param type $playerId
     * @return type
     */
    public function getPlayerData($playerId) {
        $DbRows = $this->Db->exec('SELECT server, summonerName FROM gameLeagueOfLegends WHERE playerId = '.$playerId);
        return $DbRows[0];
    }
    
    /**
     * 
     * @param type $server
     * @param type $call
     * @return type
     */
    private function makeAPICall($server, $call) {
        return json_decode(file_get_contents('https://'.$server.'.api.pvp.net/api/lol/'.$server.'/'.$call.'?api_key='.$this->API_Key));
    }
    
    
    /**
     * 
     * @param type $playerId
     * @return type \Gamepus\Rank::createRankData($rank, $tsgroup)
     */
    public function getPlayerRank($playerId){
        $playerData = $this->getPlayerData($playerId);
        
        $lolData = array();
        # fetching json data from leagueoflegends API
        $lolPlayerData = $this->makeAPICall($playerData['server'], 'v1.4/summoner/by-name/'.rawurlencode($playerData['summonerName']));
        
        foreach ($lolPlayerData as $data) {
            $lolId = $data->id;
        }
        
                
        $lolRankData = array();
        $lolRankData = $this->makeAPICall($playerData['server'], 'v2.5/league/by-summoner/'.$lolId.'/entry');
        var_dump($lolRankData);
        foreach ($lolRankData as $data) {
            $lolRank = $data[0]->tier;
            echo 'LolRank: '.$lolRank;
        }
        
        return \Gamepus\Rank::createRankData($lolRank, $this->TeamspeakGroupMap[$lolRank]);
    }
}
