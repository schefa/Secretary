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
		
		$parts		= explode('.', $key);
		$file		= $parts[0];
		$function	= isset($parts[1]) ? $parts[1] : null;
		
		// Get Class
		$className =  "Secretary\HTML\\" .ucfirst($file);
		if (!class_exists($className))
		{
			$path = JPATH_ROOT . '/administrator/components/com_secretary/application/html/'. strtolower($file) . '.php';
			
			if ($path)
			{
				require_once $path;
				
				if (!class_exists($className))
				{
					throw new \Exception(sprintf('Class %s not found.', $className), 500);
				}
			}
			else
			{
				throw new \Exception(sprintf('%s not found.', $file), 500);
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
			throw new \InvalidArgumentException('Function not supported '. $function, 500);
		}
		
		array_shift($args); // Delete function name
		
		$params = array();
		foreach ($args as &$arg)
			$params[] = &$arg;
		
		// Calls the function with additional parameter
		return call_user_func_array($function, $params);
	}
	
	/**
	 * Modal dialog
	 * 
	 * @param string $selector
	 * @return string
	 */
	public static function modal( $selector = 'a.open-modal')
	{
		$html = array();
		$html[] = '<div class="secretary-modal secretary-modal-outer" style="display:none;"><div class="secretary-modal-inner"></div></div>';
		$html[] = "<script>
            (function($){
				
				var body = document.body,
					html = document.documentElement;
	
				var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
				
				$('.secretary-modal').height(height);
				
                $('.secretary-modal').on('click','.modal-close',function() {
					$(this).parents('.secretary-modal-outer:first').hide();
					$(this).parents('.secretary-modal-inner:first').empty();
                });
				
                $('".$selector."').click(function() { 
                    var url = $(this).data('url');
                    $('.secretary-modal-outer .secretary-modal-inner').addClass(' loading-gif');
                    $('.secretary-modal-outer').show();
                    $.ajax({
                        url : url,
                    }).done(function(data) {
                        $('.secretary-modal-outer .secretary-modal-inner').html(data);
                        $('.secretary-modal-outer .secretary-modal-inner').removeClass(' loading-gif');
                    });
                });
            })(jQuery);
		</script>";
		
		return implode('', $html);
	}
	 
}
