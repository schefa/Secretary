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
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JFormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewMarkets extends JViewLegacy
{
    protected $business;
    protected $canDo;
    protected $categoryId;
    protected $chartData;
    protected $items;
    protected $pagination;
    protected $selectedColumns;
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
		
		$this->categories	= JFormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= JFormHelper::loadFieldType('SecretaryStatus', false)->getOptions( $this->view );
		$this->getJS();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function getJS()
	{
		$document = JFactory::getDocument();
		$document->addScriptDeclaration("
		jQuery(document).ready(function($){
			$('#watchlist_select').change(function(){
				var value = $(this).val();
				$('#watchlist_id').val(value);
				$('form').get(0).setAttribute('action', 'index.php?option=com_secretary&view=markets&catid='+value); 
				this.form.submit();
			});
		});
		");
	}
    
}
