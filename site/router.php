<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 * 
 */

defined('_JEXEC') or die;

/*
#TODO
class SecretaryRouter extends JComponentRouterBase
{
	public function build(&$query)
	{
		$segments = array();

		// Get a menu item based on Itemid or currently active
		$app = \Secretary\Joomla::getApplication();
		$menu = $app->getMenu();
		$params = JComponentHelper::getParams('com_secretary');

		// We need a menu item.  Either the one specified in the query, or the current active one if none specified
		if (empty($query['Itemid']))
		{
			$menuItem = $menu->getActive();
		}
		else
		{
			$menuItem = $menu->getItem($query['Itemid']);
		}

		$mView = (empty($menuItem->query['view'])) ? null : $menuItem->query['view'];
		$mId = (empty($menuItem->query['id'])) ? null : $menuItem->query['id'];

        if (isset($query['view'])) {
            $segments[]  = $query['view'];
            unset($query['view']);
        }
		
        if (isset($query['task'])) {
            $segments[]  = $query['task'];
            unset($query['task']);
        }

        if(isset( $query['id'] )) {
            $segments[] = $query['id'];
            unset( $query['id'] ); 
        }
		
        if(isset( $query['tid'] )) {
            $segments[] = $query['tid'];
            unset( $query['tid'] ); 
        }
		
        if(isset( $query['cid'] )) {
            $segments[] = $query['cid'];
            unset( $query['cid'] ); 
        }
		
        if(isset( $query['extension'] )) {
            $segments[] = $query['extension'];
            unset( $query['extension'] ); 
        }
		
        if(isset( $query['layout'] )) {
            $segments[] = $query['layout'];
            unset( $query['layout'] ); 
        }
		
        unset( $query['lang'] );
		
		return $segments;
	}

	public function parse(&$segments)
	{
	
        $vars = array();
		
		if($segments[0] = "message.submit") {
			$vars['view'] = 'message';
			$vars['task'] = 'submit';
			
			if(isset($segments[1]))
				$vars['id'] = $segments[1] ;
					
			if(isset($segments[2]))
				$vars['tid'] = $segments[2] ;
					
			if(isset($segments[3]))
				$vars['cid'] = $segments[3] ;
					
	var_dump($vars);
			return $vars;
		}
		
        $vars['view'] =  $segments[0] ;

        $app  = \Secretary\Joomla::getApplication();
        $menu = $app->getMenu();
        $item = $menu->getActive();
        
        $count = count($segments);
        switch( $segments[0] )
        {
            default :
			
				if(isset($segments[1]))
					$vars['id'] = $segments[1] ;
				
				if(isset($segments[2]))
               		$vars['extension'] = $segments[2] ;
				
				if(isset($segments[3]))
                	$vars['layout'] = $segments[3] ;
					
                break;
			
		}
		
		return $vars;
	}
}
	*/