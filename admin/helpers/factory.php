<?php
/**
 * @version     3.0.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die; 

// Get Secretary Framework
require_once  JPATH_ADMINISTRATOR .'/components/com_secretary/application/Secretary.php'; 

/**
 * Class is deprecated and will be deleted
 * 
 * @deprecated
 *
 */
abstract class SecretaryFactory
{
       
	/**
	 * @deprecated
	 */
	public static function getBusiness() {
	    return Secretary\Application::company();
	}
	
	/**
	 * Secretary\Database::getQuery($table, $id, $fieldcheck, $select, $output)
	 * 
	 * @deprecated
	 */
	public static function getData($table, $id, $fieldcheck='id', $select = '*', $output = 'loadObject')
	{
	    return Secretary\Database::getQuery($table, $id, $fieldcheck, $select, $output);
	} 
	
}
