<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

defined('_JEXEC') or die;

class Model
{

    /**
     * Creates a single model
     * 
     * @param string $name name of the model
     * @param string $prefix
     * @param array $config 
     */
    public static function create($name, $prefix = 'SecretaryModel', array $config = array('ignore_request' => true))
    {
        $path = SECRETARY_ADMIN_PATH . '/models/' . strtolower($name) . '.php';
        require_once $path;

        $modelClass = $prefix . ucfirst($name);
        $model = new $modelClass($config);

        if ($model)
		{
            $app = \Secretary\Joomla::getApplication();

            $model->setState('task', $app->input->getCmd('task'));
            $menu = $app->getMenu();

            if (is_object($menu))
			{
                if ($item = $menu->getActive())
				{
                    $params = $menu->getParams($item->id);
                    $model->setState('parameters.menu', $params);
                }
            }
        }

        return $model;
    }
}