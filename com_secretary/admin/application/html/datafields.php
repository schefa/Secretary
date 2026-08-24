<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary\HTML;

defined('_JEXEC') or die;

require_once SECRETARY_ADMIN_PATH . '/application/HTML.php';

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
		<input id="jform_fields_##counter##_title" type="text" value="##title##" class="form-control" name="jform[fields][##counter##][title]" placeholder="' . \Joomla\CMS\Language\Text::_('COM_SECRETARY_FIELD_KEY') . '" />
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

		foreach ($allFields as $field)
		{
			$fieldOptions[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $field->id, \Joomla\CMS\Language\Text::_($field->title));
		}

		if (is_array($extension))
		{
			$extension = implode(",", $extension);
		}

		$html = '<div class="select-arrow"><select id="getfields" class="form-control inputbox" data-ext="' . $extension . '">';
		$html .= \Joomla\CMS\HTML\HTMLHelper::_('select.options', $fieldOptions, 'value', 'text');
		$html .= '</select></div>';

		return $html;
	}
}
