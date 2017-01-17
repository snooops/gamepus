<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Gamepus;
//use TeamSpeak3\TeamSpeak3;
/**
 * Description of MyTeamSpeak
 *
 * @author Snooops
 */
class MyTeamSpeak {
    
    protected $TS3;
    
    public function __construct($TS3) {
        $this->TS3 = $TS3;
    }
    
    
    public function assignServerGroup($ts3id, $new_tsgroupId, $teamspeakGroupMap){       
        
        $tsClient = $this->TS3->clientGetByUid($ts3id);

        # parse Groups to work with it
        $tsClientGroups = $tsClient->getInfo();
       
       
        if (is_int($tsClientGroups['client_servergroups'])) {
            $parsedServerGroups = array($tsClientGroups['client_servergroups']);
        }
        else {
            $parsedServerGroups = \explode(',', $tsClientGroups['client_servergroups']);
        }
       

         # check if user already have this group
         $found = false;

         foreach ($parsedServerGroups as $groupId) {

             # match against the potentail new ts group
            if ($groupId == $new_tsgroupId) {
                $found = true;
            }
            
            // $overwatch_ranks war nicht gesetzt, daraufhin kam dieser fehler: 
            // in_array() expects parameter 2 to be array, null given

            if (in_array($groupId, $teamspeakGroupMap) && !$found) {
                $this->TS3->clientGetByUid($ts3id)->remServerGroup($groupId);
            }
         }

         # assign new Servergroup
         if (!$found) {
             $this->TS3->clientGetByUid($ts3id)->addServerGroup($new_tsgroupId);
         }
    }
}
