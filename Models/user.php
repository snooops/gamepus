<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author Snooops
 */
class user {
    
    protected 
                $Db,
                $userId
            ;
    
    public function _construct($userId, \DB\SQL $Db){
        $this->Db       = $Db;
        $this->userId   = $userId;
        
        $DbUser = $this->Db->exec('SELECT username, email FROM users WHERE id = ?', $this->userId);
        var_dump($DbUser);
    }
    
    
}
