<?php
 
defined('_JEXEC') or die;


\Joomla\CMS\Form\FormHelper::addFieldPath(SECRETARY_ADMIN_PATH.'/models/fields');

class SecretaryViewTimes extends \Joomla\CMS\MVC\View\HtmlView
{
    protected $app;
	protected $items;
	protected $pagination;
	protected $state;
	protected $week;
	protected $month;
	protected $year;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
	    $this->app	    = Secretary\Joomla::getApplication();
		$this->section	= $this->app->input->getCmd('section');
		$this->view		= $this->app->input->getCmd('view');
		$this->extension= $this->app->input->getCmd('extension');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		
		if ( !$this->canDo->get('core.show'))
		{
		    echo '<div class="alert alert-danger">'. \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR').'</div>'; return false;
		}
		
		$this->state		= $this->get('State');
		$this->pagination	= $this->get('Pagination'); 
		$this->projectExists = \Secretary\Database::getQuery('times', 'projects','extension','COUNT(*)','loadResult');
		
		$this->categoryId	= $this->state->get('filter.category');
		
		$this->title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIME_MANAGEMENT');
		
        if (!empty($this->extension))
		{
			$this->title = \Joomla\CMS\Language\Text::_('COM_SECRETARY_'. strtoupper($this->extension) );
		}
		
