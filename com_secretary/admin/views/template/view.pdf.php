<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryViewTemplate extends \Joomla\CMS\MVC\View\HtmlView
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
		
		$this->kontotitle	= \Joomla\CMS\Language\Text::_($this->item->title);
		
        if (empty($this->kontotitle))
		{
			$this->kontotitle = \Joomla\CMS\Language\Text::_('COM_SECRETARY_DOCUMENT') ;
		}
		
		$html = \Secretary\Helpers\Templates::transformText($this->item->text);
		$config = array('title'=>$this->kontotitle, 'dpi'=>$this->item->dpi,'format'=>$this->item->format,'header'=>$this->item->header,'footer'=>$this->item->footer,'margins'=>$this->item->margins);
		
		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($html,$this->item->css, $config);
		
		return true; 	
	}	
}
 