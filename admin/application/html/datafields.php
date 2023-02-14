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

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

use JText;
use JHtml;

// No direct access
defined('_JEXEC') or die;

class Datafields
{

	public static function item()
	{
		return '
<div class="field-item" style="display:none;">
	<div class="field-item-title secretary-tooltip">
        <span class="tooltip-toggle">##description##</span>
		<span class="pull-left">##hard##</span>
		<input id="jform_fields_##counter##_id" type="hidden" class="form-control" name="jform[fields][##counter##][id]" value="##id##" />
		<input id="jform_fields_##counter##_hard" type="hidden" class="form-control" name="jform[fields][##counter##][hard]" value="##hard##" />
		<input id="jform_fields_##counter##_title" type="text" value="##title##" class="form-control" name="jform[fields][##counter##][title]" placeholder="' . JText::_('COM_SECRETARY_FIELD_KEY') . '" />
    </div>
	<div class="field-item-values">##values##</div>
    <div class="btn btn-default field-remove"><i class="fa fa-remove"></i></div>
</div>';
	}

	public static function listOptions($extension, $unsets = array())
	{
		// Liste aller Standard Datenfelder
		$allFields		= \Secretary\Helpers\Items::getFields($extension, $unsets);
		$fieldOptions	= array();

		foreach ($allFields as $field) {
			$fieldOptions[] = JHtml::_('select.option', $field->id, JText::_($field->title));
		}

		if (is_array($extension)) $extension = implode(",", $extension);

		$html = '<div class="select-arrow"><select id="getfields" class="form-control inputbox" data-ext="' . $extension . '">';
		$html .= JHtml::_('select.options', $fieldOptions, 'value', 'text');
		$html .= '</select></div>';

		return $html;
	}
}
