<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * 
 */

namespace Secretary;

// No direct access
defined('_JEXEC') or die; 


class Model {
    
    /**
     * Creates a single model
     * 
     * @param string $name name of the model
     * @param string $prefix
     * @param array $config 
     */
    public static function create($name,$prefix = 'SecretaryModel',array $config = array('ignore_request' => true))
    {
        $path = SECRETARY_ADMIN_PATH.'/models/'.strtolower($name).'.php';
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
