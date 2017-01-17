<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of auth
 *
 * @author Snooops
 */

//namespace Gamepus\Controller;

class Login {
    
    protected   $Auth,
                $Db,
                $f3;
    

    
    public function __construct(Base $f3) {
        $this->f3 = $f3;
        $this->setDb($this->f3->get('Db'));
        $this->Auth = $this->f3->get('Auth');
    }
    
    
    public function checkLogin($username, $password) {
        if (!$this->Auth->login($username,$password)) {
            throw new Exception('Login failed');
        }
    }  
    
    public function login ($username, $password) {
        
    }
    
    public function logout() {
        
    }
}
