<?php
 
defined('_JEXEC') or die;


class SecretaryViewReports extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $business;
	protected $state;
	protected $params;
	protected $contacts;
	protected $documents;
	protected $documents_total = array();
	protected $zeitraumoptions;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app			= \Secretary\Joomla::getApplication();
		$this->view		= $app->input->getCmd('view');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		$this->business	= \Secretary\Application::company(); 
		
		// Status
		$model                    = $this->getModel('Reports');
		$this->state              = $this->get('State');
		$this->zeitraumoptions    = $model->zeitraumoptions;
		$this->docsStates         = $model->getStates('documents');
		$this->contStates         = $model->getStates('subjects');
		$this->prodStates         = $model->getStates('products');
		
		// Data
		$incomesObj               = $model->getStats($this->business, json_decode($this->business['guv1']));
		$costsObj                 = $model->getStats($this->business, json_decode($this->business['guv2']));
		$this->documents          = $model->rebuildDocumentsItems($incomesObj,$costsObj);
		$this->documents_total    = $model->total;
		$this->contacts           = $model->getContactsGrowth($this->business);
		$this->products           = $model->getProductsGrowth($this->business);
		
		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
		}
		 
		$this->document->addScript(\Joomla\CMS\Uri\Uri::root().'media/secretary/js/secretary.charts.js?v='.SECRETARY_VERSION);
		$this->getJS();

		\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.tab');

		parent::display($tpl);
	}
	
	public function getJS() {
	    $content = array();
	    $content[] = "jQuery(document).ready(function($) {";
	    $content[] = "$(document).on('change', '.btn-toolbar-charts select', function() {";
	    $content[] = "var parent = $(this).closest('.tab-pane');";
	    $content[] = "var url = 'index.php?option=com_secretary&view=reports&layout=default_'+parent.attr('id')+'&format=raw';";
	    $content[] = "var params = $('form').serialize();parent.empty();";
	    $content[] = "$.post(url,params).done(function(data){parent.html(data);";
	    $content[] = "});";
	    $content[] = "});";
	    $content[] = "});";
	    
	    $this->document->addScriptDeclaration(implode("",$content));
	}
}
