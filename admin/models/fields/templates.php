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

class JFormFieldTemplates extends JFormFieldList
{
	
	protected $type = 'templates';
	protected static $_items = array();
	
	public function getOptions( $only = array() )
	{ 
	
		$html = array();
		
		if(empty(self::$_items)) {
				
			$extension = (string) $this->element['extension'];
	 		$business	= Secretary\Application::company();
		
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)
					->select("id,title")
					->from($db->quoteName('#__secretary_templates'))
					->where($db->qn('business').' = '. intval($business['id']));
			
			if(!empty($only))
			     $query->where('extension LIKE ('.$db->quote(implode('","',$only)).')');
			
			$query->order('id ASC');
					
			if(!empty($extension)) $query->where($db->quoteName('extension').'='. $db->quote( $extension ));
			try {
				$db->setQuery($query);
				self::$_items = $db->loadObjectList();
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
		
		$items = self::$_items;
		
		$html[] = JHtml::_('select.option', 0, JText::_("COM_SECRETARY_NONE") );
		foreach($items as $message) {
			$html[] = JHtml::_('select.option', $message->id, JText::_($message->title) );
		}
		
		return $html;
		
	}
	
	public function getList( $default, $name = 'jform[fields][template]', $class = "",$only = array())
	{
		$html = $this->getOptions($only);
		$result =	'<select name="'.$name.'" class="form-control inputbox '.$class.'">'. JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
	
}