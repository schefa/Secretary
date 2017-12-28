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

jimport('joomla.application.component.controller');
jimport('joomla.application.component.controllerform');

class SecretaryControllerAjax extends JControllerForm
{
	
	protected $app;
	protected $input;
	protected static $_secretaryTables = array('documents','templates');
	
    /**
     * List of tasks 
     */
	protected static $allowedTasks = array(
	    'buchen',
	    'checkDocumentNumber',
	    'getCurrencySymbol',
	    'getField',
	    'load',
	    'projectTimer',
	    'saveProgress',
	    'search',
	    'searchSubjectLocation',
	    'toggleSidebar',
	    'toggleTaxRateColumn',
	    'update',
	    'updatePermission'
	);
	
	/**
	 * Constructor
	 */
	public function __construct( $config = array() ) {
	    
	    $this->app   = \Secretary\Joomla::getApplication();
	    $this->input = $this->app->input;
	    $task	     = $this->input->getVar('task');
		
		if(!in_array($task, self::$allowedTasks)) {
		    echo 'Error task - ' . $task;
			die;
		}
		parent::__construct( $config );
	}
	
	/**
	 * Performs an database query
	 * 
	 * @throws Exception
	 * @return boolean
	 */
	public function load()
	{
		$user         = \Secretary\Joomla::getUser(); 
		$table        = $this->input->getVar('table');
		$id           = $this->input->getInt('id');
		$document_id  = $this->input->getInt('document_id');
		
		if(!in_array($table,self::$_secretaryTables)) {
			throw new Exception ('Query failure: '. $table);
			return false;
		}
		
		$canDo	= \Secretary\Helpers\Access::getActions($table);
		if($id > 0 && $canDo->get('core.show')) {
			$data = Secretary\Database::getQuery($table,$id);
			
			if($table === 'templates' && ($document_id > 0 && $user->authorise('core.show', 'com_secretary.document.'.$document_id))) {
				$document = $this->getModel('Document')->getItem($document_id);
				$data->title = \Secretary\Helpers\Templates::transformText( $data->title, array('subject'=>$document->subjectid ), $document->templateInfoFields );
				$data->text = \Secretary\Helpers\Templates::transformText( $data->text, array('subject'=>$document->subjectid ), $document->templateInfoFields ); 
			}
			
			if(!empty($data)) {
				echo json_encode($data);
			} else {
			    echo '';
			}
		}
		$this->app->close();
	}
	
	public function buchen()
	{ 
		$itemID = $this->input->getInt('id');
		$data	= $this->input->post->get('jform','','RAW');
		
		if(!isset($itemID)) {
			echo 'Error: No Document';
			$this->app->close();
		} else {
			$buchung = Secretary\Database::getQuery('accounting',$itemID,'entry_id');	
			if(!empty($buchung->id)) {
				echo JText::sprintf('COM_SECRETARY_ACCOUNTS_SAVE_FAILED_RECORD_EXISTS', $buchung->id);
				$this->app->close();
			}
		}
		
		$canDo	= \Secretary\Helpers\Access::getActions('accounting');
		if(!empty($data) && !empty($data['accounting']) && $canDo->get('core.edit')) {
			$accounting_id = \Secretary\Helpers\Accounts::prepareAccounting($itemID, $data['currency'], $data['accounting'], $data['total'], $data['title']);
			if($accounting_id > 0) {
				$this->_update('documents', array( ('accounting_id').'='.(int) $accounting_id),array( ('id').'='.(int) $itemID));
				echo json_encode(array(200,JText::sprintf('COM_SECRETARY_ACCOUNTS_SAVE_RECORD_SUCCESS', $accounting_id)));
			} else {
				echo json_encode(array(404, $accounting_id)); // error message
			}
			$this->app->close();
		}
		echo json_encode(array(500,JText::_('COM_SECRETARY_SAVE_FAILED')));
		$this->app->close();
	}
	
