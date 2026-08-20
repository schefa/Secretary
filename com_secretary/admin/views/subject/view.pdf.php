<?php
 
defined('_JEXEC') or die;

 
class SecretaryViewSubject extends \Joomla\CMS\MVC\View\HtmlView
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
		
        if ( $this->_layout == 'edit' && !$check )
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}
        elseif ( false === \Secretary\Helpers\Access::show($section, $this->item->id,  $this->item->created_by) )
		{
			throw new \RuntimeException(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}
        elseif (empty($this->defaultTemplate))
		{
			throw new Exception( \Joomla\CMS\Language\Text::_('COM_SECRETARY_EMAIL_NOTEMPLATE'), 404 );
			
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
 