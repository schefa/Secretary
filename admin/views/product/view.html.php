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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class SecretaryViewProduct extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $canDo;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $jinput			= \Secretary\Joomla::getApplication()->input;
		$section		= $jinput->getCmd('view');
		$layout			= $jinput->getCmd('layout');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		
		$this->canDo	= \Secretary\Helpers\Access::getActions($section);

		// Permission
		$user = \Secretary\Joomla::getUser();
		$show = false;
		if( $layout == 'edit' && true === \Secretary\Helpers\Access::edit($section, $this->item->id, $this->item->created_by ) ) {
		    $show = true;
		} elseif( $layout != 'edit' && true === \Secretary\Helpers\Access::show($section, $this->item->id, $this->item->created_by) ) {
			$show = true;
		}
		
		if(!$show) {
		    echo '<div class="alert alert-danger">'. JText::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
		}

        if (isset($this->item->checked_out)) {
		    $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
        } else {
            $this->checkedOut = false;
        }

        JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH . '/models/fields');
        $this->itemtemplates = JFormHelper::loadFieldType('templates',false)->getList($this->item->template,'jform[template]','', array('products'));
        if(isset($this->item->template) && $this->item->template > 0)
            $this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template);
        
            
		$this->getJS();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		
		$isNew		= ($this->item->id == 0);

		// If not checked out, can save the item.
		if (!$this->checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('product.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('product.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		if (!$this->checkedOut && ($this->canDo->get('core.create'))){
			echo Secretary\Navigation::ToolbarItem('product.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create')) {
			echo Secretary\Navigation::ToolbarItem('product.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}
		
		echo Secretary\Navigation::ToolbarItem('product.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		
	}
	
	protected function getJS()
	{
		$document = JFactory::getDocument();
		
		$contacts = array();
		if(!empty($this->item->contacts) && ($this->_layout == 'edit')) 
		{
			if($c = json_decode($this->item->contacts)) {
				foreach($c AS $object)
				{
					if(!empty($object) && is_object($object)) {
						$subject 	= Secretary\Database::getQuery('subjects',$object->id,'id',"firstname,lastname");
						if(!empty($subject)) {
							$object->firstname = $subject->firstname;
							$object->lastname = $subject->lastname;
							$contacts[$object->id] =   $object;
						}
					}
				}
			} 
		}
		$this->suppliers_ids = array_keys($contacts);
		$this->contactsCounts = count($contacts);
		$contacts = json_encode( $contacts );
		
		$document = JFactory::getDocument();
		$document->addScriptDeclaration(" var featuresList = ". $contacts .";");
		$document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','product'));
	}
}
