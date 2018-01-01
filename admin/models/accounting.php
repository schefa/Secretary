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

jimport('joomla.application.component.modeladmin');

class SecretaryModelAccounting extends JModelAdmin
{
	
	protected $business = array();
	protected $extension = '';
	protected $text_prefix = 'com_secretary';

    public function __construct($config = array())
	{
	    $app 				= \Secretary\Joomla::getApplication();
		$this->extension	= $app->input->getCmd('extension','accounting');
		$this->business		= Secretary\Application::company();
        parent::__construct($config);
    }
	
	public function getTable($type = 'Accounting', $prefix = 'SecretaryTable', $config = array())
	{
		$type = $this->extension;
		return JTable::getInstance($type, $prefix, $config);
	}
	
	protected function canDelete($record)
	{
		return \Secretary\Helpers\Access::canDelete($record,'accounting');
	}
	
	public function getForm($data = array(), $loadData = true)
	{
	    $app	= \Secretary\Joomla::getApplication();
		$form	= $this->loadForm('com_secretary.'.$this->extension,$this->extension, array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) return false;
		return $form;
	}
	
	private static $_item;
	public function getItem($pk = null)
	{
		if(empty(self::$_item[$pk]) && ($item = parent::getItem($pk)))
		{
				
		    $data	= \Secretary\Joomla::getApplication()->getUserState('com_secretary.edit.'.$this->extension.'.data', array());
			
			if($this->extension == 'accounting') {
					
				if(!empty($data['accounting']) && is_array($data['accounting'])) {
					foreach($data['accounting'] as $k => $entries) {
						unset($data['accounting'][$k]);
						$item->accounting[$k] = $this->sanitizeAccPosition($entries);
					}
				} elseif(!empty($item->soll) && !empty($item->haben)) {
					$solls = $this->sanitizeAccPosition($item->soll);
					$habens = $this->sanitizeAccPosition($item->haben);
					$item->accounting = array('s'=>$solls,'h'=>$habens);
				}
				if(isset($item->accounting))
					$item->accounting = json_encode($item->accounting, JSON_NUMERIC_CHECK);
				
			}
			
			self::$_item[$pk] = $item;
		}
		
		return self::$_item[$pk];
		
	}
	
	protected function sanitizeAccPosition($value)
	{
		$array = (is_array($value)) ? array_values($value) : json_decode($value,true);
		if(!empty($array)) {
		foreach($array as $i => $row) {
			$row = array_values($row);
			$account = Secretary\Database::getQuery('accounts_system', (int) $row[0],'id','nr,title');
			$array[$i] = array($account->nr.' '.$account->title,$row[0],$row[1]);
		} }
		return $array;
	}
	
	protected function loadFormData()
	{
		$business	= \Secretary\Application::company();
		$app		= \Secretary\Joomla::getApplication();
		$data		= $app->getUserState('com_secretary.edit.'.$this->extension.'.data', array());
		
		if (empty($data)) {
			$data = $this->getItem();
			
			switch ($this->extension) {
				
				case 'accounting' :
							
					if(empty($data->taxRate))
						$data->taxRate = $business['taxvalue'];
					
						$data->total = Secretary\Utilities\Number::getNumberFormat($data->total) ;
							
					$catid	= $app->input->getInt('catid');
					if(empty($data->catid) && !empty($catid)) {
						$data->catid = $catid;
						$data->category = Secretary\Database::getQuery('folders', $data->catid );
					}
					
					break;
					
			}
			
		}
	
		return $data;
	}
	
	public function save($data)
	{
		
		// Initialise variables;
	    $app	= \Secretary\Joomla::getApplication();
	    $user	= \Secretary\Joomla::getUser();
		$table	= $this->getTable();
		$key	= $table->getKeyName();
		$pk		= (!empty($data[$key])) ? $data[$key] : (int)$this->getState($this->getName().'.id');
		
		// Access
		if(!(\Secretary\Helpers\Access::checkAdmin())) {
			if ( !$user->authorise('core.create', 'com_secretary.accounting') || ($pk > 0 && !$user->authorise('core.edit.own', 'com_secretary.accounting.'.$pk) ) )
			{
				throw new Exception(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
				return false;
			}
		}
		
		// Allow an exception to be thrown.
		try
		{
			// Load existing record.
			if ($pk > 0) $table->load($pk);

			switch ($this->extension) {
				
				case 'accounting' :
					$data['created'] = date('Y-m-d H:i:s');
					$data['created_by'] = $user->id;
					$data['business'] = (!empty($table->business)) ? $table->business : $this->business['id'];
					
					/** **********************/
							
					$soll = array(); $soll_sum = 0;
					$haben = array(); $haben_sum = 0;
					
					foreach($data['accounting']['s'] as $val) {
						$soll[] = array( $val['id'], $val['sum'] ); $soll_sum += $val['sum']; }
					
					foreach($data['accounting']['h'] as $val) {
						$haben[] = array( $val['id'], $val['sum'] ); $haben_sum += $val['sum']; }
					
					$data['soll'] = json_encode($soll, JSON_NUMERIC_CHECK);
					$data['haben'] = json_encode($haben, JSON_NUMERIC_CHECK);
					
					if((empty($soll) || empty($haben)) || $soll_sum != $haben_sum) {
						$errTitle = JText::_('COM_SECRETARY_TOTAL');
						$this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
						return false;
					} else {
						$data['total'] = $soll_sum;
					}
					
					break;
					
				case 'accounts' :
					$data['business'] = $this->business['id'];
					$data['fields']	= \Secretary\Helpers\Items::saveFields($data['fields']);
					
					if($data['kid'] < 1) {
						$errTitle = JText::_('COM_SECRETARY_ACCOUNTS_SYSTEM_PID');
						$this->setError(JText::sprintf('COM_SECRETARY_ERROR_CHECK_THIS', $errTitle));
						return false;
					}
					
					break;
					
			}
			
			// Bind
			if (!$table->bind($data)) {
				$this->setError($table->getError()); return false;
			}
			
			// Store
			if (!$table->store()) {
				$this->setError($table->getError()); return false;
			}
			
			// Update after storage
			if($this->extension == 'accounts_system') {
				if (!$table->rebuildLevel($table->id, $table->parent_id, $table->level )) {
					$this->setError($table->getError()); return false; }
							
				if (!$table->reorderSystem()) {
					$this->setError($table->getError()); return false; }
			} elseif($this->extension == 'accounting') {
				$newID = (int) $table->id;
				\Secretary\Helpers\Uploads::upload( 'accounting', 'accountings', $data['upload_title'], $newID );
			}
				
			// Aktivität
			$activityAction = ($pk > 0) ? 'edited' : 'created';
			\Secretary\Helpers\Activity::set('accounting', $activityAction, 0, $table->id );

		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$pkName = $table->getKeyName();

		if (isset($table->$pkName)) {
			$this->setState($this->getName().'.id', $table->$pkName);
		}

		$this->cleanCache();
		return true;
		
	}
	
	public function batch($commands, $pks, $contexts)
	{
		\Secretary\Helpers\Batch::batch( 'accountings', $commands, $pks, $contexts);
		$this->cleanCache();
		return true;
	}
	
	
}