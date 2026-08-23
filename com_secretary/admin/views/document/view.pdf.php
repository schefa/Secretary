<?php
 
defined('_JEXEC') or die;

 
class SecretaryViewDocument extends \Joomla\CMS\MVC\View\HtmlView
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
		$this->item	= $this->get('Item');

		// Permission
		if (!\Secretary\Helpers\Access::documentExportAllowed($this->item, $this->_layout))
		{
		    throw new Exception( \Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'),500);
		}
		
		// Check for errors.
		if (count(($errors = $this->get('Errors')) ?? []))
		{
		    throw new Exception( implode("\n", $errors), 404);
		}
		
		
		$this->emailTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->message['template']);
		$this->defaultTemplate		= \Secretary\Helpers\Templates::getTemplate($this->item->template);
		
		if (empty($this->defaultTemplate))
		{
			throw new Exception( \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_NOTEMPLATE'), 404 );
		}

		$html  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->text,array('subject'=>$this->item->subjectid), $this->item->templateInfoFields );
		$header  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->header,array('subject'=>$this->item->subjectid), $this->item->templateInfoFields );
		$footer  = \Secretary\Helpers\Templates::transformText($this->defaultTemplate->footer,array('subject'=>$this->item->subjectid), $this->item->templateInfoFields );
		
		$config = array('title'=>$this->item->document_title .'_'. $this->item->createdEntry,'dpi'=>$this->defaultTemplate->dpi,'format'=>$this->defaultTemplate->format,'header'=>$header,'footer'=>$footer,'margins'=>$this->defaultTemplate->margins);
		

		$pdf = Secretary\PDF::getInstance();
		$pdf->execute($html,$this->defaultTemplate->css, $config);

		\Secretary\Joomla::getApplication()->close();
	}
	
}
 