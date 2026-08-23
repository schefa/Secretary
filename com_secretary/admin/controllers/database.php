<?php

defined('_JEXEC') or die;


class SecretaryControllerDatabase extends \Joomla\CMS\MVC\Controller\FormController
{
	protected $app;
	static $whiteTasks = array('fixassets', 'submit');
	static $whiteExports = array('sql', 'csv', 'xml', 'excel', 'json');

	/**
	 * Class constructor
	 * 
	 * @param array $config 
	 */
	public function __construct($config = array())
	{
		$this->app = \Secretary\Joomla::getApplication();
		
        if (!in_array($this->app->input->getCmd('task'), self::$whiteTasks))
		{
			die();
		}

		if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary'))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}
		parent::__construct($config);
	}

	/**
	 * Method to fix assets table
	 * 
	 * @return boolean
	 */
	public function fixassets()
	{
		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));

		$msg = \Joomla\CMS\Language\Text::_("COM_SECRETARY_NOTHING_CHANGED");
		$model = $this->getModel('Database', 'SecretaryModel');

		$assets_fix = $this->app->input->getCmd('assets_fix');
		$assets_clear = $this->app->input->getCmd('assets_clear');

		if (isset($assets_fix))
		{
			$errorReport = $model->assetsErrorMissingParent();
			
            if ($errorReport['status'] > 0)
			{
				$stackMsg = array();
				$no_parents = $errorReport['no_parent'];
				
                foreach ($no_parents as $child)
				{
					$result = $model->assetsFix((int) $child->id);
					
                    if ($result !== true)
					{
						$stackMsg[] = $result;
                    }
                }
				
                if (!empty($stackMsg))
				{
                    $msg = \Joomla\CMS\Language\Text::_('COM_SECRETARY_ASSETS_ERRORS_FIX_MANUALY');
                    $msg .= implode('<br>', $stackMsg);
                }
                else
				{
                            $msg = \Joomla\CMS\Language\Text::sprintf("COM_SECRETARY_THIS_SUCCESSFUL", \Joomla\CMS\Language\Text::_("COM_SECRETARY_ASSETS_ERRORS_FIX"));
                }
			}
		}
        elseif (isset($assets_clear))
		{
			$return = $model->clearAssetsTable();
			
            if ($return)
			{
				$msg = \Joomla\CMS\Language\Text::sprintf("COM_SECRETARY_THIS_SUCCESSFUL", \Joomla\CMS\Language\Text::_("COM_SECRETARY_ASSETS_ERRORS_FIX"));
            }
		}
		$this->setRedirect('index.php?option=com_secretary&view=database', $msg);
		
        return true;
	}

	/**
	 * Method to export
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function submit()
	{
		// Check for request forgeries.
		\Joomla\CMS\Session\Session::checkToken() or jexit(\Joomla\CMS\Language\Text::_('JINVALID_TOKEN'));
		$msg = "";

		// Initialise variables.
		$app = \Secretary\Joomla::getApplication();
		$user = \Secretary\Joomla::getUser();
		$model = $this->getModel('Database', 'SecretaryModel');

		if (!$user->authorise('core.admin', 'com_secretary'))
		{
			throw new Exception(\Joomla\CMS\Language\Text::_('JERROR_ALERTNOAUTHOR'), 500);
			
            return false;
		}

		$importClicked = $app->input->getCmd('import');
		$export_sql = $app->input->getCmd('export_sql');
		$export_csv = $app->input->getCmd('export_csv');
		$export_xlm = $app->input->getCmd('export_xlm');
		$export_json = $app->input->getCmd('export_json');
		$export_excel = $app->input->getCmd('export_excel');

		if (isset($importClicked))
		{
			// Attempt to save the data.
			$return = $model->import();
			
            if ($return == 0)
			{
				// Save the data in the session.
				$this->setMessage(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_THIS_FAILED_BECAUSE', \Joomla\CMS\Language\Text::_('COM_SECRETARY_IMPORT'), $model->getError()), 'warning');
				$this->setRedirect('index.php?option=com_secretary&view=database');
				
                return false;
			}
			$msg = \Joomla\CMS\Language\Text::sprintf("COM_SECRETARY_THIS_SUCCESSFUL", \Joomla\CMS\Language\Text::_("COM_SECRETARY_IMPORT"));
		}
        elseif (isset($export_sql) || isset($export_csv) || isset($export_xlm) || isset($export_excel) || isset($export_json))
		{
			$formatType = false;
			
            if (isset($export_sql))
			{
				$formatType = 'sql';
            }
            elseif (isset($export_csv))
			{
                $formatType = 'csv';
            }
            elseif (isset($export_xlm))
			{
                        $formatType = 'xml';
            }
            elseif (isset($export_excel))
			{
                        $formatType = 'excel';
            }
            elseif (isset($export_json))
			{
                        $formatType = 'json';
            }

			// Attempt to save the data.
			$return = $model->export($formatType);
			
            if ($return === false)
			{
                        // Save the data in the session.
                        $this->setMessage(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_THIS_FAILED_BECAUSE', \Joomla\CMS\Language\Text::_('COM_SECRETARY_EXPORT'), $model->getError()), 'warning');
                        $this->setRedirect('index.php?option=com_secretary&view=database');
				
                return false;
            }
			$msg = \Joomla\CMS\Language\Text::sprintf("COM_SECRETARY_THIS_SUCCESSFUL", \Joomla\CMS\Language\Text::_("COM_SECRETARY_EXPORT"));
		}

		// Redirect to the list screen.
		$app->setUserState('com_secretary.edit.database.data', null);
		$this->setRedirect('index.php?option=com_secretary&view=database', $msg);
		
        return true;
	}
}