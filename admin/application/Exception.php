<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary;

class Exception extends \Exception
{
    
    /**
     * Class constructor
     *
     * @param string $message Error message
     * @param int $code       Error code
     */
    function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
    }
    
}