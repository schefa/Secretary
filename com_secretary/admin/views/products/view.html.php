<?php
 
defined('_JEXEC') or die;

\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewProducts extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $items;
	protected $pagination;
	protected $documents;
	protected $state;
	protected $view;
	protected $canDo;
	protected $categoryId;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $app				= \Secretary\Joomla::getApplication();
		$this->state		= $this->get('State');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->documents	= \Secretary\Helpers\Folders::getList("documents");
		
	    $this->selectedColumns = Secretary\Session::getColumns('products_columns',\Secretary\Helpers\Products::$selectedColumns);
        $this->acceptedColumns = array_filter($this->selectedColumns, function($val){ 
            return ($val === true); });
		
		$this->categoryId	= $app->input->getInt('catid', 0);
		$this->view			= $app->input->getCmd('view');
		$this->canDo		= \Secretary\Helpers\Access::getActions($this->view);
		
		if ( !$this->canDo->get('core.show'))
		{
		    $app->enqueueMessage( \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 'error');
		    
            return false;
		}
        elseif (count(($errors = $this->get('Errors')) ?? []))
		{
		    throw new Exception(implode("\n", $errors));
		    
            return false;
		}
        
		$this->categories	= \Joomla\CMS\Form\FormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= \Joomla\CMS\Form\FormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		$this->templates	= \Joomla\CMS\Form\FormHelper::loadFieldType('Templates', false)->getOptions(array( $this->view ));
		$this->years        = $this->getProductYears();
		
		$this->getJS();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to get valid years for filter
	 * 
	 * @return mixed[]
	 */
	protected function getProductYears() { 
	    
	    $db = \Secretary\Database::getDBO();
	    $db->setQuery('SELECT DISTINCT year FROM '.$db->qn('#__secretary_products'));
	    $years = $db->loadColumn();
	    $years = array_unique(array_merge(array(date('Y')),$years), SORT_REGULAR);
	    
	    $result = array();
	    
        foreach ($years as $year)
		{
	        $result[] =  \Joomla\CMS\HTML\HTMLHelper::_('select.option', $year, $year );
	    }
	    
        return $result;
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html	= array();
	
		if ($this->canDo->get('core.create'))
		{
			$addEventText = \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT');
			$html[] = Secretary\Navigation::ToolbarItem('product.add', \Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addEventText), false, 'newentry');
			
            if (isset($this->items[0]))
			{
    			$html[] = Secretary\Navigation::ToolbarItem('products.buy','COM_SECRETARY_BUY', true, 'default','fa-sign-in');
    			$html[] = Secretary\Navigation::ToolbarItem('products.sell','COM_SECRETARY_SELL', true, 'default','fa-sign-out');
    		}
		}

		if (isset($this->items[0]) && $this->canDo->get('core.edit'))
		{
			$title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH');
			$html[] = "<button data-joomla-dialog='{\"popupType\": \"inline\", \"src\": \"#secretary-batch-dialog\"}' type=\"button\" class=\"btn btn-small\">
						<i class=\"fa fa-database\" title=\"".$title."\"></i>&nbsp;".$title."</button>";
		}

		if ($this->canDo->get('core.edit') && isset($this->items[0]) && $this->canDo->get('core.delete'))
		{
			$html[] = Secretary\Navigation::ToolbarItem('products.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}

		if ($this->canDo->get('core.edit') && isset($this->items[0]->checked_out))
		{
			$html[] = Secretary\Navigation::ToolbarItem('products.checkin', 'COM_SECRETARY_TOOLBAR_CHECKIN', true, 'default hidden-toolbar-btn', 'fa-refresh');
		}
		
		if (!empty($html))
		{
			array_unshift($html, '<div class="select-arrow-toolbar-next">&#10095;</div>');
		}
		
		echo implode("\n", $html);
	} 
    
	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		
		$document->addScriptDeclaration("
		
			jQuery(document).ready(function($){
				$('#products_category').change(function(){
					var value = $(this).val();
					$('#products_catID').val(value);
					$('form').get(0).setAttribute('action', 'index.php?option=com_secretary&view=products&catid='+value); 
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
    
	protected function getSortFields()
	{
	    $fields = array(
			'a.title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_TITLE'),
		);
	    
        foreach ($this->acceptedColumns as $key => $t)
		{
	        $fields['a.'.$key] = \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_'.$key);
	    }
		
        return $fields;
	}
	
}
