<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class SecretaryViewLanguage extends \Joomla\CMS\MVC\View\HtmlView
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
		
		$lang               = \Joomla\CMS\Factory::getLanguage();
		
		$this->state		= $this->get('State');
		$language			= $this->state->get('filter_language');
		$this->language		= (!empty($language)) ? $language: $lang->getTag(); 
		
		$this->translation	= $model->getTranslation($this->language);
		$languages			= $model->getSiteLanguages();
		
		$options   = array(); 
		
        foreach ($languages as $language)
		{
			$options[] = \Joomla\CMS\HTML\HTMLHelper::_('select.option', $language, $language);
		}
		$this->lists['filter_language'] = \Joomla\CMS\HTML\HTMLHelper::_('select.genericlist', $options, 'filter_language', ' onchange="submit();" ', 'value', 'text', $this->language);
		 
		parent::display($tpl);
	} 
	
}
