<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author Thomas
 */
class user {
    private $session;
    private $logged_in = false;
    private $userdata;
    
    public function __construct() {
        if($s = $this->getSession()) {
            $this->logged_in = true;
            $this->userdata = $s;
        }
    }
    
    public function __destruct() {
        
    }
    
    public function login($username, $password, $db = null) {
        if($db == null)
            throw new Exception ("No database connection provided", 1, null);
        
        $user = $db->select("users", ["*"], "username=$1", [$username]   );
        
        if($user->rows > 0) {
            $data = $user->fetch();
            if($data["password"] == sha1($password)) {
                $this->userdata = $data;
                $this->setSession();
                return true;
            }
        }        
        return false;
    }
    
    public function logout() {
        if($this->isLoggedIn()) {
            $this->session = null;
            unset($this->session);
            
            $_SESSION["usersession"] = null;
            unset($_SESSION["usersession"]);
        }
            
    }
    
    private function getSession() {
        if(session_status() == PHP_SESSION_NONE)
            session_start();

        try {
            $session_data = arrGet($_SESSION, "usersession");
            $this->session = $session_data;
            return $session_data;
        } catch(Exception $e) {
            return false;
        }
    }
    
    private function setSession() {
        if(!$this->getSession()) {
            $session_data = $this->userdata;
            $_SESSION["usersession"] = $session_data;
            $this->session = $session_data;
        }
    }
    
    public function isLoggedIn() {
        return $this->logged_in;
    }
    
    public function get($var) {
        return arrGet($this->session, $var);
    }
}
