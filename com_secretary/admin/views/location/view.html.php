<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryViewLocation extends \Joomla\CMS\MVC\View\HtmlView
{
    protected $business;
    protected $canDo;
    protected $extension;
    protected $form;
    protected $item;
    protected $layout;
    protected $state;
	protected $view;

	/**
	 * Method to display the View
	 * 
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $jinput			= Secretary\Joomla::getApplication()->input;
		$this->view		= $jinput->getCmd('view');
		$this->extension= $jinput->getCmd('extension');
		$this->layout	= $jinput->getCmd('layout');
		$this->business	= Secretary\Application::company();
		$this->title	= (!empty($this->extension)) ? \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION_'.strtoupper( $this->extension )) : \Joomla\CMS\Language\Text::_('COM_SECRETARY_LOCATION');
		
		$this->state	= $this->get('State');
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		// Data fields for the fields tab (see tmpl/edit.php); without this
		// printFields() is handed an empty list and saved fields never show.
		$this->datafields = \Secretary\Helpers\Items::makeFieldsReadyForList($this->item->fields);
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		
		// Permission 
		$check	= \Secretary\Helpers\Access::edit($this->view, $this->item->id, $this->item->created_by );
		
        if ( $this->layout == 'edit' && !$check )
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
        elseif ( $this->layout != 'edit' && false === \Secretary\Helpers\Access::show($this->view, $this->item->id,  $this->item->created_by) )
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
		    throw new Exception(implode("\n", $errors));
		}

        if (isset($this->item->checked_out))
		{
            $this->checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == Secretary\Joomla::getUser()->id);
        }
        else
		{
            $this->checkedOut = false;
        }
        
        $this->document->addScriptDeclaration(\Secretary\HTML::_('javascript.submitformbutton','location'));

		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tab');
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{

		$isNew		= ($this->item->id == 0);

		// If not checked out, can save the item.
		if (!$this->checkedOut && ($this->canDo->get('core.edit')||($this->canDo->get('core.create'))))
		{
			echo Secretary\Navigation::ToolbarItem('location.apply', 'COM_SECRETARY_TOOLBAR_APPLY', false, 'saveentry');
			echo Secretary\Navigation::ToolbarItem('location.save', 'COM_SECRETARY_TOOLBAR_SAVE', false, 'saveentry');
		}
		
        if (!$this->checkedOut && ($this->canDo->get('core.create')))
		{
			echo Secretary\Navigation::ToolbarItem('location.save2new', 'COM_SECRETARY_TOOLBAR_SAVE_AND_NEW', false);
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $this->canDo->get('core.create'))
		{
			echo Secretary\Navigation::ToolbarItem('location.save2copy', 'COM_SECRETARY_TOOLBAR_SAVE_AS_COPY', false);
		}
		
        if (empty($this->item->id))
		{
			echo Secretary\Navigation::ToolbarItem('location.cancel', 'COM_SECRETARY_TOOLBAR_CANCEL', false);
		}
		else
		{
			echo Secretary\Navigation::ToolbarItem('location.cancel', 'COM_SECRETARY_TOOLBAR_CLOSE', false);
		}

	}
	
}
