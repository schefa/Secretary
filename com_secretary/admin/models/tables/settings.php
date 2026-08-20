<?php

defined('_JEXEC') or die;

class SecretaryTableSettings extends \Joomla\CMS\Table\Table
{

	/**
	 * Class constructor
	 *
	 * @param mixed $db
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__secretary_settings', 'id', $db);
	}

	public function getAssetPId()
	{
		return self::_getAssetParentId();
	}

	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\Table\Table::_getAssetParentId()
	 */
	protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = null, $id = null)
	{
		$asset = self::getInstance('Asset');
		$asset->loadByName('com_secretary');
		
        return $asset->id;
	}
}
