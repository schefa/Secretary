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
// no direct access
defined('_JEXEC') or die;

class JFormFieldCategoriesMultiselect extends JFormField
{

	var $type = 'categoriesmultiselect';

	function getInput($extension = false, $name = false, $selected = false)
	{
		$folders = array();
		if (empty($extension)) {
			$extension = (string) $this->element['extension'];
		}
		$business = \Secretary\Application::company();
		$user = \Secretary\Joomla::getUser();

		$db = \Secretary\Database::getDBO();
		$query = $db->getQuery(true)->select("id AS value, title")
			->from($db->quoteName("#__secretary_folders"))
			->where($db->quoteName("business") . ' = ' . intval($business['id']))
			->where($db->quoteName("level") . " > 0");
		if (!empty($extension))
			$query->where($db->quoteName('extension') . "=" . $db->quote($extension));


		$db->setQuery($query);
		$folders = $db->loadObjectList();

		for ($i = 0; $i < count($folders ?? []); $i++) {
			if (
				$user->authorise('core.show', 'com_secretary.folder.' . $folders[$i]->value)
				|| $user->authorise('core.show.other', 'com_secretary.folder.' . $folders[$i]->value)
			) {
				$folders[$i]->title = JText::_($folders[$i]->title);
			} else {
				unset($folders[$i]);
			}
		}

		if (!empty($name))
			$this->name = $name;
		if (!empty($selected))
			$this->value = $selected;

		// Output
		return JHTML::_('select.genericlist', $folders, $this->name . '[]', 'class="inputbox" style="width:220px;" multiple="multiple" size="6"', 'value', 'title', $this->value);
	}
}