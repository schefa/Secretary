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

class JFormFieldGender extends JFormFieldList
{
	
	protected $type = 'gender';
	private $_list = array();

	public function getOptions( $key = false )
	{ 
	
		$options = array();
		
		if(empty($this->_list)) {
    		$db = \Secretary\Database::getDBO();
    		$query = $db->getQuery(true);
    		$query->select('*')
    			->from($db->qn('#__secretary_fields'))
    			->where($db->qn('hard').'='.$db->quote('anrede'))
    			->where($db->qn('extension')."=".$db->quote('system'))
    			->order('id');
    		$db->setQuery($query);
    		$this->_list = $db->loadObject(); 
		} 
		
		$object = $this->_list;
		if( $fieldvalues = json_decode($object->values, true) ) {
			
			foreach($fieldvalues AS $pos => $val){
				$fieldvalues[$pos] = JText::_($val);
				if(strlen($val) >= 1) {
					$val = JText::_($val);
				} else {
					$val = JText::_('COM_SECRETARY_NONE');
				}
				$options[$pos] = JHTML::_( 'select.option',$pos,$val);
			}
			if($key === false) {
				return $options;
			} elseif(isset($options[$key])) {
				return JText::_($fieldvalues[$key]);
			}
				
		}
		return false;
	}
	
	public function getList( $default, $name = 'jform[subject][]', $id = 'jform_subject_gender'  )
	{               
		$options = $this->getOptions();
		if($options) {
            return '<select name="'.$name.'" id="'.$id.'" class="form-control inputbox">'. JHtml::_('select.options', $options, 'value', 'text', $default) . '</select>';
		}
		return false ;
	}
}