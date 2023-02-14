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

class JFormFieldSecretarystatus extends JFormFieldList
{

	protected $type = 'secretarystatus';

	public function getOptions($extension = 'root')
	{
		$db = \Secretary\Database::getDBO();

		if (!empty($this->element['extension'])) {
			$extension = (string) $this->element['extension'];
		} elseif ($mod = \Secretary\Joomla::getApplication()->input->getCmd('module')) {
			$extension = $mod;
		} else {
			$extension = $extension;
		}

		$html = array();

		$query = $db->getQuery(true)
			->select("id,title")
			->from($db->quoteName('#__secretary_status'))
			->where($db->quoteName('extension') . ' = ' . $db->quote($extension))
			->order('ordering ASC, id ASC');

		$db->setQuery($query);
		$items = $db->loadObjectList();

		foreach ($items as $message) {
			$html[] = JHtml::_('select.option', $message->id, JText::_($message->title));
		}

		return $html;
	}

	public function getObject($id)
	{
		$items = new JObject();

		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true)
			->select("*")
			->from($db->quoteName('#__secretary_status'))
			->where($db->quoteName('id') . ' = ' . $db->escape($id));

		$db->setQuery($query);
		$items = $db->loadAssoc();

		return $items;
	}
}