<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhpBot;


/**
 * Description of Bot
 *
 * @author johndoe
 */
class Bot {
    
    /**
     *
     * @var type PhpBot\EventHandler
     */
    protected $event_handler;
    
    /**
     * @var type curl
     */
    protected $curl_handle;
    
    /**
     *
     * @var type int
     */
    protected $update_offset; 
    
    
    
    const API_URL = Constants::BASE_URL . '/bot' . Constants::BOT_TOKEN ;
    
    
    
    
    
    function __construct() {
        $this->curl_handle = curl_init();
        curl_setopt($this->curl_handle, CURLOPT_POST, true);
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);    
        
        
    }
    function loop($callable){
        $callable; //Does required initializations 

        curl_setopt($this->curl_handle,  API_URL . '/getUpdates');
        while(true){
            curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, ['offset' => $this->update_offset]);
            $updates = curl_exec($this->curl);           
            $a = json_decode($updates, true);
            $updatesArray = $a['result'];
            if(isset($a['update_id'])){
                $this->update_offset = $a['update_id'];
            }
            
         if(function_exists($this->event_handler->onUpdateNewMessage)){
             $this->event_handler->onUpdateNewMessage($update);
         }
            
        }
    }
    
    
    function sendMessage(array $options) {
        
    }
    
    function log($something){
        error_log($something);
    }
    

    /**
     * 
     * Set an event handler for updates 
     * @param type $handler
     */
    function setEventHandler($handler){
        $this->event_handler = $handler;
    }
    
}
