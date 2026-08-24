<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

defined('_JEXEC') or die;

class Security
{

    /**
     * Encrypt and decrypt a string
     * 
     * @param string $action close/open
     * @param string $string string
     * @return NULL|string
     */
    public static function encryptor($action, $string)
    {
        $output = NULL;

        $encrypt_method = "AES-256-CBC";
        $secret = \Secretary\Joomla::getApplication()->get('secret');
        $secret_key = $secret . 'pAIo2';
        $secret_iv = $secret . 'pAl1Io2';

        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        switch ($action)
		{
            case 'close':
                $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                $output = base64_encode($output);
                break;

            case 'open':
                $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
                break;

            default:
                break;
        }

        return $output;
    }
}