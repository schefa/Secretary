<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldAccounts extends JFormFieldList
{
	
	protected $type = 'accounts';
	
	public function getOptions( )
	{
		$id = \Secretary\Joomla::getApplication()->input->getInt('id');
		$html = array();
		
        $db = \Secretary\Database::getDBO();
        $query = $db->getQuery(true);
        
        $query->select("s.nr,s.title");
        $query->from($db->qn('#__secretary_accounts_system','s'));
        $query->select('a.id');
        $query->join('RIGHT',$db->qn('#__secretary_accounts','a').' ON a.kid = s.id');
        $query->where('a.year = '.date('Y'));
        $query->order('s.id ASC');
				
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
	//	$items = Secretary\Utilities::reorderTree($items, 'kid', 'id');
		
		foreach($items as $message) {
			$html[] = JHtml::_('select.option', $message->id,  $message->nr. ' - '.$message->title );
		}
		
		return $html;
	}
	
	public function getList( $default, $name = 'jform[accounts]' )
	{
		$html = $this->getOptions();
		$result = '<select name="'.$name.'" class="form-control inputbox">'. JHtml::_('select.options', $html, 'value', 'text', $default) . '</select>';
		return $result;
	}
}