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

// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldProductUsage extends JFormFieldList
{
	protected $type = 'productUsage';

	public function getOptions($default = false)
	{
		$html = array();

		$result = array(
			0 => JText::_('COM_SECRETARY_NONE'),
			1 => JText::_('COM_SECRETARY_PRODUCT_USAGE_VERBRAUCH'),
			2 => JText::_('COM_SECRETARY_PRODUCT_USAGE_EINKAUF'),
		);

		foreach ($result as $key => $value) {
			$html[] = JHtml::_('select.option', $key, $value);
		}

		if ($default == false) {
			return $html;
		} else {
			return $html[$default];
		}
	}

	public function getList($default, $name = 'jform[fields][pUsage]')
	{
		$html = $this->getOptions();
		$result = '<select name="' . $name . '" class="form-control inputbox pUsage">' . JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
}