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
 