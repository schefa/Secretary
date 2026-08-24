<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\HTML;

defined('_JEXEC') or die;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

class Reports
{

    public static function documentsItem($i, $item, $cssClass, $documentsCharts)
    {
        $html = array();
        
        foreach ($item as $type => $val)
		{
            if ($type === substr($cssClass, 0, 1))
			{
                foreach ($val as $curr => $val2)
				{
                    $x = 1;
                    //  krsort($val2);

                    foreach ($val2 as $key2 => $values)
					{
                        if (!is_numeric($key2))
                        {
                            continue;
                        }
                        
                        if ($x > 1)
                        {
                        	$html[] =  " / ";
                        }
                        $html[] = '<span class="' . $cssClass . ' status-' . $values['css'] . '">';
                        // $html[] = $val[0] .': ';
                        if (!empty($values[1]) && $values[1] > 0)
						{
                            $html[] = \Secretary\Utilities\Number::getNumberFormat($values[1], $curr);
                        }
                        else
						{
                            $html[] = \Secretary\Utilities\Number::getNumberFormat(0, $curr);
                        }
                        $html[] = "</span>";
                        $typePos = ($cssClass === 'income') ? 0 : 1;
                        $documentsCharts['classes'][$i][$typePos][] = $cssClass . ' status-' . $values['css'];
                        $documentsCharts['series'][$i][$typePos][$values[0]] = round($values[1], 2) . ' ' . $curr;

                        $x++;
                    }
                    $html[] = "<br>";
                }
            }
        }

        return array('html' => implode("", $html), 'data' => $documentsCharts);
    }
}
