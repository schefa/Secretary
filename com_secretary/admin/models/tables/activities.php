<?php

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
