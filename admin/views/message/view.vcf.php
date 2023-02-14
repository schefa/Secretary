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

class SecretaryViewMessage extends JViewLegacy
{
	
	protected $state;
	protected $item;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		// Get model data.
		$item = $this->get('Item');
		
		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
			JError::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$card_name = ($item->firstname ? ' ' . $item->firstname : '') .  $item->lastname ;
		JFactory::getDocument()->setMimeEncoding('text/directory', true);
		\Secretary\Joomla::getApplication()->setHeader('Content-disposition', 'attachment; filename="' . $card_name . '.vcf"', true);

		$vcard = array();
		$vcard[] .= 'BEGIN:VCARD';
		$vcard[] .= 'VERSION:3.0';
		$vcard[]  = 'N:' . Secretary\Utilities::cleaner($item->lastname,true) . ';' . Secretary\Utilities::cleaner($item->firstname,true) ;
		$vcard[]  = 'FN:' . $item->name;
		$vcard[]  = 'TITLE:' . $item->category_title;
		$vcard[]  = 'TEL;TYPE=WORK,VOICE:' . $item->phone;
		$vcard[]  = 'ADR;TYPE=WORK:;;' . Secretary\Utilities::cleaner($item->street,true) . ';' . $item->zip . ';' . Secretary\Utilities::cleaner($item->location,true) . ';' . Secretary\Utilities::cleaner($item->country,true);
		$vcard[]  = 'LABEL;TYPE=WORK:' . Secretary\Utilities::cleaner($item->street,true) . "\n" . $item->zip . "\n" . Secretary\Utilities::cleaner($item->location,true) . "\n" . Secretary\Utilities::cleaner($item->country,true);
		$vcard[]  = 'EMAIL;TYPE=PREF,INTERNET:' . $item->email;
		/*$vcard[]  = 'URL:' . $item->webpage;*/
		$vcard[]  = 'REV:' . date('c', time()) . 'Z';
		$vcard[]  = 'END:VCARD';

		echo implode("\n", $vcard);
	}
}
