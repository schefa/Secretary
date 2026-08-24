<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class SecretaryTableTemplate extends \Joomla\CMS\Table\Table
{

    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__secretary_templates', 'id', $db);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
    {
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.template.' . $array['id']))
		{
            $actions = \Joomla\CMS\Access\Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_secretary/access.xml', "/access/section[@name='template']/");
            $default_actions = \Joomla\CMS\Access\Access::getAssetRules('com_secretary.template.' . $array['id'])->getData();
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
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::check()
     */
    public function check()
    {
        //If there is an ordering column and this is a new row then get the next ordering value
        if (property_exists($this, 'ordering') && $this->id == 0)
		{
            $this->ordering = self::getNextOrder();
        }

        return parent::check();
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetName()
     */
    protected function _getAssetName()
    {
        $k = $this->_tbl_key;
        
        return 'com_secretary.template.' . (int) $this->$k;
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
    protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = null, $id = null)
    {
        $asset = self::getInstance('Asset');
        $asset->loadByName('com_secretary.template');
        
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
            \Secretary\Helpers\Activity::set('templates', 'deleted', $this->catid, $pk);
        }
        
        return $result;
    }
}