	public function update()
	{
		
		$table	= $this->input->getVar('table');
		$itemID = $this->input->getInt('id');
		$data	= $this->input->post->get('jform','','RAW');
		$values = $data['fields'];
		
		if(!isset($table) || !isset($itemID) || !isset($values)) {
			echo 'Error: No data';
			$this->app->close();
		}
		
		$section = Secretary\Application::getSingularSection( $table );
		$canDo	= \Secretary\Helpers\Access::getActions($section);
		
		if(!in_array($table,self::$_secretaryTables)) {
			echo ('Query failure: '. $table);
			$this->app->close();
		}
		
		if(!empty($data) && $canDo->get('core.edit')) {
				
			$oldFields = Secretary\Database::getQuery($table,$itemID,'id','fields','loadResult');
			
			if($oldFields = json_decode($oldFields, true)) {
				$values = array_merge($oldFields, $values);
			}
			
			$values = \Secretary\Helpers\Items::rebuildFieldsForDocument($values);
			
			// Update Progress
			$db		= \Secretary\Database::getDBO();
			$fields = array( $db->qn('fields').'='. $db->quote( $values ) );
			$conditions = array( $db->qn('id').'='.((int) $itemID ));
			$result = $this->_update( $table, $fields, $conditions );
			
	
			if($result) {
				echo JText::_('COM_SECRETARY_SAVE_SUCCESS');
			} else {	
				echo JText::_('COM_SECRETARY_SAVE_FAILED');
			} 
			
		}
		$this->app->close();
	}
	
	/**
	 * Save project progress
	 */
	public function saveProgress()
	{ 
		$itemID       = $this->app->input->getInt('id');
		$value        = $this->app->input->get('value','','float');
		$extension    = $this->app->input->getCmd('extension');
		$canDo        = \Secretary\Helpers\Access::getActions('time');

		if($canDo->get('core.edit') && isset($extension) && isset($itemID) && isset($value)) {
			// Update Progress
			$db		= \Secretary\Database::getDBO();
			$fields = array( $db->qn('progress').'='.((float) $value ));
			$conditions = array( $db->qn('id').'='.((int) $itemID ));
			$result = $this->_update( $extension, $fields, $conditions );
			if($result) {
				echo JText::_('COM_SECRETARY_SAVE_SUCCESS');
			} else {	
				echo JText::_('COM_SECRETARY_SAVE_FAILED');
			}
		}
		$this->app->close();
	}
	
	public function projectTimer()
	{ 
		$extension        = 'tasks';
		$user             = JFactory::getUser();
		$userContactId    = Secretary\Database::getQuery('subjects',$user->id,'created_by','id','loadResult');
		$action           = $this->app->input->getVar('action');
		$itemID           = $this->app->input->getInt('itemID');
		$projectID        = $this->app->input->getInt('pid');
		
		if($user->guest)
		    return false;
		
		if(isset($action) && isset($itemID) && isset($projectID)) {
			if($action == 'stop')
			{
				// Calculate Time Between last Start and Now
				$projectTimer	= \Secretary\Helpers\Times::getProjectStatus($itemID, $projectID);
				$worktime = time() - strtotime($projectTimer->created);
				
				// Get Ready to Update the Project Task
				$thatProjectTask = Secretary\Database::getQuery('tasks',$itemID,'id','contacts,totaltime');
				$teamMembers = json_decode($thatProjectTask->contacts, true);
				
				// Holen des Users aus der Kontakttabelle
				/*$userContact = Secretary\Database::getQuery('subjects', $user->id);
				if(!empty($userContact)) $userContactId = $userContact->id;
				*/
				// Update Team Members Worktime
				$new = array();
				foreach($teamMembers as $idx => $member) {
					foreach($member as $key => $val) {
						$new[$idx][$key] = $val;
						if( $new[$idx]['id'] == $userContactId) {
							if( $key == 'time' ) {
								$new[$idx][$key] = $val + $worktime;
							} else {
								$new[$idx]['time'] = $worktime;
							}
						}
					}
				}
				
				$teamMembers = json_encode($new, JSON_NUMERIC_CHECK);
				$totaltime = $worktime + $thatProjectTask->totaltime;
				
				// Update Total Time
				$db		= \Secretary\Database::getDBO();
				$fields = array( $db->qn('totaltime').'='.intval( $totaltime ), $db->qn('contacts').'='.$db->quote($teamMembers));
				$conditions = array( $db->qn('id').'='.intval( $itemID ), $db->qn('projectID').'='. intval($projectID));
				$result = $this->_update( $extension, $fields, $conditions );
				if($result) {
					echo \Secretary\Helpers\Times::secondsToWorktime($totaltime);
				} else {
					echo JText::_('COM_SECRETARY_FAILURE');	
				}
			}
			\Secretary\Helpers\Activity::set($extension, $action, $projectID, $itemID, $user->id );
		}
		$this->app->close();
	}
	
