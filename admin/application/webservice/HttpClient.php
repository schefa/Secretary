<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\Webservice;

use JFactory;

// No direct access
defined('_JEXEC') or die;

class HttpClient
{

    private $url;
    private $timeout;

    public function __construct($url, $timeout)
    {
        $this->url = $url;
        $this->timeout = $timeout;
    }

    public function execute()
    {
        $application = JFactory::getApplication();
    
        $ch = curl_init($this->url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->timeout,
        ));

        $response = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($httpStatus !== 200)
        {
            $application->enqueueMessage("HTTP call failed with error ".$httpStatus.".", 'error');
        }
        elseif ($response === false)
        { 
            $application->enqueueMessage("HTTP call failed empty response.", 'error');
        }

        return $response;
    }

}
