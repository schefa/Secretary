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

jimport('joomla.application.component.controlleradmin');

class SecretaryControllerMarkets extends JControllerAdmin
{
    
    protected $app;
    protected $catid;
    protected $view;
    protected $redirect_url;
    
	public function __construct() {
	    $this->app		= \Secretary\Joomla::getApplication();
	    $this->catid	= $this->app->input->getInt('catid',0);
		$this->view		= 'markets';
		$this->redirect_url = 'index.php?option=com_secretary&amp;view='.$this->view.'&amp;catid='. $this->catid;
		parent::__construct();
	}

	public function getModel($name = 'Market', $prefix = 'SecretaryModel', $config = array('ignore_request' => true))
	{
	    $model = parent::getModel($name, $prefix, $config);
	    return $model;
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId);
		$append .= '&catid=' . $this->catid;
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		$append .= '&catid=' . $this->catid;
		return $append;
	}
	
	public function applyColumns()
	{
		$stockcolumns	= $this->app->input->get('chk_group', array(), 'array');
		$this->app->setUserState('filter.stockcolumns', $stockcolumns);
		$this->setRedirect(JRoute::_($this->redirect_url, false));
		return true;
	}
	
	public function updateStock()
	{ 
	    $data  = $this->app->input->post->getString('data');

	    $canDo	= \Secretary\Helpers\Access::getActions('market');
	    if(!empty($data) && $canDo->get('core.edit')) {
    	        
    	    parse_str($data , $output); 
    	   
    	    $new = array();
    	    $new['id'] = key($output['detail']);
    	    $new['quantity'] = $output['detail'][ $new['id'] ]['quantity'];
    	    $new['ek_price'] = $output['detail'][ $new['id'] ]['ek_price'];
    	    if($output['detail'][ $new['id'] ]['catid'] > 0)
    	    $new['catid'] = $output['detail'][ $new['id'] ]['catid'];
    	    
            $model = $this->getModel('Market','SecretaryModel');
            $result = $model->save($new);
    
            echo $result;
            
	    }
	    $this->app->close();
	}
	
	public function addStock()
	{ 

	    $canDo	= \Secretary\Helpers\Access::getActions('market');
	    if($canDo->get('core.create')) {
	        $data	= $this->app->input->post->getString('data');
    	    $json = json_decode($data,true);
    	    if(!is_array($json) or !isset($json['symbol']))
    	        $this->app->close();
    		
    		$model = $this->getModel('Market','SecretaryModel');
    		$result = $model->addStock($json);
    		
    		echo $result;
	    }
	    $this->app->close();
	}

	public function getStockData()
	{
	    $model = $this->getModel('Markets','SecretaryModel');
	    $result = $model->getChartData();
	    $this->app->close();
	}

	public function searchStock()
	{ 
	    $term	= $this->app->input->getString('term');
	    
	    $model = $this->getModel('Market','SecretaryModel');
	    $result = $model->searchStock($term);
	    
	    echo $result;
	    
	    $this->app->close();
	}
	
}