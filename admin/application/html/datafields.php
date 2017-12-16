<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary\HTML;

require_once JPATH_ADMINISTRATOR .'/components/com_secretary/application/HTML.php'; 

use JText;
use JHtml;
 
// No direct access
defined('_JEXEC') or die;

class Datafields
{
	
	public static function item ()
	{
		return '
<div class="field-item" style="display:none;">
	<div class="field-item-title secretary-tooltip">
        <span class="tooltip-toggle">##description##</span>
		<span class="pull-left">##hard##</span>
		<input id="jform_fields_##counter##_id" type="hidden" class="form-control" name="jform[fields][##counter##][id]" value="##id##" />
		<input id="jform_fields_##counter##_hard" type="hidden" class="form-control" name="jform[fields][##counter##][hard]" value="##hard##" />
		<input id="jform_fields_##counter##_title" type="text" value="##title##" class="form-control" name="jform[fields][##counter##][title]" placeholder="'. JText::_('COM_SECRETARY_FIELD_KEY') .'" />
    </div>
	<div class="field-item-values">##values##</div>
    <div class="btn btn-default field-remove"><i class="fa fa-remove"></i></div>
</div>';
	}
	
	public static function listOptions ($extension, $unsets = array() )
	{
		// Liste aller Standard Datenfelder
		$allFields		= \Secretary\Helpers\Items::getFields($extension, $unsets);
		$fieldOptions	= array();
		
		foreach($allFields as $field) {
			$fieldOptions[] = JHtml::_('select.option', $field->id, JText::_($field->title));
		}
		
		if(is_array($extension)) $extension = implode(",",$extension);
		
		$html = '<div class="select-arrow"><select id="getfields" class="form-control inputbox" data-ext="'. $extension .'">';
		$html .= JHtml::_('select.options', $fieldOptions, 'value', 'text');
        $html .= '</select></div>';
			
		return $html;
	}
	
}
