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
 
class SecretaryViewDocument extends JViewLegacy
{
	protected $item;
	protected $document_title;
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
		$section	= 'document';
		$this->item	= $this->get('Item'); 

		// Permission 
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id, $this->item->created_by );
		
		$show = false;
		if( $this->_layout == "edit" && true === $check) {
		    $show = true;
		} elseif($this->_layout != "edit") {
		    $subjectUserId = Secretary\Database::getQuery('subjects', $this->item->subjectid,'id', 'created_by','loadResult');
		    if(false !== \Secretary\Helpers\Access::show($section,$this->item->id,$this->item->created_by))
		        $show = true;
		        if(false !== \Secretary\Helpers\Access::show($section,$this->item->id,$subjectUserId))
		            $show = true;
		}
		
		if( !$show) {
		    throw new Exception( JText::_('JERROR_ALERTNOAUTHOR'),500); return false;
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
		    throw new Exception( implode("\n", $errors), 404); return false;
		}
		
		
		$this->emailTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);
		$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template);
		
		if(empty($this->defaultTemplate)) {
			throw new Exception( JText::_('COM_SECRETARY_EMAIL_NOTEMPLATE'), 404 );
			return false;
		}

		$html  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text,array('subject'=>$this->item->subjectid), $this->item->templateInfoFields );
		$header  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->header,array('subject'=>$this->item->subjectid), $this->item->templateInfoFields );
		$footer  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->footer,array('subject'=>$this->item->subjectid), $this->item->templateInfoFields );
		
		$config = array('title'=>$this->item->document_title .'_'. $this->item->createdEntry,'dpi'=>$this->defaultTemplate->dpi,'format'=>$this->defaultTemplate->format,'header'=>$header,'footer'=>$footer,'margins'=>$this->defaultTemplate->margins);
		
			
		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($html,$this->defaultTemplate->css, $config);
		
	    return true;
	    /*
	    header('Content-type: application/pdf');
	    header('Content-Disposition: inline; filename="' .$this->item->template . $this->item->createdEntry.'.pdf"');
	    header('Content-Transfer-Encoding: binary');
	    header('Accept-Ranges: bytes');
		*/
	}	
	
}
 