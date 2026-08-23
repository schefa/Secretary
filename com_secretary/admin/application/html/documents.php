<?php

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';


defined('_JEXEC') or die;

class Documents
{
    /**
     * Method to get a summary of documents depending on their status
     * 
     * @param array $data
     * @param int $totalData
     * @return string HTML
     */
    public static function summary($data, $totalData)
    {

        $sum = array();
        $html = array();

        for ($i = 0; $i < $totalData; $i++)
		{
            $currentCurrency = $data[$i]->currency;
            $nextCurrency = (isset($data[$i + 1]->currency)) ? $data[$i + 1]->currency : 0;
            
            if (!isset($sum[$currentCurrency]))
            {
            	$sum[$currentCurrency] = array();
            }
            $sum[$currentCurrency][] = $data[$i]->total;

            $html[] = \Joomla\CMS\Language\Text::_($data[$i]->status_title) . ': ';
            $html[] = '<span class="brutto-' . $data[$i]->class . ' pull-right">';
            $html[] .= \Secretary\Utilities\Number::getNumberFormat($data[$i]->total, $data[$i]->currencySymbol);
            $html[] .= '</span><br>';

            if (($totalData - 1) == $i || $currentCurrency !== $nextCurrency)
			{
                if (count($sum[$currentCurrency] ?? []) > 1 && ($currentCurrency !==  $nextCurrency))
				{
                    $html[] = '<h4 class="text-right">';
                    $html[] = \Secretary\Utilities\Number::getNumberFormat(array_sum($sum[$currentCurrency]), $data[$i - 1]->currencySymbol);
                    $html[] = '</h4>';
                }

                if (($totalData - 1) !== $i)
                {
                    $html[] = '<div class="sidebar-split"></div>';
                }
            }
        }

        return implode('', $html);
    }
}
