<?php
/**
 * @version     3.2.0
 * @package     com_secretary
 *
 * @author       Fjodor Schaefer (schefa.com)
 * @copyright    Copyright (C) 2015-2017 Fjodor Schaefer. All rights reserved.
 * @license      MIT License
 */
 
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class SecretaryModelMessages extends JModelList
{
	protected $app;
	
	/**
	 * Class constructor
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'subject', 'a.subject',
				'state', 'a.state',
				'created', 'a.created', 
				'created_by_alias', 'a.created_by_alias', 
				'contact_to', 'a.contact_to', 
				'contact_to_alias', 'a.contact_to_alias', 
				'title', 'c.title',
				'sum', 'contact_to_name',
				'priority', 'a.priority',
			);
		}

        $this->app      = \Secretary\Joomla::getApplication();
		$this->catid    = $this->app->input->getInt('catid');
		
		parent::__construct($config);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\ListModel::populateState()
	 */
	protected function populateState($ordering = null, $direction = null)
	{ 
	    parent::populateState('sum', 'DESC');
	    
	    $limit = $this->getUserStateFromRequest($this->context . '.list.limit', 'limit', $this->app->get('list_limit'), 'int');
	    $this->setState('list.limit', $limit);
	}
	
	/**
	 * {@inheritDoc}
	 * @see \Joomla\CMS\MVC\Model\ListModel::getListQuery()
	 */
	protected function getListQuery()
	{ 
      	$db               = \Secretary\Database::getDBO();
		$query 	          = $db->getQuery(true);
		$orderAdditional  = '';
		$contactToStr     = (Secretary\Database::getDbType() == 'postgresql') ? 'CAST (a.contact_to AS INTEGER)': 'a.contact_to';
		
		// Gruppieren nach Email, Kategorie, an-Wen
		if(Secretary\Database::getDbType() != 'postgresql') {
			$query->select('a.id,a.subject,a.catid,a.refer_to,a.contact_to,
					a.contact_to_alias,a.created_by, a.created_by_alias,a.state,
					max(a.created) as created,COUNT(*) AS sum')
					->from($db->qn('#__secretary_messages','a'));
					
					// Contact TO
			$query->select("CASE WHEN contact_to > 0
							THEN CONCAT_WS(' ',s.firstname,s.lastname)
							ELSE contact_to_alias END AS contact_to_alias ");
			$query->leftJoin($db->qn('#__secretary_subjects','s').' ON s.id = '.$contactToStr);
							
			$query->select('c.title');
			$query->leftJoin($db->qn('#__secretary_folders','c').' ON c.id = a.catid');
			
			$query->group('a.refer_to,a.catid');
							
			// Filter by published state.
			$state = $this->getState('filter.state');
			if (is_numeric($state)) {
				$query->having('a.state = ' . intval($state));
			}
			
			// Filter by a single or group of folders.
			if ($this->catid > 0 && is_numeric($this->catid))
			{
				// Unterkategorien holen
				$subCategories = \Secretary\Helpers\Folders::subCategories($this->catid);
				array_push($subCategories, 0);
				if(is_array($subCategories)) $subCategories = implode("','", $subCategories);
				$query->having("a.catid IN ('" . $subCategories . "')");
			}
			
			$orderAdditional = ', a.created DESC';
		} else  { 
			
			$query->select('a.refer_to,a.catid,COUNT(*) AS sum');
			
			// $query->select(',a.id,a.subject,a.contact_to,
			//		a.contact_to_alias,a.created_by, a.created_by_alias,a.state,
			//		max(a.created) as created,COUNT(*) AS sum');
			
			$query->from($db->qn('#__secretary_messages','a'));
					
			// Contact TO
			/*
			$query->select("CASE WHEN contact_to > 0
					THEN CONCAT_WS(' ',s.firstname,s.lastname)
					ELSE contact_to_alias END AS contact_to_alias ");
			$query->leftJoin($db->qn('#__secretary_subjects','s').' ON s.id = a.contact_to');
			
			$query->select('c.title');
			$query->leftJoin($db->qn('#__secretary_folders','c').' ON c.id = a.catid');
				
				*/	
			
			$query->group('a.refer_to,a.catid');
					
			// Filter by published state.
			$state = $this->getState('filter.state');
			if (is_numeric($state)) {
				$query->having('a.state = ' . intval($state));
			}
			
			// Filter by a single or group of folders.
			if ($this->catid > 0 && is_numeric($this->catid))
			{
				// Unterkategorien holen
				$subCategories = \Secretary\Helpers\Folders::subCategories($this->catid);
				array_push($subCategories, 0);
				if(is_array($subCategories)) $subCategories = implode("','", $subCategories);
				$query->having("a.catid IN ('" . $subCategories . "')");
			}
		}
	
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn) . $orderAdditional);
		}
		
		return $query;
	}

    public function getTalks()
	{
		$db		= \Secretary\Database::getDBO();
		$query	= $db->getQuery(true);
		$user	= \Secretary\Joomla::getUser();
		
		$createdbyToStr = (Secretary\Database::getDbType() == 'postgresql') ? 'CAST (a.created_by AS INTEGER)': 'a.created_by';
		$fieldStr = (Secretary\Database::getDbType() == 'postgresql') ? " (s.firstname || ' ' || s.lastname) " : 'CONCAT(s.firstname," ",s.lastname)';
		
		
		$query->select($this->getState('list.select',$db->qn(array('a.id','a.catid','a.refer_to','a.state','a.subject','a.message','a.created','a.created_by','a.created_by_alias'))));
		$query->from($db->qn('#__secretary_messages','a'));

		$query->select("c.title AS category");
		$query->leftJoin($db->qn('#__secretary_folders','c')." ON c.id = a.catid");

		$query->select($fieldStr." AS created_by_user");
		$query->leftJoin($db->qn('#__secretary_subjects','s')." ON s.id = ".$createdbyToStr); 

		$query->select("status.title AS statustitle");
		$query->leftJoin($db->qn('#__secretary_status','status').' ON status.id = a.state');
				
		$id	= $this->app->input->getInt('rid');
		if(!empty($id)) {
			$query->where('('.$db->qn('a.id').'='.$db->quote(intval($id)).')OR('.$db->qn('a.refer_to').'='.$db->quote(intval($id)).')');
		}
		
		// Gruppierung nach Ansprechpartner
		$contactTo	= $this->app->input->getInt('contact_to');
		if(!empty($contactTo)) {
			$query->where('(('.$db->qn('a.contact_to').'='.$db->quote(intval($contactTo)).')
						OR('.$db->qn('a.contact_to').'='.$db->quote(intval($user->id)) .'))');
		}
		
		// Gruppierung nach Email
		$created_by_alias	= $this->app->input->getString('cba');
		if(!empty($created_by_alias)) {
			$query->where($db->qn('a.created_by_alias').'='. $db->quote($created_by_alias));
		}
		
		// Filter by a single or group of folders.
		if ($this->catid > 0 && is_numeric($this->catid))
		{
			// Unterkategorien holen
			$subCategories = \Secretary\Helpers\Folders::subCategories($this->catid);
			if(is_array($subCategories)) $subCategories = implode(",", $subCategories);
			$query->where($db->qn('a.catid').'IN('.$subCategories.')');
		}
		
		$query->order('a.created ASC');
		
		$db->setQuery($query);
		$results = $db->loadObjectList();
    	return $results;
    }
	
	public function getRecentTalks()
	{ 
	    $results = array();
      	$db		= \Secretary\Database::getDBO();
		$query 	= $db->getQuery(true);
		
		$contactToStr = (Secretary\Database::getDbType() == 'postgresql') ? 'CAST (a.contact_to AS INTEGER)': 'a.contact_to';
		
		$query->select( 'a.id,a.subject,a.catid,a.contact_to,a.created_by,a.created_by_alias,a.created,a.refer_to' );
		$query->from($db->qn('#__secretary_messages','a'));
			
			// Contact TO
		$query->select("CASE WHEN contact_to > 0
						THEN CONCAT_WS(' ',firstname,lastname)
						ELSE contact_to_alias END AS contact_to_alias ");
		$query->leftJoin($db->qn('#__secretary_subjects','s') .' ON s.id = '. $contactToStr);
			
			// Category
		$query->select('c.title');
		$query->leftJoin($db->qn('#__secretary_folders','c') .' ON c.id = a.catid');
			
			/*
			// Gruppieren nach Email, Kategorie, an-Wen
		if(Secretary\Database::getDbType() != 'postgresql')
			$query->group('a.contact_to_alias,a.catid,a.contact_to');
		*/
		
		// Filter by published state.
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.state = ' . intval($state));
		}
		
		$query->order('a.created');
		
		$db->setQuery($query);
		$items = $db->loadObjectList();
		
		if(!empty($items)) {
		    $user = \Secretary\Joomla::getUser();
		    $userContact = Secretary\Database::getQuery('subjects',(int) $user->id,'created_by','id','loadResult');
		    $userContactId = (isset($userContact)) ? (int) $userContact : -1;
            		    
		    foreach($items as $result) {
		        
		        if($result->refer_to < 1)
		            continue;
	            
	            $mitem = Secretary\Database::getQuery('messages',(int) $result->refer_to, 'id');
	            if(empty($mitem)) continue;
	            
	            // Permission message
	            $canSee = false; $canChange = false; $canEdit = false;
	            
		        // Own Post
		        if(!empty($mitem) && ($userContactId == $mitem->created_by)) {
		            $canSee = true;
		            if($user->authorise('core.edit.own', 'com_secretary.message') ||  $user->authorise('core.edit', 'com_secretary.message')){
		                $canEdit = true; $canChange	= true;
		            }
		        } elseif($user->id == $mitem->created_by) {
		            $canSee = true; $canEdit = false; $canChange = false;
		        }
		        
		        if(!$canSee && ((int) $mitem->contact_to === $userContactId)) { $canSee = true; }
		        elseif(!$canSee) { $canSee = $user->authorise('core.show','com_secretary.message.'.$mitem->id)
		        || $user->authorise('core.show.other','com_secretary.message'); }
		        
		        if( !$canSee ) continue;
		        $results[] = $result;
		    }
		}
		
		
		return $results;
	}

	public function getCorrespondence()
	{ 
      	$db		= \Secretary\Database::getDBO();
      	$query 	= $db->getQuery(true);
      	$contactToStr = (Secretary\Database::getDbType() == 'postgresql') ? 'CAST (a.contact_to AS INTEGER)': 'a.contact_to';
		
		$query->select( 'null as sum,a.id,a.subject,a.catid,a.contact_to,a.created_by,a.created_by_alias,a.created' );
		$query->from($db->qn('#__secretary_messages','a'));
			
			// Contact TO
		$query->select("	CASE WHEN contact_to > 0
						THEN CONCAT_WS(' ',firstname,lastname)
						ELSE contact_to_alias END AS contact_to_alias ");
		$query->leftJoin($db->qn('#__secretary_subjects','s') .' ON s.id = '. $contactToStr);
			
		// Category
		$query->select('c.title');
		$query->leftJoin($db->qn('#__secretary_folders','c') .' ON c.id = a.catid');
			
		$query->where(' (( a.contact_to_alias = '.$db->quote("").') OR (a.contact_to_alias IS NULL))');
		$query->where('contact_to = 0');
		
		// Filter by published state.
		$state = $this->getState('filter.state');
		if (is_numeric($state)) {
			$query->where('a.state = ' . intval($state));
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		if ($orderCol && $orderDirn) {
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		$query->order('a.created');
		
		$db->setQuery($query);
		$results = $db->loadObjectList();
    	return $results;
	}
}
