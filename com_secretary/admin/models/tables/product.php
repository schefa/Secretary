<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class SecretaryTableProduct extends \Joomla\CMS\Table\Table
{

    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__secretary_products', 'id', $db);
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::bind()
     */
    public function bind($array, $ignore = '')
    {
        if (!\Secretary\Joomla::getUser()->authorise('core.admin', 'com_secretary.product.' . $array['id']))
		{
            $actions = \Joomla\CMS\Access\Access::getActionsFromFile(JPATH_ADMINISTRATOR . '/components/com_secretary/access.xml', "/access/section[@name='product']/");
            $default_actions = \Joomla\CMS\Access\Access::getAssetRules('com_secretary.product.' . $array['id'])->getData();
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

        $data['created_by']    = (!empty($this->created_by)) ? $this->created_by : \Secretary\Joomla::getUser()->id;

        // Data Fields
        $data['fields']    = (isset($data['fields'])) ? \Secretary\Helpers\Items::saveFields($data['fields']) : FALSE;

        // Beziehungen
        $features = array();
        
        if (!empty($data['features']))
		{
            foreach ($data['features'] as $idx => $feature)
			{
                foreach ($feature as $key => $value)
				{
                    $features[(int) $idx][$key] = Secretary\Utilities::cleaner($value);
                }
            }
        }
        $data['contacts'] = (!empty($features)) ? json_encode($features) : '';
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::check()
     */
    public function check()
    {
        if (strlen($this->title) < 1)
		{
            $errTitle = \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_TITLE');
            $this->setError(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
            
            return false;
        }

        if ($this->quantityMax < $this->quantityMin)
		{
            $w = \Joomla\CMS\Language\Text::_('COM_SECRETARY_PRODUCT_QUANTITYMAX') . ': ' . round($this->quantityMax, 2) . ' < ' . round($this->quantityMin, 2);
            $this->setError(\Joomla\CMS\Language\Text::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $w));
            
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
        
        return 'com_secretary.product.' . (int) $this->$k;
    }

    /**
     * {@inheritDoc}
     * @see \Joomla\CMS\Table\Table::_getAssetParentId()
     */
    protected function _getAssetParentId(\Joomla\CMS\Table\Table $table = null, $id = null)
    {
        $asset = self::getInstance('Asset');
        $asset->loadByName('com_secretary.product');
        
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
            \Secretary\Helpers\Activity::set('products', 'deleted', $this->catid, $pk);
        }
        
        return $result;
    }
}
