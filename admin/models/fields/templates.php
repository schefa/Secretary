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

class JFormFieldTemplates extends JFormFieldList
{

	protected $type = 'templates';
	protected static $_items = array();

	public function getOptions($only = array())
	{

		$html = array();

		if (empty(self::$_items)) {

			$extension = $this->element ? (string) $this->element['extension'] : NULL;
			$business = Secretary\Application::company();

			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true)
				->select("id,title")
				->from($db->quoteName('#__secretary_templates'))
				->where($db->qn('business') . ' = ' . intval($business['id']));

			if (!empty($only))
				$query->where('extension LIKE (' . $db->quote(implode('","', $only)) . ')');

			$query->order('id ASC');

			if (!empty($extension))
				$query->where($db->quoteName('extension') . '=' . $db->quote($extension));
			try {
				$db->setQuery($query);
				self::$_items = $db->loadObjectList();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}

		$items = self::$_items;

		$html[] = JHtml::_('select.option', 0, JText::_("COM_SECRETARY_NONE"));
		foreach ($items as $message) {
			$html[] = JHtml::_('select.option', $message->id, JText::_($message->title));
		}

		return $html;
	}

	public function getList($default, $name = 'jform[fields][template]', $class = "", $only = array())
	{
		$html = $this->getOptions($only);
		$result = '<select name="' . $name . '" class="form-control inputbox ' . $class . '">' . JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
}