		// Filter
		$this->listscaleOpts	= \Secretary\Helpers\Times::getListScaleOptions();
		$this->listscale		= $this->state->get('filter.scale');
		$this->listOffsetFuture	= $this->state->get('filter.future');
		$this->listOffsetPast	= $this->state->get('filter.past');
		
		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			throw new Exception(implode("\n", $errors));
		}
		
		$model				= $this->getModel('times');
		$this->checkRep		= \Secretary\Helpers\Times::getLookAfterRepetitions('times', date('Y-m-d'));
		
		$this->getWeek();
		$this->getDay();
		$this->getMonth();
		$this->getYear();
		$this->categories	= \Joomla\CMS\Form\FormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		$this->states		= \Joomla\CMS\Form\FormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		$this->projects     = \Secretary\Database::getQuery('times', 'projects', 'extension', 'id,title', 'loadObjectList'); 
		
		
		$uhrzeitJetzt = ($this->listscale == 'hours') ? " h:i:s":"";
		$startDate = date($this->year .'-'. $this->month.'-'.$this->day .$uhrzeitJetzt);
		
		// Convert to Timestamps
		$this->today			= strtotime( $startDate );
		$this->vorspannSecs		= strtotime("-".$this->listOffsetPast." ". $this->listscale, $this->today);
		$this->abpannSecs		= strtotime("+".$this->listOffsetFuture ." ". $this->listscale, $this->today);
		
		// Intervall gibt die Breite / Länge der Div Box an, aus der prozentuell der Abstand berechnet wird
		$this->intervall	= $this->abpannSecs - $this->vorspannSecs;
		
		if ($this->app->input->getCmd('ext') == 'tasks')
		{
			$this->tasks = $this->get('Tasks');
		}
		
		// Get data depending on task
		switch ($this->section)
		{
			case 'year' :
				if (!isset($this->year))
				{
					$this->year	= date('Y');
				}

				for ($x = 1; $x <= 12; $x++)
				{
				    $this->months[$x] = \Secretary\HTML::_('times.monthtable', $x,$this->year );
				}
				
				break;
				
			case 'month' : $this->monthTable =  \Secretary\HTML::_('times.monthtable', $this->month,$this->year, true ); break;
			case 'week' : $this->data = $model->getWeekItems( $this->week,$this->year ); break;
			case 'day' : case 'list' : default : $this->items = $this->get('Items'); $this->section = 'list'; break;
		} 
		
		$this->getJS();
		
		parent::display($tpl);
	}
	
	/**
	 * Method to assign the selected day as a global variable
	 */
	protected function getDay()
	{ 
		$this->day		= $this->app->input->getInt('day');
		
		$filterDay		= $this->state->get('filter.day');
		
        if (empty($this->day) && !empty($filterDay))
		{
			$this->day	= $filterDay;
		}

		if (empty($this->day) && empty($filterDay))
		{
			$this->day	= date("d");
		}
	}
	
	/**
	 * Method to assign the selected week as a global variable
	 */
	protected function getWeek()
	{ 
		$this->week		= $this->app->input->getInt('week');
		
		if (!empty($this->day) && !empty($this->month) && !empty($this->year))
		{
			$this->week = \Secretary\Helpers\Times::getWeekOfDayMonth($this->day,$this->month,$this->year);
		}

		$filterWeek		= $this->state->get('filter.week');
		
        if (empty($this->week) && !empty($filterWeek))
		{
			$this->week	= $filterWeek;
		}

		if (empty($this->week) && empty($filterWeek))
		{
			$this->week	= date("W");
		}
			
	}
	
	/**
	 * Method to assign the selected month as a global variable
	 */
	protected function getMonth()
	{ 
		$this->month	= $this->app->input->getInt('month');
		
		$filterMonth	= $this->state->get('filter.month');
		
        if (empty($this->month) && !empty($filterMonth))
		{
			$this->month	= $filterMonth;
		}
		
        if (empty($this->month) && empty($filterMonth))
		{
			$this->month	= date("n");
		}
			
	}
	
	/**
	 * Method to assign the current year as a global variable
	 */
  	protected function getYear()
	{
		$this->year		= $this->app->input->getInt('year');
		
        if (empty($this->year))
		{
			$this->year	= date("Y");
		}
	}
	
	/**
	 * Method to create the Toolbar for list view
	 */
	protected function addToolbarList()
	{
		echo '<div id="list-intervall" class="clearfix pull-right">
				<div class="list-intervall-1">
					<input type="number" min="0" step="1" name="filter_past" placeholder="'. \Joomla\CMS\Language\Text::_("COM_SECRETARY_TIMES_VORSPANN") .'" value="'. $this->listOffsetPast .'"/> &#10094;
				</div>
				<div class="list-intervall-2">
					<select type="text" name="filter_scale" >'. \Joomla\CMS\HTML\HTMLHelper::_("select.options", $this->listscaleOpts, 'value', 'text', $this->listscale, true) .'</select>
				</div>
				<div class="list-intervall-3">
					&#10095; <input type="number" min="0" step="1" name="filter_future" placeholder="'. \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_ABSPANN') .'" value="'. $this->listOffsetFuture .'" />
				</div>
				<div class="list-intervall-4">
					<button type="submit" class="btn headline-sidebar-button">'. \Joomla\CMS\Language\Text::_('COM_SECRETARY_APPLY') .'</button>
				</div>
			</div>';
	}
	
	/**
	 * Method to create the Toolbar
	 */
	protected function addToolbar()
	{
		$html	= array();
		
		if (!empty($this->categoryId))
		{
			$category = Secretary\Database::getQuery('folders', $this->categoryId, 'id', 'alias'); 
		}
		
		if ($this->canDo->get('core.edit') && isset($this->items[0]) && $this->canDo->get('core.delete'))
		{
			$html[] = Secretary\Navigation::ToolbarItem('times.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}
		
		if ($this->canDo->get('core.edit') && $this->canDo->get('core.edit.state'))
		{
			$html[] = "<button data-joomla-dialog='{\"popupType\": \"inline\", \"src\": \"#secretary-batch-dialog\"}' type=\"button\" class=\"btn btn-small  hidden-toolbar-btn\">
						<i class=\"icon-checkbox-partial\" title=\"".\Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH')."\"></i>".\Joomla\CMS\Language\Text::_('COM_SECRETARY_TOOLBAR_BATCH')."</button>";
		}
		
		echo implode("\n", $html);
	}
    
	protected function getSortFields()
	{
		return array(
			'time_title' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TITLE'),
			'startDate' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_STARTDATE'),
			'endDate' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_TIMES_ENDDATE'),
			'ordering' => \Joomla\CMS\Language\Text::_('COM_SECRETARY_ORDERING'),
		);
	}  
	
	protected function getJS()
	{
		$document = \Joomla\CMS\Factory::getDocument();
		
        if ($this->section == 'list')
		{
			\Joomla\CMS\HTML\HTMLHelper::_('script', 'system/core.js', ['relative' => true]);
		
			$document->addScriptDeclaration("
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
			
                    $document->addScript(SECRETARY_MEDIA_PATH.'/js/secretary.times.js?v='.SECRETARY_VERSION); 
		}
	}
	
}
