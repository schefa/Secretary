<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class SecretaryTableActivities extends \Joomla\CMS\Table\Table
{
    /**
     * Class constructor
     *
     * @param mixed $db
     */
    public function __construct(&$db)
    {
        parent::__construct('#__secretary_activities', 'id', $db);
    }
}
