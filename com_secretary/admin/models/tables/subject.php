<?php

defined('_JEXEC') or die;

class SecretaryTableSubject extends \Joomla\CMS\Table\Table
{

	/**
	 * Class constructor
	 *
	 * @param mixed $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__secretary_subjects', 'id', $db);
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::bind()
	 */
	public function bind($array, $ignore = '')
	{
		$user = \Secretary\Joomla::getUser();

		if (!$user->authorise('core.admin', 'com_secretary.subject.' . $array['id']))
		{
			$actions = \Joomla\CMS\Access\Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_secretary/access.xml', "/access/section[@name='subject']/");
			$default_actions = \Joomla\CMS\Access\Access::getAssetRules('com_secretary.subject.' . $array['id'])->getData();
			$array_jaccess = array();
			
            foreach ($actions as $action)
			{
				if (isset($default_actions[$action->name]))
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
                }
			}
			$array['rules'] = $array_jaccess;
		}


		if (isset($array['rules']) && is_array($array['rules']))
		{
			$array['rules'] = \Secretary\Helpers\Access::JAccessRulestoArray($array['rules']);
			$this->setRules($array['rules']);
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Prepares data before saving it
	 * 
	 * @param array $data
	 */
	public function prepareStore(&$data)
	{

		// The "created_by" juser picker submits a hidden field that's empty whenever no user
		// is linked; the column is NOT NULL, so an unfiltered '' fails the insert/update outright.
		$data['created_by']	= (!empty($data['created_by'])) ? (int) $data['created_by'] : (int) $this->created_by;
		$data['created']	= (isset($this->created) && ($this->created != '0000-00-00')) ? $this->created : date('Y-m-d');

		$business = Secretary\Application::company();
		$data['business'] = (isset($this->business)) ? $this->business : (int) $business['id'];

		// Data Fields
		$data['fields']	= (isset($data['fields'])) ? \Secretary\Helpers\Items::saveFields($data['fields']) : FALSE;

		// Google Maps
		$coords = (!empty($data['location'])) ? \Secretary\Helpers\Locations::getCoords($data['street'], $data['zip'], $data['location']) : array();
		$data['lat'] = (isset($coords['lat'])) ? $coords['lat'] : 0.0;
		$data['lng'] = (isset($coords['lng'])) ? $coords['lng'] : 0.0;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::check()
	 */
	public function check()
	{
		// No Contact
		if (empty($this->lastname))
		{
			$errTitle = \Joomla\CMS\Language\Text::_('COM_SECRETARY_SUBJECT_NAME');
			$this->setError(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
			
            return false;
		}
		// Wrong Email
		if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL))
		{
			$this->setError('Invalid Email');
			
            return false;
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetName()
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		
        return 'com_secretary.subject.' . (int) $this->$k;
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetParentId()
	 */
	protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = null, $id = null)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary.subject');
		
        return $asset->id;
	}

	/**
	 * Delete and save activity
	 *
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::delete()
	 */
	public function delete($pk = null)
	{
		$this->load($pk);
		$result = parent::delete($pk);
		
        if ($result)
		{
			\Secretary\Helpers\Activity::set('subjects', 'deleted', $this->catid, $pk);
		}
		
        return $result;
	}
}
