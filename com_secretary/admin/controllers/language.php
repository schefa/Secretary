<?php
/** @author      Fjodor Schaefer - https://www.schefa.com
 */

defined('_JEXEC') or die;


class SecretaryControllerLanguage extends \Joomla\CMS\MVC\Controller\FormController
{

	protected $app;

	public function __construct($config = array())
	{
		if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
		{
			die;
		}

		$this->app = \Secretary\Joomla::getApplication();
		parent::__construct($config);
	}

	public function save($key = null, $urlVar = null)
	{
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$data = $this->app->input->get('jform', '', 'array');
		$filter_language = $this->app->input->getVar('filter_language');

		$model = $this->getModel('language');
		$model->save($data);

		$this->setRedirect('index.php?option=com_secretary&view=language&filter_language=' . $filter_language, \Joomla\CMS\Language\Text::_('COM_SECRETARY_TRANSLATIONS_SAVED'));
	}

	public function share()
	{
		$lang = \Joomla\CMS\Factory::getLanguage();

		$filter_language = $this->app->getUserStateFromRequest('com_secretary.filter_language', 'filter_language', $lang->getName(), 'string');
		$data = $this->app->input->get('jform', '', 'array');

		$model = $this->getModel('language');
		$content = $model->makeFile($data, true);

		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=" . $filter_language . ".com_secretary.ini");

		print $content;

		$this->app->close();
	}

	public function cancel($key = null)
	{
		$this->setRedirect('index.php?option=com_secretary&view=dashboard');
	}
}