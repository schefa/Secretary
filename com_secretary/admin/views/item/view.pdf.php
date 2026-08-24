<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryViewItem extends \Joomla\CMS\MVC\View\HtmlView
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
		$document = \Joomla\CMS\Factory::getDocument();
		$document->setMimeEncoding('application/pdf');
		
		if ($id > 0)
		{
		    $item = Secretary\Database::getQuery('uploads', intval($id) ,'id','business,title,folder');
		    $file = SECRETARY_ADMIN_PATH.'/uploads/'.$item->business .'/'.$item->folder .'/'.$item->title;
			$filename = $item->title;
			
		}
        elseif ($docId > 0)
		{
			$kontotitle	= Secretary\Email::getCategoryTitle($catid);
			
			$business	= Secretary\Application::company();
			$path		= SECRETARY_ADMIN_PATH.'/uploads/'.$business['id'].'/emails/';
			$filename	= $kontotitle.'-'.$docId.'.pdf';
			$file		= $path . $filename;
			
		}
		
		if ($id || $docId)
		{
			header('Content-Disposition: inline; filename="' . $filename . '"');
			header('Content-Transfer-Encoding: binary');
			header('Accept-Ranges: bytes');
			@readfile($file);
			
		}
		
		// Download
		if ($download === 1)
		{
			\Joomla\CMS\Factory::getApplication()->setHeader('Content-Disposition','attachment;filename="'. $filename .'"');
		}
		
	}	
}
 