<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

JFormHelper::addFieldPath(JPATH_SITE .'/administrator/components/com_secretary/models/fields');

class SecretaryViewTimes extends JViewLegacy
{
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
	    $user			= Secretary\Joomla::getUser();
	    $jinput			= Secretary\Joomla::getApplication()->input;
		$this->section	= $jinput->getCmd('section');
		$this->view		= $jinput->getCmd('view');
		$this->extension= $jinput->getCmd('extension');
		$this->canDo	= \Secretary\Helpers\Access::getActions($this->view);
		
		if ( !$this->canDo->get('core.show')) {
			throw new Exception( JText::_('JERROR_ALERTNOAUTHOR') , 500);
			return false;
		}
		
		$this->state		= $this->get('State');
		$this->pagination	= $this->get('Pagination');
		$this->projectExists = \Secretary\Database::getQuery('times', 'projects','extension','COUNT(*)','loadResult');
		 
		$this->categoryId	= $this->state->get('filter.category');
		
		$this->title = JText::_('COM_SECRETARY_TIME_MANAGEMENT');
		if(!empty($this->extension)) $this->title = JText::_('COM_SECRETARY_'. strtoupper($this->extension) );
		
		// Filter
		$this->listscaleOpts	= \Secretary\Helpers\Times::getListScaleOptions();
		$this->listscale		= $this->state->get('filter.scale');
		$this->listOffsetFuture	= $this->state->get('filter.future');
		$this->listOffsetPast	= $this->state->get('filter.past');
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
			throw new Exception(implode("\n", $errors));
		
		$model				= $this->getModel('times');
		$this->checkRep		= \Secretary\Helpers\Times::getLookAfterRepetitions('times', date('Y-m-d'));
		
		$this->getWeek();
		$this->getDay();
		$this->getMonth();
		$this->getYear();
		$this->categories	= $this->getCategories();
		$this->states		= $this->getStates();
		
		if($jinput->getCmd('ext') == 'tasks') {
			$this->tasks = $this->get('Tasks');
		}
		
		// Get data depending on task
		switch ($this->section)
		{

			case 'year' :
				if (!isset($this->year))
					$this->year	= date('Y');
			
				for ($x = 1; $x <= 12; $x++)
				    $this->months[$x] = \Secretary\HTML::_('times.monthtable', $x,$this->year );  
				
				break;
				
			case 'month' : $this->monthTable =  \Secretary\HTML::_('times.monthtable', $this->month,$this->year, true ); break;
			case 'week' : $this->data = $model->getWeekItems( $this->week,$this->year ); break;
			case 'day' : case 'list' : default : $this->items = $this->get('Items'); $this->section = 'list'; break;
		}
		
		$this->getJS();
		
