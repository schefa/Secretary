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

class SecretaryViewItem extends JViewLegacy
{
    
    /**
     * Method to display the View
     *
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\View\HtmlView::display()
     */
	public function display($tpl = 'pdf')
	{
		
	    $app	= Secretary\Joomla::getApplication();
		$id		= $app->input->getInt('id');
		$catid	= $app->input->getInt('catid');
		$docId	= $app->input->getInt('document');
		$download	= $app->input->getInt('download');
		
		// Dokumenttyp setzen
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		
		if($id > 0) {
			
		    $item = Secretary\Database::getQuery('uploads', intval($id) ,'id','business,title,folder');
		    $file = SECRETARY_ADMIN_PATH.'/uploads/'.$item->business .'/'.$item->folder .'/'.$item->title;
			$filename = $item->title;
			
		} elseif($docId > 0) {
				
			$kontotitle	= Secretary\Email::getCategoryTitle($catid);
			
			$business	= Secretary\Application::company();
			$path		= SECRETARY_ADMIN_PATH.'/uploads/'.$business['id'].'/emails/';
			$filename	= $kontotitle.'-'.$docId.'.pdf';
			$file		= $path . $filename;
			
		}
		
		if($id || $docId) {
			
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			@readfile($file);
			
		}
		
		// Download
		if($download === 1)
			JResponse::setHeader('Content-Disposition','attachment;filename="'. $filename .'"');
		
	}	
}
 