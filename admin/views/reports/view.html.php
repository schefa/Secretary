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

class SecretaryViewReports extends JViewLegacy
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
		if (count($errors = $this->get('Errors'))) {
			throw new Exception(implode("\n", $errors));
		}
		 
		$this->document->addScript(JURI::root().'media/secretary/js/secretary.charts.js?v='.SECRETARY_VERSION);
		$this->getJS();
		
		parent::display($tpl);
	}
	
	public function getJS() {
	    $content = array();
	    $content[] = "jQuery(document).ready(function($) {";
	    $content[] = "$('.btn-toolbar-charts select').live('change',function() {";
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
