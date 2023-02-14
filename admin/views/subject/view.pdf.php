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
 
class SecretaryViewSubject extends JViewLegacy
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
		$section                  = 'subject';
		$this->item	              = $this->get('Item');
		$this->defaultTemplate    = \Secretary\Helpers\Templates::getTemplate($this->item->template);

		// Permission 
		$check	= \Secretary\Helpers\Access::edit($section, $this->item->id, $this->item->created_by );
		if( $this->_layout == 'edit' && !$check ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		} elseif( false === \Secretary\Helpers\Access::show($section, $this->item->id,  $this->item->created_by) ) {
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));
			return false;
		} elseif(empty($this->defaultTemplate)) {
			throw new Exception( JText::_('COM_SECRETARY_EMAIL_NOTEMPLATE'), 404 );
			return false;
		}
        
		// Prepare
		$extra = array('subject'=>$this->item->id);
		$info = array('created_by'=>$this->item->created_by);
		
		$html  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text,$extra,$info);
		$header  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->header,$extra,$info);
		$footer  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->footer,$extra,$info);
		
		$config = array('title'=>$this->item->firstname.' '.$this->item->lastname, 'dpi'=>$this->defaultTemplate->dpi,'format'=>$this->defaultTemplate->format,'header'=>$header,'footer'=>$footer,'margins'=>$this->defaultTemplate->margins);
		
		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($html,$this->defaultTemplate->css, $config);
		
	    return true;
	}	
	
}
 