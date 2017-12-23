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

class SecretaryViewLanguage extends JViewLegacy
{
	protected $translation;
	protected $lists;
	protected $language;
	protected $state;
	
	/**
	 * Method to display the View
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\View\HtmlView::display()
	 */
	public function display($tpl = null)
	{
		$model = $this->getModel();
		
		$lang               = JFactory::getLanguage();
		
		$this->state		= $this->get('State');
		$language			= $this->state->get('filter_language');
		$this->language		= (!empty($language)) ? $language: $lang->getTag(); 
		
		$this->translation	= $model->getTranslation($this->language);
		$languages			= $model->getSiteLanguages();
		
		$options   = array(); 
		foreach ($languages as $language)
		{
			$options[] = JHtml::_('select.option', $language, $language);
		}
		$this->lists['filter_language'] = JHtml::_('select.genericlist', $options, 'filter_language', ' onchange="submit();" ', 'value', 'text', $this->language);
		 
		parent::display($tpl);
	} 
	
}
