<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
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
