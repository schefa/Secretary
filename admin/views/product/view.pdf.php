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

class SecretaryViewProduct extends JViewLegacy
{
	protected $item;
	protected $defaultTemplate;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = 'pdf')
	{
		$jinput			= JFactory::getApplication()->input;
		$section		= $jinput->getCmd('view');
		$this->item	    = $this->get('Item');

		// Permission
		$user = JFactory::getUser();
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id, $this->item->created_by );
		if( $this->_layout == 'edit' && !$check ) {
			throw new Exception( JText::_('JERROR_ALERTNOAUTHOR'), 500);
			return false;
		} elseif( false === \Secretary\Helpers\Access::show($section, $this->item->id,  $this->item->created_by) ) {
		    throw new Exception( JText::_('JERROR_ALERTNOAUTHOR'), 500);
			return false;
		}
		
		$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template);
		
		if(empty($this->defaultTemplate)) {
			throw new Exception( JText::_('COM_SECRETARY_EMAIL_NOTEMPLATE'), 404 );
			return false;
		}

		$extra = array('product'=>$this->item->id);
		$info = array('created_by'=>$this->item->created_by);
		
		$html  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text,$extra,$info);
		$header  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->header,$extra,$info);
		$footer  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->footer,$extra,$info);
		
		$config = array('title'=>$this->item->title , 'dpi'=>$this->defaultTemplate->dpi,'format'=>$this->defaultTemplate->format,'header'=>$header,'footer'=>$footer,'margins'=>$this->defaultTemplate->margins);
		
		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($html,$this->defaultTemplate->css, $config);
		
	    return true;
	}	
	
}
 