<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldAccountssystem extends JFormFieldList
{
	
	protected $type = 'accountssystem';
	
	public function getOptions( )
	{
	    
	    $id = \Secretary\Joomla::getApplication()->input->getInt('id');
		$html = array();
		
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        
        $query->select($db->qn(array("id","title","parent_id","level","type","nr")));
        $query->from($db->qn('#__secretary_accounts_system'));
        $query->where($db->qn('id').'!='.intval($id));
        $query->order('id ASC');
				
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		$items = Secretary\Utilities::reorderTree($items, 'parent_id', 'id');
		
		// Make list
		$html[] = JHtml::_('select.option', 0, JText::_('COM_SECRETARY_NONE') );
		foreach($items as $item) {
			if(isset($item->step))
				$title = str_repeat( "&mdash;", $item->level) . $item->nr. ' - '.$item->title;
			else
				$title = $item->nr. ' - '.$item->title;
			$html[] = JHtml::_('select.option', $item->id,  $title );
		}
		
		return $html;
		
	}
	
	public function getList( $default, $name = 'jform[accountssystem]' )
	{
		$html = $this->getOptions();
		$result = '<select name="'.$name.'" class="form-control inputbox">'. JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
}