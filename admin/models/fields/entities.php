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

class JFormFieldEntities extends JFormFieldList
{

	protected $type = 'entities';

	public function getInput()
	{
		$params = Secretary\Application::parameters();
		if ($params->get('entitySelect') != 1) {

			$html = '<input id="' . $this->id . '_entity" type="text" class="fullwidth" name="' . $this->name . '" value="' . $this->value . '" />';
		} else {

			$options = array();
			$items = \Secretary\Database::getObjectList('entities', ['id', 'title'], [], 'title ASC');
			$options[] = JHtml::_('select.option', 0, JText::_('COM_SECRETARY_SELECT_OPTION'));
			foreach ($items as $message) {
				$options[] = JHtml::_('select.option', $message->id, JText::_($message->title));
			}

			$html = '<div class="select-arrow select-arrow-white">'
				. '<select name="' . $this->name . '" id="' . $this->id . '" class="form-control entity-select">'
				. JHtml::_('select.options', $options, 'value', 'text', $this->value)
				. '</select></div>';
		}

		return $html;
	}

	public function getOptions()
	{

		$html = array();

		$params = Secretary\Application::parameters();
		if ($params->get('entitySelect') == 1) {

			$db = \Secretary\Database::getDBO();
			$query = $db->getQuery(true)
				->select("id,title")
				->from($db->qn('#__secretary_entities'))
				->order('title ASC');

			$db->setQuery($query);
			$items = $db->loadObjectList();

			$html[] = JHtml::_('select.option', 0, JText::_('COM_SECRETARY_ENTITY'));
			foreach ($items as $message) {
				$html[] = JHtml::_('select.option', $message->id, $message->title);
			}
		}

		return $html;
	}

	public function getList($default = 0, $name = 'jform[items][##counter##][entity]')
	{
		$html = $this->getOptions();
		$result = '<select name="' . $name . '" id="jform_items_entity" class="form-control entity-select">' . JHtml::_('select.options', $html, 'value', 'text') . '</select>';
		return $result;
	}
}