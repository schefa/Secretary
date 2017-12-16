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

class JFormFieldSecretarystatus extends JFormFieldList
{
	
	protected $type = 'secretarystatus';

	public function getOptions( $extension = 'root' )
	{
        $db = JFactory::getDbo();
		
		if (!empty($this->element['extension'])) {
			$extension = (string) $this->element['extension'];
		} elseif ($mod	= JFactory::getApplication()->input->getCmd('module')) {
			$extension =  $mod;
		} else {
			$extension = $extension;	
		}
		
		$html = array();
		
        $query = $db->getQuery(true)
				->select("id,title")
				->from($db->quoteName('#__secretary_status'))
				->where($db->quoteName('extension').' = '. $db->quote($extension))
				->order('ordering ASC, id ASC');
				
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		foreach($items as $message) {
			$html[] = JHtml::_('select.option', $message->id, JText::_($message->title) );
		}
		
		return $html;
		
	}
	
	public function getObject( $id )
	{
		
		$items = new JObject();
		
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
        		->select("*")
        		->from($db->quoteName('#__secretary_status'))
        		->where($db->quoteName('id').' = '. $db->escape($id));
				
		$db->setQuery($query);
		$items = $db->loadAssoc();
		
		return $items;
		
	}
}