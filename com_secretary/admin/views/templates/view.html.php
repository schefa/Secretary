<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewTemplates extends \Joomla\CMS\MVC\View\HtmlView
{
    protected $categoryId;
    protected $categories;
    protected $canDo;
    protected $extension;
    protected $items;
    protected $pagination;
    protected $state;
    protected $states;
	protected $title;
	protected $view;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app				= \Secretary\Joomla::getApplication();
		$this->view			= $app->input->getCmd('view', 'templates');
		$this->extension	= $app->input->getCmd('extension');
		$this->categoryId	= $app->input->getInt('catid',0);
		
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		
		$this->title		= \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATES');
		
        if (!empty($this->extension))
		{
			$this->title .= " - ". \Joomla\CMS\Language\Text::_('COM_SECRETARY_'.strtoupper($this->extension));
		}
		
		// Permission check
		if ( !$this->canDo->get('core.show'))
		{
			$app->enqueueMessage( \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 'error');
			
            return false;
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
		    throw new Exception(implode("\n", $errors));
		}
        
		$this->categories	= \Joomla\CMS\Form\FormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= \Joomla\CMS\Form\FormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		$this->getJS();
		
		parent::display($tpl);
	} 
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html = array();

		if ($this->canDo->get('core.create'))
		{
			$addEventText = \Joomla\CMS\Language\Text::_('COM_SECRETARY_TEMPLATE');
			
            if (isset($this->extension))
			{
				$addEventText .= ' '.\Joomla\CMS\Language\Text::_('COM_SECRETARY_'.$this->extension);
			}
			$html[] = Secretary\Navigation::ToolbarItem('template.add', \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry');
		}

		// Stapel
		if ($this->canDo->get('core.edit'))
		{
		    $html[] = '<button data-joomla-dialog=\'{"popupType": "inline", "src": "#secretary-batch-dialog"}\' type="button" class="btn btn-small">
						<span class="fa fa-database" title=\"'.\Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH').'\"></span>'.
		    \Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH').'</button>';
		}
		
		
		if ($this->canDo->get('core.delete') && isset($this->items[0]))
		{
			$html[] = Secretary\Navigation::ToolbarItem('templates.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}
		
		if (!empty($html))
		{
			array_unshift($html, '<div class="select-arrow-toolbar-next">&#10095;</div>');
		}
		
		echo implode("\n", $html);
	}
	
	protected function getSortFields()
	{
		return array(
			'a.id' => \Joomla\CMS\Language\Text::_('JGRID_HEADING_ID'),
			'a.title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_NAME'),
			'a.desc' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_DESCRIPTION'),
			'a.business' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_BUSINESS'),
			'category' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_CATEGORY'),
			'a.state' => \Joomla\CMS\Language\Text::_('JSTATUS'),
			'a.language' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_LANGUAGE'),
		);
	}

	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		$document->addScriptDeclaration("
		jQuery(document).ready(function($){
			$('#select_category').change(function(){
				var value = $(this).val();
				$('#products_catID').val(value);
				$('form').get(0).setAttribute('action', 'index.php?option=com_secretary&view=templates&catid='+value); 
				this.form.submit();
			});
		});

		Joomla.orderTable = function() {
				table = document.getElementById('sortTable');
				direction = document.getElementById('directionTable');
				order = table.options[table.selectedIndex].value;
				if (order != '". $this->state->get('list.ordering') ."') {
					dirn = 'asc';
				} else {
					dirn = direction.options[direction.selectedIndex].value;
				}
				Joomla.tableOrdering(order, dirn, '');
			}
		");
	}
    
}
