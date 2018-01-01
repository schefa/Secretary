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
		
        $db = \Secretary\Database::getDBO();
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