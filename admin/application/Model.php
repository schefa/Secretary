<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

namespace Secretary;

// No direct access
defined('_JEXEC') or die; 

use JFactory;

class Model {
    
    /**
     * Creates a single model
     * 
     * @param string $name name of the model
     * @param string $prefix
     * @param array $config
     * @return unknown
     */
    public static function create($name,$prefix = 'SecretaryModel',array $config = array('ignore_request' => true))
    {
        $path = JPATH_COMPONENT_ADMINISTRATOR.'/models/'.strtolower($name).'.php';
        require_once $path;
        
        $modelClass = $prefix . ucfirst($name);
        $model = new $modelClass($config);
        
        if ($model)
        {
            $app = JFactory::getApplication();
            
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