		parent::display($tpl);
	}
	
	protected function getDay()
	{
		$jinput			= JFactory::getApplication()->input;
		$this->day		= $jinput->getInt('day');
		
		$filterDay		= $this->state->get('filter.day');
		if(empty($this->day) && !empty($filterDay))
			$this->day	= $filterDay;
			
		if(empty($this->day) && empty($filterDay))
			$this->day	= date("d");
	}
	
	protected function getWeek()
	{
		$jinput			= JFactory::getApplication()->input;
		$this->week		= $jinput->getInt('week');
		
		if(!empty($this->day) && !empty($this->month) && !empty($this->year))
			$this->week = \Secretary\Helpers\Times::getWeekOfDayMonth($this->day,$this->month,$this->year);
		
		$filterWeek		= $this->state->get('filter.week');
		if(empty($this->week) && !empty($filterWeek))
			$this->week	= $filterWeek;
			
		if(empty($this->week) && empty($filterWeek))
			$this->week	= date("W");
			
	}
	
	protected function getMonth()
	{
		$jinput			= JFactory::getApplication()->input;
		$this->month	= $jinput->getInt('month');
		
		$filterMonth	= $this->state->get('filter.month');
		if(empty($this->month) && !empty($filterMonth))
			$this->month	= $filterMonth;
		if(empty($this->month) && empty($filterMonth))
			$this->month	= date("n");
			
	}
	
  	protected function getYear()
	{
		$jinput			= JFactory::getApplication()->input;
		$this->year		= $jinput->getInt('year');
		if(empty($this->year))
			$this->year	= date("Y");
	}
	
	protected function addToolbarList()
	{
		echo '<div id="list-intervall" class="clearfix pull-right">
				<div class="list-intervall-1">
					<input type="number" min="0" step="1" name="filter_past" placeholder="'. JText::_("COM_SECRETARY_TIMES_VORSPANN") .'" value="'. $this->listOffsetPast .'"/> &#10094;
				</div>
				<div class="list-intervall-2">
					<select type="text" name="filter_scale" >'. JHtml::_("select.options", $this->listscaleOpts, 'value', 'text', $this->listscale, true) .'</select>
				</div>
				<div class="list-intervall-3">
					&#10095; <input type="number" min="0" step="1" name="filter_future" placeholder="'. JText::_('COM_SECRETARY_TIMES_ABSPANN') .'" value="'. $this->listOffsetFuture .'" />
				</div>
				<div class="list-intervall-4">
					<button type="submit" class="btn headline-sidebar-button">'. JText::_('COM_SECRETARY_APPLY') .'</button>
				</div>
			</div>';
	}
	
	protected function addToolbar()
	{
		$html	= array();
		
		if(!empty($this->categoryId))
			$category = Secretary\Database::getQuery('folders', $this->categoryId, 'id', 'alias');
		
		/*
		if ($this->canDo->get('core.create')) {
			if(isset($category->alias)) {
				$addtimeText = $category->alias;
			} else {
				$addtimeText = JText::_('COM_SECRETARY_TIME');
			}
			$html[] = Secretary\Navigation::ToolbarItem('time.add', JText::sprintf('COM_SECRETARY_NEW_ENTRY_TOOLBAR',$addtimeText), false, 'newentry');
		}
		*/

		if (($this->canDo->get('core.edit') || $this->canDo->get('core.edit.own')) && isset($this->items[0])) {
			$html[] = Secretary\Navigation::ToolbarItem('time.edit', 'COM_SECRETARY_TOOLBAR_EDIT', true, 'default', 'fa-pencil-square-o');
		}
		if ($this->canDo->get('core.edit') && isset($this->items[0]) && $this->canDo->get('core.delete')) {
			$html[] = Secretary\Navigation::ToolbarItem('times.delete', 'COM_SECRETARY_TOOLBAR_DELETE', true, 'default hidden-toolbar-btn', 'fa-trash');
		}

		// Add a batch button
		if ($this->canDo->get('core.edit') && $this->canDo->get('core.edit.state'))
		{
			$html[] = "<button data-toggle=\"modal\" data-target=\"#collapseModal\" class=\"btn btn-small  hidden-toolbar-btn\">
						<i class=\"icon-checkbox-partial\" title=\"".JText::_('COM_SECRETARY_TOOLBAR_BATCH')."\"></i>".JText::_('COM_SECRETARY_TOOLBAR_BATCH')."</button>";
		}
		
		echo implode("\n", $html);
	}
    
	protected function getSortFields()
	{
		return array(
			'a.id' => JText::_('JGRID_HEADING_ID'),
			'a.title' => JText::_('COM_SECRETARY_TITLE'),
			'a.startDate' => JText::_('COM_SECRETARY_TIMES_STARTDATE'),
			'a.endDate' => JText::_('COM_SECRETARY_TIMES_ENDDATE'),
			'a.ordering' => JText::_('COM_SECRETARY_ORDERING'),
			'a.desc' => JText::_('COM_SECRETARY_DESCRIPTION'),
		);
	}

	private function getStates()
	{
		$states = JFormHelper::loadFieldType('Secretarystatus', false)->getOptions( $this->view );
		return $states;
	}
	
	private function getCategories()
	{
		$categories = JFormHelper::loadFieldType('Categories', false)->getCategories( $this->view );
		return $categories;
	}
    
	protected function getJS()
	{
	    $document = JFactory::getDocument();
		if($this->section == 'list')
		{
			JHtml::_('script', 'system/core.js', false, true);
		
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
