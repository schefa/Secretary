<?php
/**
 * @package     Secretary
 * @copyright   Copyright (C) 2014-2026 Fjodor Schaefer. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Secretary;

defined('_JEXEC') or die;

class HTML
{

	protected static $functions = array();
	public static function register($key, $function)
	{
		if (is_callable($function))
		{
			self::$functions[$key] = $function;
			
            return true;
		}
		
        return false;
	}

	public static function _($key)
	{
		if (array_key_exists($key, self::$functions))
		{
			return self::call(self::$functions[$key], func_get_args());
		}

		$parts = explode('.', $key);
		$file = $parts[0];
		$function = isset($parts[1]) ? $parts[1] : null;

		// Get Class
		$className = "Secretary\HTML\\" . ucfirst($file);
		
        if (!class_exists($className))
		{
			$path = SECRETARY_ADMIN_PATH . '/application/html/' . strtolower($file) . '.php';

			require_once $path;

			if (!class_exists($className))
			{
				throw new \Exception(sprintf('Class %s not found.', $className), 500);
			}
		}

		$callFunction = array($className, $function);
		
        if (is_callable($callFunction))
		{
			self::register($key, $callFunction);
			
            return self::call($callFunction, func_get_args());
		}
        else
		{
			throw new \InvalidArgumentException(sprintf('Function %s::%s not found.', $className, $function), 500);
		}
	}

	protected static function call($function, $args)
	{
		if (!is_callable($function))
		{
			throw new \InvalidArgumentException('Function not supported ' . $function, 500);
		}

		array_shift($args); // Delete function name

		$params = array();
		
        foreach ($args as &$arg)
		{
			$params[] = & $arg;
		}

		// Calls the function with additional parameter
		return call_user_func_array($function, $params);
	}
}