	/**
	 * Get currency symbol for a name
	 */
	public function getCurrencySymbol()
	{ 
		$term	= $this->app->input->getVar('term');
		$symbol = Secretary\Database::getQuery('currencies', Secretary\Utilities::cleaner($term),'currency','symbol','loadResult');
		echo $symbol;
		$this->app->close();
	}
	
	public function getField()
	{
		$extension    = $this->app->input->post->getVar('extension');
		$standard     = $this->app->input->post->getString('standard');
		if(isset($standard)) {
			$default	= urldecode($standard);
			$default	= Secretary\Utilities::cleaner($default,true);
		} else {
			$default	= '';
		}
		$id			= $this->app->input->post->getInt('id');
		if(!empty($id) && !empty($extension)) {
			$ret = \Secretary\Helpers\Items::getField($id,$extension,$default); 
			if(!empty($ret)) { echo json_encode($ret); }
		}
		$this->app->close();
	}
	
	/**
	 * Universal search method
	 */
	public function search()
	{
	    $section   = $this->input->getCmd('section');
	    $term      = $this->input->getString('term');
	    $return    = '';
	    
	    
	    if(in_array($section,['accounts','document_title','documents','locations','products','subjects'])) {
	        switch ($section) {
	            case 'accounts':
	                $return = \Secretary\Helpers\Accounts::getAccounts($term);
	                break;
	            case 'document_title':
	                $return = \Secretary\Helpers\Documents::searchTitle($term);  
	                break;
	            case 'documents':
	                $return = \Secretary\Helpers\Documents::search($term);
                    break;
	            case 'locations':
	                $extension    = $this->input->getCmd('extension');
	                if(!empty($term) && !empty($extension)) {
	                    $return = \Secretary\Helpers\Locations::search($term, $extension);
	                }
	                break;
	            case 'products':
	                $pUsage	= $this->input->getInt('u');
	                $return = \Secretary\Helpers\Products::search($term,$pUsage);
	                break;
	            case 'subjects':
	                $source	= $this->input->getVar('source');
	                $id     = $this->input->getInt('id');
	                if(!empty($term))
	                {
	                    $return = \Secretary\Helpers\Subjects::getSubjects($term, $source);
	                }
	                elseif(is_numeric($id) && $id > 0)
	                {
	                    $model = $this->getModel('Subject');
	                    $subject = $model->getItem($id);
	                    $return = json_encode($subject);
	                }
	                break;
	        }
	        echo $return;
	    }
	    $this->app->close();
	}
			
	/**
	 * AJAX search for ZIP/location in the contacts table
	 */
	public function searchSubjectLocation()
	{ 
		$term	= $this->input->getString('term');
		$type   = $this->input->getCmd('type');
		if(!empty($term) && in_array($type,array('zip','location'))) {
		    $ret = \Secretary\Helpers\Subjects::searchLocations($term,$type);
			if(!empty($ret)) { echo $ret; }
		}
		$this->app->close();
	}
	
