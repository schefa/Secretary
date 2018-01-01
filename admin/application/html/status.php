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

use JText;
use JArrayHelper;
use JHtml;
use Secretary\Database;

// No direct access
defined('_JEXEC') or die;

class Status
{
	
	public static function state($value = 0, $i, $taskPrefix = '', $canChange = FALSE, $state = array() )
	{
		// Liste von Buttons. Bei Klick soll das Gegenteil ermöglicht werden, also wenn Open, dann Schließen
		// use closeTask 
		if(empty($state)) $state = Database::getQuery('status', (int) $value , 'id', '*', 'loadAssoc');
		
		// Task ist das Gegenteil. 
		$html = '';
		
		$style = substr($taskPrefix, 0, -1);
		$style = (!empty($style)) ? 'status-'.$style : '';
		
		if ($canChange) {
			$html   = '<a href="javascript:void(0);" class="hasTooltip secretary-state '.$style.' '.$state['class'].'"
							onclick="return listItemTask(\'cb'.$i.'\',\''. $taskPrefix .'setStates\')"
							data-original-title="'.JText::_($state['description']).'">
								<span class="secretary-state-icon fa fa-'.$state['icon'].'"></span>
								<span class="secretary-state-title">'. JText::_($state['title']) .'</span>
							</a>';
		} else {
			$html   = '<div class="secretary-state '.$style.' '.$state['class'].'">
							<span class="secretary-state-icon fa fa-'.$state['icon'].'"></span>
							<span class="secretary-state-title">'. JText::_($state['title']) .'</span>
						</div>';
		}
		
		return $html;
	}
	
	public static function messages($value = 0, $i, $canChange)
	{
		// Array of image, task, title, action.
		$states	= array(
			-2	=> array('trash.png',		'messages.unpublish',	'JTRASHED',				'COM_SECRETARY_MESSAGES_MARK_AS_UNREAD'),
			1	=> array('tick.png',		'messages.unpublish',	'COM_SECRETARY_MESSAGES_OPTION_READ',		'COM_SECRETARY_MESSAGES_MARK_AS_UNREAD'),
			0	=> array('publish_x.png',	'messages.publish',		'COM_SECRETARY_MESSAGES_OPTION_UNREAD',	'COM_SECRETARY_MESSAGES_MARK_AS_READ')
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[0]);
		$html	= JHtml::_('image', 'admin/'.$state[0], JText::_($state[2]), null, true);
		if ($canChange)
		{
			$html = '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">'
					.$html.'</a>';
		}

		return $html;
	}
	
	public static function checkall($name = 'checkall-toggle', $tip = 'JGLOBAL_CHECK_ALL', $action = 'Joomla.checkAll(this)')
	{
		//JHtml::_('bootstrap.tooltip');
		return '<input type="checkbox" name="checkall-toggle" value="" title="'. JText::_("JGLOBAL_CHECK_ALL") .'" onclick="Joomla.checkAll(this)" />';
		// return '<input type="checkbox" name="' . $name . '" value="" class="hasTooltip" title="' . $tip . '" onclick="' . $action . '" />';
	}
	
	public static function isdefault($value, $i, $prefix = '', $enabled = true )
	{
		
		$html = "";
		
		$states = array(
				0 => array('setDefault', '', 'JLIB_HTML_SETDEFAULT_ITEM', '', 1, 'star-o', ''),
				1 => array('unsetDefault', 'JDEFAULT', 'JLIB_HTML_UNSETDEFAULT_ITEM', 'JDEFAULT', 1, 'star', 'btn-isdefault-active'),
			);
			
		if($value == 0)
		{
			$html = '<a class="btn-isdefault '.$states[$value][6].' hasTooltip" onclick="return listItemTask(\'cb'.$i.'\',\''.$prefix . $states[$value][0].'\')" href="javascript:void(0);" data-original-title="'. JText::_($states[$value][2]).'"><span class="fa fa-'.$states[$value][5].'"></span></a>';
		}
		elseif($value == 1)
		{
			$html = '<span class="btn-isdefault '.$states[$value][6].' hasTooltip"><i class="fa fa-'.$states[$value][5].'"></i></span>';
		}

		return $html;
	}
}
