<?php
/**
 * @version     3.0.0
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

class JFormFieldSecretarySections extends JFormFieldList
{
	
	protected $type = 'secretarysections';
	
	public function getIcons() {
	  return $result = array( 
		  'system' =>  ('---'),
		  'accountings' => '<i class="fa fa-book"></i>',
		  'businesses' => '<i class="fa fa-home"></i>',
		  'folders' => '<i class="fa fa-folders-o"></i>',
		  'documents' => '<i class="fa fa-file-o"></i>',
		  'subjects' => '<i class="fa fa-users"></i>',
		  'products' => '<i class="fa fa-shopping-cart"></i>',
		  'messages' => '<i class="fa fa-comment-o"></i>',
		  'newsletters' => '<i class="fa fa-newspaper-o"></i>',
		  'locations' => '<i class="fa fa-cube"></i>',
		  'templates' => '<i class="fa fa-print"></i>',
		  'times' => '<i class="fa fa-calendar"></i>',
	  ); 
	}
	
	public function getModulesArray() {
	  return $result = array( 
		  'system' => ('---'),
		  'accountings' => JText::_('COM_SECRETARY_ACCOUNTING'),
		  'businesses' => JText::_('COM_SECRETARY_BUSINESSES'),
		  'folders' => JText::_('COM_SECRETARY_FOLDERS'),
		  'documents' => JText::_('COM_SECRETARY_DOCUMENTS'),
		  'subjects' => JText::_('COM_SECRETARY_SUBJECTS'),
		  'products' => JText::_('COM_SECRETARY_PRODUCTS'),
		  'messages' => JText::_('COM_SECRETARY_MESSAGES'),
		  'newsletters' => JText::_('COM_SECRETARY_NEWSLETTER'),
		  'locations' => JText::_('COM_SECRETARY_LOCATIONS'),
		  'templates' => JText::_('COM_SECRETARY_TEMPLATES'),
		  'times' => JText::_('COM_SECRETARY_TIME_MANAGEMENT'),
	  ); 
	}
	 
	public function getResult( $key )
	{
		$result = $this->getModulesArray();
		return $result[$key];
	}
	
	public function getOptions()
	{
		
		$result = $this->getModulesArray(); 
		
		$unsets = explode(',',$this->element['unset']);
		
		foreach($unsets as $unset) {
			$unset = (string) $unset;
			if(!empty($unset) && isset($result[$unset])) {
				unset($result[$unset]);
			}
		}
		
		return $result;
	}
}