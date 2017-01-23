<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of log
 *
 * @author Snooops
 */
namespace Helper;

class Log {
    protected $defaultChannel;
    
    protected $Db;
    
    /**
     * 
     * @param type $defaultChannel
     */
    public function __construct ($defaultChannel){
        $this->defaultChannel = $defaultChannel;
    }
    
    
    /**
     * writes a message into the database
     * 
     * @param type $message
     */
    private function channelMySQL($message){
        try {
            // Todo: $message escaping
            $this->Db->exec('INSERT INTO logger (message) VALUES ("'. $message .'")');
        } catch (Exception $e) {
            echo 'Cant write message into Database: '.$e;
        }
    }
    
    
    public function message($message, $channel='') {
        
        if ($channel == '') {
            $useChannel = $this->defaultChannel;
        }
        else {
            // maybe some checks if channel is valid und existing
            $useChannel = $channel;
        }
        
        
        switch ($useChannel) {
            case 'mysql':
                $this->channelMySQL($message);
            break;
        }         
    }
    
    
    /**
     * 
     * @param type $Db
     */
    public function setupChannelMySQL(\DB\SQL $Db) {
        $this->Db = $Db;
    }
    
  
}
