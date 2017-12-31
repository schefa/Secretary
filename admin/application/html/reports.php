<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH .'/application/HTML.php';

defined('_JEXEC') or die;

class Reports
{
	
	public static function documentsItem($i, $item, $cssClass, $documentsCharts, $currencyRates = array())
	{
        $html = array();
        foreach($item AS $type => $val) {
            
            if( $type === substr($cssClass,0,1)) {
                foreach($val AS $curr => $val2) {
                    $x = 1;
                  //  krsort($val2);
                    
                    foreach($val2 AS $key2 => $values) {
                        if(!is_numeric($key2))
                            continue;
                        if($x > 1) $html[] =  " / ";
                        $html[] = '<span class="'.$cssClass.' status-'.$values['css'].'">';
                            // $html[] = $val[0] .': ';
                            if(!empty($values[1]) && $values[1] > 0) {
                                $html[] = \Secretary\Utilities\Number::getNumberFormat($values[1],$curr);
                            } else {
                                $html[] = \Secretary\Utilities\Number::getNumberFormat(0,$curr);
                            }
                        $html[] = "</span>";
                        $typePos = ($cssClass === 'income') ? 0 : 1;
                        $documentsCharts['classes'][$i][$typePos][] = $cssClass.' status-'.$values['css']; 
                        // Currency Converter
                        if(isset($currencyRates[$curr])) {
                            $documentsCharts['series'][$i][$typePos][$values[0]] = round ($values[1] / $currencyRates[$curr], 2) .' '.$curr;
                        } else {
                            $documentsCharts['series'][$i][$typePos][$values[0]] = round( $values[1] , 2). ' '.$curr;
                        }
                        
                        $x++; 
                    }
                    $html[] = "<br>";
                }
                
            }
        }
        
        return array('html'=> implode("",$html), 'data' => $documentsCharts );
	}
	
}
