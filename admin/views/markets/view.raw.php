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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JFormHelper::addFieldPath(JPATH_SITE .'/administrator/components/com_secretary/models/fields');

class SecretaryViewMarkets extends JViewLegacy
{
	protected $categoryId;
	protected $canDo;
	protected $items;
	protected $pagination;
	protected $state;
	protected $chartData;

	/**
	 * Method to display the View
	 * 
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $jinput				= Secretary\Joomla::getApplication()->input;
	    $this->business	    = Secretary\Application::company();
	    $this->view			= $jinput->getCmd('view', 'markets');
	    $this->extension	= $jinput->getCmd('extension');
	    $this->layout       = $jinput->getCmd('layout');
	    $this->categoryId	= $jinput->getInt('catid');
	    $this->title		= JText::_('COM_SECRETARY_MARKETS');
	    
	    $this->state		    = $this->get('State');
	    $this->items		    = $this->get('Items');
	    $this->pagination	    = $this->get('Pagination');
	    $this->chartData        = $this->get('ChartData');
	    $this->selectedColumns  = $this->get('Columns');
	    $this->canDo		    = \Secretary\Helpers\Access::getActions($this->view);
	    
	    if ( !$this->canDo->get('core.show')) {
	        throw new Exception( JText::_('JERROR_ALERTNOAUTHOR') , 500);
	        return false;
	    } elseif (count($errors = $this->get('Errors'))) {
	        throw new Exception(implode("\n", $errors));
	        return false;
	    }

	    echo $this->loadTemplate($this->layout);
		
		return;
	}
}
