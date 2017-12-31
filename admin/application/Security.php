<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary;

// No direct access
defined('_JEXEC') or die; 

class Security {
    
    /**
     * Encrypt and decrypt a string
     * 
     * @param string $action close/open
     * @param string $string string
     * @return NULL|string
     */
    public static function encryptor($action,$string) {
        $output = NULL;
        
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'pAIo2';
        $secret_iv = 'pAl1Io2';
        
        $key	= hash('sha256', $secret_key);
        $iv		= substr(hash('sha256', $secret_iv), 0, 16);
        
        switch ($action) {
                
            case 'close' :
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
                break;
                
            case 'open' :
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                break;
                
            default: break;
        } 
        
        return $output;
    }
    
} 
