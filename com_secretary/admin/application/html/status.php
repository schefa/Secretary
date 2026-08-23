<?php

namespace Secretary\HTML;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

use Secretary\Database;

defined('_JEXEC') or die;

class Status
{

	public static function state($item, $i, $taskPrefix = '', $canChange = FALSE, $state = array())
	{
		$value = (isset($item->state)) ? $item->state : 0;

		// Liste von Buttons. Bei Klick soll das Gegenteil ermöglicht werden, also wenn Open, dann Schließen
		// use closeTask 
		if (empty($state))
		{
			$state = Database::getQuery('status', (int) $value, 'id', '*', 'loadAssoc');
		}

		// Task ist das Gegenteil. 
		$html = '';

		$style = $taskPrefix;
		$style = (!empty($style)) ? 'status-' . $style : '';

		if ($canChange)
		{
			$html   = '<div class="secretary-status-button" data-id="' . $item->id . '" data-section="' . $taskPrefix . '">
                        <span class="hasTooltip secretary-state ' . $style . ' ' . $state['class'] . '"
							data-original-title="' . \Joomla\CMS\Language\Text::_($state['description']) . '">
								<span class="secretary-state-icon fa fa-' . $state['icon'] . '"></span>
								<span class="secretary-state-title">' . \Joomla\CMS\Language\Text::_($state['title']) . '</span>
							</span></div>';
		}
        else
		{
			$html   = '<div class="secretary-state ' . $style . ' ' . $state['class'] . '">
							<span class="secretary-state-icon fa fa-' . $state['icon'] . '"></span>
							<span class="secretary-state-title">' . \Joomla\CMS\Language\Text::_($state['title']) . '</span>
						</div>';
		}

		return $html;
	}

	public static function checkall($name = 'checkall-toggle', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')
	{
		return '<input type="checkbox" name="checkall-toggle" value="" title="' . \Joomla\CMS\Language\Text::_("JGLOBAL_CHECK_ALL") . '" onclick="Joomla.checkAll(this)" />';
	}

	public static function isdefault($value, $i, $prefix = '', $enabled = true)
	{

		$html = "";

		$states = array(
			0 => array('setDefault', '', 'JLIB_HTML_SETDEFAULT_ITEM', '', 1, 'star-o', ''),
			1 => array('unsetDefault', 'JDEFAULT', 'JLIB_HTML_UNSETDEFAULT_ITEM', 'JDEFAULT', 1, 'star', 'btn-isdefault-active'),
		);

		if ($value == 0)
		{
			$html = '<a class="btn-isdefault ' . $states[$value][6] . ' hasTooltip" onclick="return listItemTask(\'cb' . $i . '\',\'' . $prefix . $states[$value][0] . '\')" href="javascript:void(0);" data-original-title="' . \Joomla\CMS\Language\Text::_($states[$value][2]) . '"><span class="fa fa-' . $states[$value][5] . '"></span></a>';
		}
        elseif ($value == 1)
		{
			$html = '<span class="btn-isdefault ' . $states[$value][6] . ' hasTooltip"><i class="fa fa-' . $states[$value][5] . '"></i></span>';
		}

		return $html;
	}
}
