<?php

defined('_JEXEC') or die;


class SecretaryModelFolder extends \Joomla\CMS\MVC\Model\AdminModel
{
	protected $app;
	private $extension;
	private static $_item;

	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		$this->app = \Secretary\Joomla::getApplication();
		$this->extension = $this->app->input->getCmd('extension');
		parent::__construct($config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::populateState()
	 */
	protected function populateState()
	{
		$parentId = $this->app->input->getInt('parent_id');
		$this->setState('folder.parent_id', $parentId);

		// Load the User state.
		$pk = $this->app->input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);

		// Load the parameters.
		$params =  Secretary\Application::parameters();
		$this->setState('params', $params);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::canDelete()
	 */
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record, 'folder');
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::getTable()
	 */
	public function getTable($type = 'Folder', $prefix = 'SecretaryTable', $config = array())
	{
		return \Joomla\CMS\Table\Table::getInstance($type, $prefix, $config);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::getItem()
	 */
	public function getItem($pk = null)
	{
		if (empty(self::$_item[$pk]) && ($result = parent::getItem($pk)))
		{
			if (strpos($result->title ?? '', 'COM_') !== false)
			{
				$result->title = \Joomla\CMS\Language\Text::_($result->title);
			}
			
            if (strpos($result->alias ?? '', 'COM_') !== false)
			{
				$result->alias = \Joomla\CMS\Language\Text::_($result->alias);
			}
			$result->extension = (isset($result->extension)) ? $result->extension : $this->extension;

			// Prime required properties.
			if (empty($result->id))
			{
				$result->parent_id = $this->getState('folder.parent_id');
			}

			// Convert the created and modified dates to local user time for display in the form.
			$tz = new DateTimeZone($this->app->getCfg('offset'));
			
            if ((int) $result->created_time)
			{
				$date = new \Joomla\CMS\Date\Date($result->created_time);
				$date->setTimezone($tz);
				$result->created_time = $date->toSql(true);
			}
            else
			{
				$result->created_time = null;
			}

			// Get Standard Fields required for the module
			if (empty($result->id) && empty($result->fields))
			{
				$fields	= array();
				$requiredFields = \Secretary\Helpers\Items::getRequiredFields($result->extension);
				
                if (!empty($requiredFields))
				{
					foreach ($requiredFields as $v)
					{
						$fields[] =  array((int) $v->id, \Joomla\CMS\Language\Text::_($v->title), $v->standard, $v->hard);
                    }
					$result->fields = json_encode($fields);
				}
			}

			if (in_array($result->extension, array('documents', 'subjects', 'products')))
			{
				$newFields = array();
				$fields = json_decode($result->fields);
				
                if (count($fields ?? []) > 0)
				{
					foreach ($fields as $key => $value)
					{
						if ('template' === $value[3] && empty($result->template))
						{
							$result->template = $value[2];
						}
                        else
						{
							$newFields[] = $value;
						}
                    }
					
                    if (empty($result->template))
					{
						$result->template = 0;
                    }
					$result->fields = json_encode($newFields, JSON_NUMERIC_CHECK);
				}
                else
                {
                    $result->template = 0;
                }
			}

			if ($result->extension == 'documents' && $fields = json_decode($result->fields))
			{
				$newFields = array();
				
                foreach ($fields as $key => $value)
				{
					if ('pUsage' == $value[3] && empty($result->productUsage))
					{
						$result->productUsage = $value[2];
                    }
                    else
                    {
                        if ('emailtemplate' == $value[3] && empty($result->emailtemplate))
                        {
                                            $result->emailtemplate = (int) $value[2];
                        }
                        else
                        {
                                        $newFields[] = $value;
                        }
                    }
				}
				$result->fields = json_encode($newFields, JSON_NUMERIC_CHECK);
			}
			
            if (empty($result->productUsage))
			{
				$result->productUsage = 0;
			}
			
            if (empty($result->template))
			{
				$result->template = 0;
			}
			
            if (empty($result->emailtemplate))
			{
				$result->emailtemplate = 0;
			}

			$result->description	= Secretary\Utilities\Text::prepareTextarea($result->description);

			self::$_item[$pk] = $result;
		}

		return self::$_item[$pk];
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::getForm()
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_secretary.folder.' . $this->extension, 'folder', array('control' => 'jform', 'load_data' => $loadData));
		
        if (empty($form))
		{
            return false;
		}
		
        if (empty($data['extension']))
		{
			$data['extension'] = $this->extension;
		}
		
        return $form;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\FormModel::loadFormData()
	 */
	protected function loadFormData()
	{
		$result = $this->app->getUserState('com_secretary.edit.' . $this->getName() . '.data', array());
		
        if (empty($result))
		{
			$result = $this->getItem();
			
            if (!empty($result))
			{
				$result->title	= Secretary\Utilities::cleaner($result->title, true);
				$result->alias	= Secretary\Utilities::cleaner($result->alias, true);
				$result->description	= Secretary\Utilities::cleaner($result->description, true);
            }
		}
		
        return $result;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::save()
	 */
	public function save($data)
	{
		$table		= $this->getTable();
		$pk			= (!empty($data['id'])) ? $data['id'] : (int) $this->getState($this->getName() . '.id');

		// Access
		$user	= \Secretary\Joomla::getUser();
		
        if (!(\Secretary\Helpers\Access::checkAdmin()))
		{
			if (!$user->authorise('core.create', 'com_secretary.' . $data['extension']) || (!$user->authorise('core.create', 'com_secretary.folder') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.folder.' . $pk))))
			{
				throw new Exception(\Joomla\CMS\Language\Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				
                return false;
            }
		}

		try
		{
			// Load existing folder
			if ($pk > 0)
			{
				$table->load($pk);
			}

			// Prepare
			$table->prepareStore($data);

			// Bind
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				
                return false;
			}

			// Check
			if (!$table->check())
			{
				$this->setError($table->getError());
				
                return false;
			}

			// Store
			if (!$table->store())
			{
				$this->setError($table->getError());
				
                return false;
			}
		}
        catch (Exception $e)
		{
			$this->setError($e->getMessage());
			
            return false;
		}

		// Done
		$newId = $table->id;

		if (!$table->rebuildLevel($newId, $table->parent_id, $table->level))
		{
			$this->setError($table->getError());
			
            return false;
		}

		if (!$table->reorderFolder($table->business, $table->extension))
		{
			$this->setError($table->getError());
			
            return false;
		}

		// Activity
		$activityAction = ($pk > 0) ? 'edited' : 'created';
		\Secretary\Helpers\Activity::set('folders', $activityAction, 0, $newId);

		// Connection with template
		$connection = new \Secretary\Helpers\Connections('folders', $newId);
		$connection->deleteConnections(false);
		
        if (!empty($data['fields']) && $fields = json_decode($data['fields'], true))
		{
			foreach ($fields as $idx => $feature)
			{
				if ($feature[3] == 'template')
				{
					$connection->addConnection($feature[2], "template");
				}
            }
		}

		$this->setState($this->getName() . '.id', $newId);

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\BaseDatabaseModel::cleanCache()
	 */
	protected function cleanCache($group = null, $client_id = 0)
	{
		parent::cleanCache('com_secretary');
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\AdminModel::batch()
	 */
	public function batch($commands, $pks, $contexts)
	{
		$return = \Secretary\Helpers\Batch::batch('folders', $commands, $pks, $contexts);
		$this->cleanCache();
		
        return true;
	}
}