	/**
	 * Test on runtime if document number is available 
	 */
	public function checkDocumentNumber()
	{
		$nr		= $this->input->getInt('nr');
		$catid	= $this->input->getInt('catid');
		$id		= $this->input->getInt('id');
				
		if(!empty($nr) && !empty($catid)) {
					
			$usedNr = \Secretary\Helpers\Documents::getDoubleCategoryNumber($nr,(int) $catid, $id);
			if (!empty($usedNr)) {
				sort($usedNr);
				$up		= $nr + 5;
				$down	= ($nr - 5 > 0) ? ($nr - 5) : ( $nr + 1);
				$range = range($down, $up);
				echo '<div class="alert alert-warning number-in-use"><span class="tiny-text">'.JText::_('COM_SECRETARY_ERROR_DOUBLE_NO').'</span><ul>';
				
				for($i = 0; $i < count($usedNr); $i++) {
					echo '<li>';
					if($range[$i] != $usedNr[$i]) {
						echo '<span class="no-is-free">'. JText::sprintf('COM_SECRETARY_THIS_IS_FREE', $range[$i]) . '</span>';
						unset($range[$i]);
						$range = array_values($range);
					} else {
						echo '<span class="no-is-used">'. $usedNr[$i] . '</span>';
					}
					echo '</li>';
				}
				echo '</ul></div>';
			}
					
		}
					
		$this->app->close(); 
	}
	
	/**
	 * Hide or show sidebar menu and save current setting in the user session
	 */
	public function toggleSidebar()
	{
		$value  = $this->input->getInt('v');
		$this->app->setUserState('filter.toggleSidebar', $value);
		$this->app->close();
	}
	
	/**
	 * Hide or show the tax rate column in document view and save current setting in the user session
	 */
	public function toggleTaxRateColumn()
	{
		$value  = $this->input->getInt('v',1);
		$this->app->setUserState('filter.toggleTaxRateColumn', $value);
		$this->app->close();
	}
	
	/**
	 * Method to update permission and assets table 
	 */
	public function updatePermission()
	{ 
	    $user	= JFactory::getUser();
	    if($user->authorise('core.admin','com_secretary'))
	    {
	        $db = \Secretary\Database::getDBO();
	        
	        $rules     = array();
	        $input     = $this->app->input->post;
	        $section   = $input->getVar('section');
	        $action    = $input->getString('action');
	        $group     = $input->getVar('group');
	        $value     = $input->getInt('value');
	        
	        // Create com_secretary Asset if empty
            $db->setQuery('SELECT id FROM #__assets WHERE name LIKE "com_secretary"');
            $parentAssetId = $db->loadResult();
            if(empty($parentAssetId)) {
                $parentAsset  = new stdClass();
                $parentAsset->name  = 'com_secretary';
                $parentAsset->title = JText::_('COM_SECRETARY_COMPONENT');
                $parentAsset->parent_id = 1;
                $parentAsset->level = 1;
                $parentAsset->rules = '{}';
                $result = $db->insertObject('#__assets', $parentAsset);
            }
	        
	        $plural = $section;
	        if($section == 'component') {
	            $assetName = 'com_secretary';
	        } else {
	            $assetName = 'com_secretary.'. $section;
	        }
	        
	        // Get Asset if exists
	        $asset  = JTable::getInstance('Asset');
	        $asset->loadByName($assetName);
	        
	        $asset_id          = $asset->id;
	        $asset->name       = $assetName;
	        $asset->title      = JText::_('COM_SECRETARY_'.strtoupper($plural));
	           
	        // Get current rules
	        if(!empty($asset->rules)) {
                $rules = (array) json_decode( $asset->rules, true );
                foreach($rules as $rule => $gr_array) { $rules[$rule] = (array) $gr_array; }
	        }
	        
	        // Set action if not exists
            if(!isset($rules[$action])) {
                $rules[$action] = array();
            }
            
            unset($rules[$action][''.$group]); 
             
            // Save Value only if yes or no, 1 or 0
            if($value < 2)
                $rules[$action][$group] = $value;
             
	        $asset->rules = json_encode($rules);
	        $asset->store();
	        
	        // There's a problem with parent_id and level
	        if( !($asset_id > 0) ) {
	            \Secretary\Helpers\Access::setParentIdAssets($assetName,$section);
	        }
	        
	        // Set Rules for secretary_settings
	        \Secretary\Helpers\Access::updateSecretaryRules();
	    }
	    
	    $this->app->close();
	}
	
	private function _update($section , $fields, $conditions )
	{
	    $db    = \Secretary\Database::getDBO();
		$query = $db->getQuery(true);
		$query->update($db->quoteName('#__secretary_'.$db->escape($section)));
		$query->set($fields);
		$query->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
		return $result;
	}
	
}