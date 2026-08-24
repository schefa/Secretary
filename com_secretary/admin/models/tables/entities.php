<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class SecretaryTableEntities extends \Joomla\CMS\Table\Table
{

    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__secretary_entities', 'id', $db);
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
            \Secretary\Helpers\Activity::set('entities', 'deleted', 0, $pk);
        }
        
        return $result;
    }
}
