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

// No direct access
defined('_JEXEC') or die; 

class Webservice {
    
    private static $_currencyRates = array();
    
    /**
     * Method to get the currency rates
     * 
     * @param array $to
     * @param string $from
     * @return array
     */
    public static function currencyConverter( $to = array(), $from = "EUR") {
        
        if(empty(self::$_currencyRates)) {
            $XML=simplexml_load_file("http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
            //the file is updated daily between 2.15 p.m. and 3.00 p.m. CET
            
            foreach($XML->Cube->Cube->Cube as $rate){
                $currency = (string) $rate["currency"];
                if(in_array($currency,$to))
                    self::$_currencyRates[$currency] = floatval( $rate["rate"] );
            }
        }
        
        return self::$_currencyRates;
    }
    
} 
