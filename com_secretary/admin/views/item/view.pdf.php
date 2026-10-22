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
		
		$file = false;

		if ($id > 0)
		{
			$db = Secretary\Database::getDBO();
		    $item = Secretary\Database::getQuery('uploads', intval($id), 'id', $db->qn(array('extension', 'itemID', 'business', 'title', 'folder')));

			if (!empty($item->title) && \Secretary\Helpers\Uploads::canShow($item))
			{
				$file = \Secretary\Helpers\Uploads::safeUploadPath($item->business . '/' . $item->folder . '/' . $item->title);
				$filename = $item->title;
			}
		}
        elseif ($docId > 0)
		{
			// E-mailed PDFs are named after the document's creation time, not its id,
			// so only the section-wide permission can be checked.
			if (Secretary\Joomla::getUser()->authorise('core.show', 'com_secretary.document'))
			{
				$kontotitle	= Secretary\Email::getCategoryTitle($catid);
				$business	= Secretary\Application::company();
				$filename	= $kontotitle . '-' . $docId . '.pdf';
				$file		= \Secretary\Helpers\Uploads::safeUploadPath($business['id'] . '/emails/' . $filename);
			}
		}

		if (($id || $docId) && $file === false)
		{
			throw new \Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 403);
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
 