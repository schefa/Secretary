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
 
// No direct access
defined('_JEXEC') or die;

class SecretaryController extends JControllerLegacy
{ 
    /**
     * Main entry point
     * 
     * {@inheritDoc}
     * @see \Joomla\CMS\MVC\Controller\BaseController::display()
     */
    public function display($cachable = false, $urlparams = false)
	{ 
        $raws = array('preview','modal','modaljusers','raw'); 
		
		$app	= \Secretary\Joomla::getApplication();
        $user		= \Secretary\Joomla::getUser();
		$business	= \Secretary\Application::company();
		
        $view	= $app->input->getCmd('view', 'dashboard');
        $layout	= $app->input->getCmd('layout', '');
        $task	= $app->input->getCmd('task', '');
        $format	= $app->input->getCmd('format', '');
        $app->input->set('view', $view);
        $app->input->set('layout', $layout);
        $app->input->set('task', $task);
		
		$canSee = $user->authorise('core.manage','com_secretary');
		if($app->isClient('site')) {
		    $single = Secretary\Application::getSingularSection($view);
            $canSee	= $user->authorise('core.show','com_secretary.'.$single);
            
            $parts  = explode(".",$task);
            if(in_array($view,array('dashboard')) || in_array($parts[0],array('ajax')))
                $canSee = $user->authorise('core.show','com_secretary.business');
            if(($view === 'message' && $layout === 'form') || $view === 'messages')
                $canSee = true;
		} else {
		    $canDo = \Secretary\Helpers\Access::getActions($view);
		    $canSee = boolval($canDo->get('core.show'));
		}
		
		if($view === 'dashboard')
		    $canSee = true;
		
	    if((\Secretary\Helpers\Access::checkAdmin()) || $canSee )
		{
			if( !empty($business) || ($view == 'business' && $layout == 'edit') ) {
				
				switch ($view) {
					
					case 'navbar' :  
						switch ($layout) {
						    case 'changelog' : include_once( SECRETARY_ADMIN_PATH ."/application/html/changelog.php"); break;
							case 'lastversion' : echo \Secretary\HTML::_('layout.lastversion'); break;
						}
						break;
						
					default:
					    
					    // Display raw content body without anything
						if(strpos( $task, 'ajax' ) !== false || $task == 'ajax' 
						    || ($app->input->getCmd('tmpl') == 'component') || in_array($layout, $raws) || in_array($format, $raws)) {
						    
							parent::display($cachable, $urlparams);
							
						} else {
						// Display everything
						    if($app->isClient('administrator') && !($view == 'template' && $layout == 'edit'))
								echo Secretary\Navigation::getSidebar();
							
							echo '<div class="secretary-container">';
							
							if($app->isClient('administrator')) {
							    echo \Secretary\HTML::_('layout.latestVersionMsg');
							    echo \Secretary\HTML::_('layout.topToolbar');
						    }
							parent::display($cachable, $urlparams);
							
							echo \Secretary\HTML::_('layout.footer',$app->isClient('administrator'));
							echo '</div>';
						}
						break;
				}
				
			} else {
				echo \Secretary\HTML::_('business.startBusiness');
			}
		} else {
		    echo '<div class="alert alert-danger">'.JText::_('JERROR_ALERTNOAUTHOR').'</div>';
		    return false;
		}
		
        return $this;
    }
    
}
