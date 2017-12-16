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

class SecretaryViewTemplate extends JViewLegacy
{
	protected $item;
	protected $kontotitle;
	protected $emailTemplate;
	protected $defaultTemplate;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = 'pdf')
	{
		
	    $app		= Secretary\Joomla::getApplication();
		$this->item	= $this->get('Item');
		
		// Dokument Titel
		$categoryId	= $app->input->getInt('catid');
		$view		= $app->input->getVar('view');
		
		$this->kontotitle	= JText::_($this->item->title);
		if(empty($this->kontotitle)) {
			$this->kontotitle = JText::_('COM_SECRETARY_DOCUMENT') ;
		}
		
		$html = \Secretary\Helpers\Templates::transformText($this->item->text);
		$config = array('title'=>$this->kontotitle, 'dpi'=>$this->item->dpi,'format'=>$this->item->format,'header'=>$this->item->header,'footer'=>$this->item->footer,'margins'=>$this->item->margins);
		
		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($html,$this->item->css, $config);
		
		return true; 	
	}	
}
 